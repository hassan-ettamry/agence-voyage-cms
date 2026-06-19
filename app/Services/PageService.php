<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    private const CONTAINER_ROLES = [
        'layout',
        'grid',
        'grid-item',
        'card',
        'content',
        'group',
    ];

    private const CONTENT_WIDGET_TYPES = [
        'text',
        'richtext',
        'heading',
        'button',
        'image',
        'icon',
        'icon-text',
        'link',
        'video',
        'iframe',
        'gallery',
        'map',
        'contact-form',
        'faq',
        'countdown',
    ];

    private PageStructureValidator $validator;

    public function __construct(
        PageStructureValidator $validator,
        private MenuService $menuService
    )
    {
        $this->validator = $validator;
    }

    /**
     * Créer une nouvelle page
     */
    public function create(array $data, $user): Page
    {
        return DB::transaction(function () use ($data, $user) {

            $menuSelection = $data['menu_selection'] ?? 'none';
            unset($data['menu_selection']);

            $data = $this->normalizeMetaFields($data);

            // Validation structure
            $data['structure'] = $data['structure'] ?? [];

            $data['structure'] = $this->canonicalizeStructure($data['structure']);

            $this->validator->validate($data['structure']);

            // Associer la page à l'agence de l'utilisateur
            $data['agency_id'] = $user->agency_id;

            // Générer un slug unique
            $data['slug'] = $this->generateUniqueSlug(
                $data['slug'] ?? Str::slug($data['title'])
            );

            $page = Page::create($data);

            $this->menuService->syncPageSelection($page, $menuSelection);

            return $page;
        });
    }

    /**
     * Mettre à jour une page
     * + créer une version si la structure change
     */
    public function update(Page $page, array $data, $user): Page
    {
        return DB::transaction(function () use ($page, $data, $user) {

            $hasMenuSelection = array_key_exists('menu_selection', $data);
            $menuSelection = $data['menu_selection'] ?? null;
            unset($data['menu_selection']);

            $data = $this->normalizeMetaFields($data);

            if (! array_key_exists('structure', $data)) {
                $structure = $this->canonicalizeStructure($page->structure);

                if ($page->structure !== $structure) {
                    $data['structure'] = $structure;
                }
            }

            // Validation structure
            if (array_key_exists('structure', $data)) {
                $data['structure'] = $this->canonicalizeStructure(
                    $data['structure'] ?? []
                );

                $this->validator->validate($data['structure']);
            }

            // Vérifier si la structure a changé
            $structureChanged = array_key_exists('structure', $data) &&
                json_encode($data['structure']) !== json_encode(
                    $this->canonicalizeStructure($page->structure)
                );

            // Si changement → créer une version
            if ($structureChanged) {
                $this->createVersion($page, $user);
            }

            // Vérifier si le slug change → rendre unique
            if (isset($data['slug']) && $data['slug'] !== $page->slug) {
                $data['slug'] = $this->generateUniqueSlug($data['slug']);
            }

            $page->update($data);

            if ($hasMenuSelection) {
                $this->menuService->syncPageSelection($page, $menuSelection);
            } elseif (array_key_exists('title', $data)) {
                $page->menuItems()->update(['title' => $page->title]);
            }

            return $page;
        });
    }

    /**
     * Save builder structure through the page lifecycle.
     */
    public function updateStructure(Page $page, array $structure, $user): Page
    {
        return $this->update(
            $page,
            [
                'structure' => $structure,
            ],
            $user
        );
    }

    /**
     * Restaurer une version précédente
     */
    public function restore(Page $page, PageVersion $version, $user): Page
    {
        return DB::transaction(function () use ($page, $version, $user) {

            // Sauvegarder l'état actuel avant restauration
            $this->createVersion($page, $user);

            $structure = $this->canonicalizeStructure($version->structure);

            $this->validator->validate($structure);

            // Restaurer les données
            $page->update([
                'structure' => $structure,
                'meta' => $version->meta,
            ]);

            return $page;
        });
    }

    /**
     * Dupliquer une page
     */
    public function duplicate(Page $page, $user): Page
    {
        return DB::transaction(function () use ($page, $user) {

            $newPage = $page->replicate();

            // Modifier les champs pour éviter conflit
            $newPage->title = $page->title.' (copie)';
            $newPage->slug = $this->generateUniqueSlug($page->slug.'-copy');
            $newPage->status = Page::STATUS_DRAFT;
            $newPage->published_at = null;
            $newPage->agency_id = $user->agency_id;
            $newPage->structure = $this->canonicalizeStructure($page->structure);

            $newPage->save();

            return $newPage;
        });
    }

    /**
     * Publier une page
     */
    public function publish(Page $page): Page
    {
        $structure = $this->canonicalizeStructure($page->structure);

        $this->validator->validate($structure);

        $page->update([
            'structure' => $structure,
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return $page;
    }

    /**
     * Créer une version (historique)
     */
    private function createVersion(Page $page, $user): void
    {
        $lastVersion = $page->versions()->max('version') ?? 0;

        PageVersion::create([
            'page_id' => $page->id,
            'structure' => $this->canonicalizeStructure($page->structure),
            'meta' => $page->meta,
            'version' => $lastVersion + 1,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Store flat request SEO fields inside the page meta JSON column.
     */
    private function normalizeMetaFields(array $data): array
    {
        $meta = $data['meta'] ?? [];

        if (array_key_exists('meta_title', $data)) {
            $meta['title'] = $data['meta_title'];
            unset($data['meta_title']);
        }

        if (array_key_exists('meta_description', $data)) {
            $meta['description'] = $data['meta_description'];
            unset($data['meta_description']);
        }

        if ($meta !== []) {
            $data['meta'] = $meta;
        }

        return $data;
    }

    /**
     * Normalize persisted legacy root wrappers into the canonical root node list.
     */
    public function canonicalizeStructure(?array $structure): array
    {
        if (! $structure) {
            return [];
        }

        if (array_is_list($structure)) {
            return $this->canonicalizeNodeCollection($structure);
        }

        if (
            ($structure['type'] ?? null) === 'page' &&
            array_key_exists('children', $structure)
        ) {
            $children = $structure['children'];

            return is_array($children) && array_is_list($children)
                ? $this->canonicalizeNodeCollection($children)
                : [];
        }

        return [];
    }

    private function canonicalizeNodeCollection(array $nodes, ?array $parent = null): array
    {
        $canonical = [];

        foreach ($nodes as $index => $node) {
            $node = $this->canonicalizeNode($node, $parent, $index);

            if ($node !== null) {
                $canonical[] = $node;
            }
        }

        return $canonical;
    }

    private function canonicalizeNode(mixed $node, ?array $parent = null, int $siblingIndex = 0): ?array
    {
        if (! is_array($node)) {
            return null;
        }

        $node['props'] = $this->normalizeNodeProps(
            $node['props'] ?? []
        );

        $children = $node['children'] ?? [];

        $node['children'] = is_array($children) && array_is_list($children)
            ? $this->canonicalizeNodeCollection($children, $node)
            : [];

        if (($node['type'] ?? null) === 'container') {
            $node['props']['containerRole'] = $this->inferContainerRole(
                $node,
                $parent,
                $siblingIndex
            );
        }

        return $node;
    }

    private function normalizeNodeProps(mixed $props): array
    {
        if (! is_array($props)) {
            return [];
        }

        return array_is_list($props)
            ? []
            : $props;
    }

    private function inferContainerRole(array $node, ?array $parent, int $siblingIndex): string
    {
        $props = $this->normalizeNodeProps($node['props'] ?? []);
        $existing = $props['containerRole'] ?? null;

        if (is_string($existing) && in_array($existing, self::CONTAINER_ROLES, true)) {
            return $existing;
        }

        $display = $props['display'] ?? 'block';
        $parentProps = $this->normalizeNodeProps($parent['props'] ?? []);
        $parentDisplay = $parentProps['display'] ?? 'block';

        if ($display === 'grid') {
            return 'grid';
        }

        if (($parent['type'] ?? null) === 'container' && $parentDisplay === 'grid') {
            return 'grid-item';
        }

        if (($parent['type'] ?? null) === 'section' && $siblingIndex === 0) {
            return 'layout';
        }

        if ($this->hasCardSurface($props)) {
            return 'card';
        }

        if ($this->hasContentChildren($node)) {
            return 'content';
        }

        return 'group';
    }

    private function hasCardSurface(array $props): bool
    {
        $backgroundColor = $props['backgroundColor'] ?? null;

        if (
            is_string($backgroundColor)
            && $backgroundColor !== ''
            && strtolower($backgroundColor) !== 'transparent'
        ) {
            return true;
        }

        foreach ([
            'borderWidth',
            'borderRadius',
            'padding',
            'paddingTop',
            'paddingBottom',
            'paddingLeft',
            'paddingRight',
        ] as $key) {
            if ($this->isPositiveNumber($props[$key] ?? null)) {
                return true;
            }
        }

        return false;
    }

    private function hasContentChildren(array $node): bool
    {
        $children = $node['children'] ?? [];

        if (! is_array($children)) {
            return false;
        }

        foreach ($children as $child) {
            if (
                is_array($child)
                && in_array($child['type'] ?? null, self::CONTENT_WIDGET_TYPES, true)
            ) {
                return true;
            }
        }

        return false;
    }

    private function isPositiveNumber(mixed $value): bool
    {
        return is_numeric($value) && (float) $value > 0;
    }

    /**
     * Persist a canonical structure for existing pages loaded with legacy shape.
     */
    public function ensureCanonicalStructure(Page $page): Page
    {
        $structure = $this->canonicalizeStructure($page->structure);

        if ($page->structure !== $structure) {
            $page->forceFill([
                'structure' => $structure,
            ])->save();

            $page->refresh();
        }

        return $page;
    }

    /**
     * Générer un slug unique
     */
    /**
     * Prepare page structure for JSON consumed by the browser builder.
     */
    public function structureForBuilder(?array $structure): array
    {
        return $this->encodeNodeCollectionForBuilder(
            $this->canonicalizeStructure($structure)
        );
    }

    private function encodeNodeCollectionForBuilder(array $nodes): array
    {
        return array_map(
            fn (array $node) => $this->encodeNodeForBuilder($node),
            $nodes
        );
    }

    private function encodeNodeForBuilder(array $node): array
    {
        $props = $this->normalizeNodeProps(
            $node['props'] ?? []
        );

        $node['props'] = $props === []
            ? (object) []
            : $props;

        $children = $node['children'] ?? [];

        $node['children'] = is_array($children) && array_is_list($children)
            ? $this->encodeNodeCollectionForBuilder($children)
            : [];

        return $node;
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = Str::slug($baseSlug);
        $original = $slug;
        $i = 1;

        while (Page::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
