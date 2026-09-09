<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteArchive;
use App\Models\SiteTemplate;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiteTemplateApplicationService
{
    public function __construct(
        private PageService $pageService,
        private PageStructureValidator $structureValidator,
        private MenuService $menuService,
        private AgencyThemeService $themeService
    ) {
    }

    public function apply(
        SiteTemplate $template,
        User $user,
        ?Theme $theme = null,
        bool $confirmThemeReset = false
    ): Page
    {
        return DB::transaction(function () use ($template, $user, $theme, $confirmThemeReset) {
            $agency = Agency::query()->findOrFail($user->agency_id);
            $pages = $this->templatePages($template);
            $selectedTheme = $theme ?? $template->theme;
            $changesTemplate = $agency->active_site_template_id !== $template->id;

            if ($pages === []) {
                throw new \InvalidArgumentException('The selected site template does not contain pages.');
            }

            if ($selectedTheme !== null && $selectedTheme->status !== Theme::STATUS_ACTIVE) {
                throw new \InvalidArgumentException('The selected theme is not active.');
            }

            $existingPages = $this->currentPages($agency);
            $existingMenus = $this->currentMenus($agency);

            if ($existingPages->isNotEmpty() || $existingMenus->contains(fn (Menu $menu) => $menu->items->isNotEmpty())) {
                $this->archiveCurrentSite($agency, $template, $user, $existingPages, $existingMenus);
            }

            if ($changesTemplate && $this->themeService->hasOverrides($agency)) {
                if (! $confirmThemeReset) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'confirm_theme_reset' => 'Confirm that applying this site template will reset agency theme customizations.',
                    ]);
                }

                $this->themeService->resetOverrides($agency);
            }

            $this->replaceCurrentSite($agency);

            $menu = $this->menuService->ensureDefaultForAgency($agency->id);
            $createdPages = collect();

            foreach ($pages as $index => $pageData) {
                $page = $this->createPageFromTemplate($agency, $pageData);
                $createdPages->push($page);

                if (($pageData['include_in_menu'] ?? true) !== false) {
                    $menuUrl = is_string($pageData['menu_url'] ?? null)
                        ? $pageData['menu_url']
                        : null;
                    MenuItem::create([
                        'menu_id' => $menu->id,
                        'page_id' => $menuUrl ? null : $page->id,
                        'title' => $pageData['menu_title'] ?? $page->title,
                        'url' => $menuUrl,
                        'parent_id' => null,
                        'order' => $index + 1,
                    ]);
                }
            }

            $agency->forceFill([
                'active_site_template_id' => $template->id,
                'template_applied_at' => now(),
            ])->save();

            if ($selectedTheme !== null) {
                $this->themeService->applyTheme($agency, $selectedTheme, $confirmThemeReset);
            }

            DashboardStatsService::forgetFor($user);

            return $createdPages->firstWhere('slug', 'home')
                ?? $createdPages->firstWhere('slug', 'accueil')
                ?? $createdPages->first();
        });
    }

    private function templatePages(SiteTemplate $template): array
    {
        $pages = $template->pages ?? [];

        return is_array($pages) && array_is_list($pages)
            ? $pages
            : [];
    }

    private function currentPages(Agency $agency)
    {
        return Page::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->orderBy('created_at')
            ->get();
    }

    private function currentMenus(Agency $agency)
    {
        return Menu::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->with('items')
            ->orderBy('id')
            ->get();
    }

    private function archiveCurrentSite($agency, SiteTemplate $template, User $user, $pages, $menus): void
    {
        SiteArchive::create([
            'agency_id' => $agency->id,
            'name' => 'Archive before '.$template->name.' - '.now()->format('Y-m-d H:i'),
            'source_template_id' => $template->id,
            'created_by' => $user->id,
            'snapshot' => [
                'version' => 1,
                'created_at' => now()->toISOString(),
                'agency' => [
                    'id' => $agency->id,
                    'theme_id' => $agency->theme_id,
                    'theme_overrides' => $agency->theme_overrides,
                    'active_site_template_id' => $agency->active_site_template_id,
                    'template_applied_at' => optional($agency->template_applied_at)->toISOString(),
                ],
                'pages' => $pages->map(fn (Page $page) => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'structure' => $page->structure ?? [],
                    'meta' => $page->meta ?? [],
                    'status' => $page->status,
                    'published_at' => optional($page->published_at)->toISOString(),
                    'created_at' => optional($page->created_at)->toISOString(),
                    'updated_at' => optional($page->updated_at)->toISOString(),
                ])->values()->all(),
                'menus' => $menus->map(fn (Menu $menu) => [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'is_default' => $menu->is_default,
                    'items' => $menu->items->map(fn (MenuItem $item) => [
                        'id' => $item->id,
                        'title' => $item->title,
                        'page_id' => $item->page_id,
                        'url' => $item->url,
                        'parent_id' => $item->parent_id,
                        'order' => $item->order,
                    ])->values()->all(),
                ])->values()->all(),
            ],
        ]);
    }

    private function replaceCurrentSite(Agency $agency): void
    {
        Menu::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->get()
            ->each(fn (Menu $menu) => $menu->delete());

        Page::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->get()
            ->each(fn (Page $page) => $page->delete());
    }

    private function createPageFromTemplate(Agency $agency, array $pageData): Page
    {
        $structure = $this->pageService->canonicalizeStructure(
            $this->themeReadyNodes(
                $this->regenerateNodeIds($pageData['structure'] ?? [])
            )
        );

        $this->structureValidator->validate($structure);

        return Page::create([
            'agency_id' => $agency->id,
            'title' => $pageData['title'] ?? 'Untitled Page',
            'slug' => Str::slug($pageData['slug'] ?? $pageData['title'] ?? Str::uuid()->toString()),
            'structure' => $structure,
            'meta' => $pageData['meta'] ?? [],
            'status' => $pageData['status'] ?? Page::STATUS_DRAFT,
            'published_at' => $pageData['published_at'] ?? null,
        ]);
    }

    private function regenerateNodeIds(mixed $nodes): array
    {
        if (! is_array($nodes) || ! array_is_list($nodes)) {
            return [];
        }

        return array_map(fn ($node) => $this->regenerateNodeId($node), $nodes);
    }

    private function regenerateNodeId(mixed $node): array
    {
        if (! is_array($node)) {
            return [];
        }

        $node['id'] = (string) Str::uuid();

        if (! isset($node['props']) || ! is_array($node['props']) || array_is_list($node['props'])) {
            $node['props'] = [];
        }

        $children = $node['children'] ?? [];
        $node['children'] = is_array($children) && array_is_list($children)
            ? array_map(fn ($child) => $this->regenerateNodeId($child), $children)
            : [];

        return $node;
    }

    private function themeReadyNodes(array $nodes): array
    {
        return array_map(fn (array $node) => $this->themeReadyNode($node), $nodes);
    }

    private function themeReadyNode(array $node): array
    {
        $themeProps = [
            'section' => ['backgroundColor', 'textColor', 'borderColor', 'borderRadius', 'boxShadow'],
            'container' => ['backgroundColor', 'textColor', 'borderColor', 'borderRadius', 'boxShadow'],
            'text' => ['color', 'textColor'],
            'richtext' => ['color', 'textColor'],
            'heading' => ['color', 'textColor'],
            'button' => ['backgroundColor', 'textColor', 'borderColor', 'borderRadius', 'boxShadow'],
            'link' => ['color', 'textColor'],
            'icon' => ['color', 'backgroundColor', 'borderColor'],
            'icon-text' => ['color', 'iconColor', 'titleColor', 'textColor', 'backgroundColor', 'borderColor'],
            'contact-form' => ['backgroundColor', 'buttonColor', 'textColor', 'borderColor', 'borderRadius', 'boxShadow'],
            'faq' => ['backgroundColor', 'textColor', 'borderColor', 'borderRadius', 'boxShadow'],
            'countdown' => ['backgroundColor', 'textColor', 'accentColor', 'borderColor', 'borderRadius', 'boxShadow'],
        ];

        $type = is_string($node['type'] ?? null) ? $node['type'] : '';
        $props = is_array($node['props'] ?? null) && ! array_is_list($node['props'])
            ? $node['props']
            : [];

        foreach ($themeProps[$type] ?? [] as $key) {
            unset($props[$key]);
        }

        $node['props'] = $props;
        $node['children'] = $this->themeReadyNodes($node['children'] ?? []);

        return $node;
    }
}
