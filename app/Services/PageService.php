<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    /**
     * Créer une nouvelle page
     */
    public function create(array $data, $user): Page
    {
        return DB::transaction(function () use ($data, $user) {

            // Associer la page à l'agence de l'utilisateur
            $data['agency_id'] = $user->agency_id;

            // Structure par défaut si absente
            $data['structure'] ??= [
                'type' => 'page',
                'children' => [],
            ];

            // Générer un slug unique
            $data['slug'] = $this->generateUniqueSlug(
                $data['slug'] ?? Str::slug($data['title'])
            );

            return Page::create($data);
        });
    }

    /**
     * Mettre à jour une page
     * + créer une version si la structure change
     */
    public function update(Page $page, array $data, $user): Page
    {
        return DB::transaction(function () use ($page, $data, $user) {

            // Vérifier si la structure a changé
            $structureChanged = isset($data['structure']) &&
                json_encode($data['structure']) !== json_encode($page->structure);

            // Si changement → créer une version
            if ($structureChanged) {
                $this->createVersion($page, $user);
            }

            // Vérifier si le slug change → rendre unique
            if (isset($data['slug']) && $data['slug'] !== $page->slug) {
                $data['slug'] = $this->generateUniqueSlug($data['slug']);
            }

            $page->update($data);

            return $page;
        });
    }

    /**
     * Restaurer une version précédente
     */
    public function restore(Page $page, PageVersion $version, $user): Page
    {
        return DB::transaction(function () use ($page, $version, $user) {

            // Sauvegarder l'état actuel avant restauration
            $this->createVersion($page, $user);

            // Restaurer les données
            $page->update([
                'structure' => $version->structure,
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
            $newPage->title = $page->title . ' (copie)';
            $newPage->slug = $this->generateUniqueSlug($page->slug . '-copy');
            $newPage->status = Page::STATUS_DRAFT;
            $newPage->published_at = null;
            $newPage->agency_id = $user->agency_id;

            $newPage->save();

            return $newPage;
        });
    }

    /**
     * Publier une page
     */
    public function publish(Page $page): Page
    {
        $page->update([
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
            'structure' => $page->structure,
            'meta' => $page->meta,
            'version' => $lastVersion + 1,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Générer un slug unique
     */
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