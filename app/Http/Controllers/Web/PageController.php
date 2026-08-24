<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Agency;
use App\Models\Component;
use App\Models\Destination;
use App\Models\Offer;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\AgencyThemeService;
use App\Services\DashboardStatsService;
use App\Services\MenuService;
use App\Services\PageIndexService;
use App\Services\PageService;
use App\Services\PublicContentCache;
use App\Services\PublicSiteUrl;
use App\Services\Renderer\PageRenderer;
use App\Support\AgencyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private PageIndexService $pageIndexService,
        private AgencyThemeService $themeService
    ) {
        $this->middleware('auth')->except(['show', 'siteHome', 'siteShow']);
        $this->middleware('verified')->only(['create', 'store', 'edit', 'builder', 'update']);
    }

    /**
     * Liste des pages
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Page::class);

        return view('pages.index', $this->pageIndexService->build($request->query(), $request->user()));
    }

    /**
     * Create form
     */
    public function create(Request $request)
    {
        $this->authorize('create', Page::class);

        return view('pages.index', array_merge(
            $this->pageIndexService->build($request->query(), $request->user()),
            ['openCreateModal' => true]
        ));
    }

    /**
     * Store page
     */
    public function store(StorePageRequest $request)
    {
        $this->authorize('create', Page::class);

        $page = $this->pageService->create(
            $request->validated(),
            $request->user()
        );

        DashboardStatsService::forgetFor($request->user());

        return redirect()
            ->route('pages.builder', $page)
            ->with('success', 'Page créée avec succès.');
    }

    /**
     * PUBLIC PAGE
     */
    public function siteHome(Request $request, PageRenderer $renderer, MenuService $menuService)
    {
        $agency = $this->publicAgency($request);

        $page = Page::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->orderByRaw("CASE WHEN slug = 'home' THEN 0 ELSE 1 END")
            ->orderBy('published_at')
            ->orderBy('created_at')
            ->firstOrFail();

        return $this->renderPublicPage($page, $agency, $renderer, $menuService);
    }

    public function siteShow(
        Request $request,
        string $agencySlug,
        string $pageSlug,
        PageRenderer $renderer,
        MenuService $menuService
    ) {
        $agency = $this->publicAgency($request);

        if ($pageSlug === 'destinations') {
            return redirect()->to(app(PublicSiteUrl::class)->destinations($agency), 301);
        }

        if ($pageSlug === 'offers') {
            return redirect()->to(app(PublicSiteUrl::class)->offers($agency), 301);
        }

        $cacheKey = PublicContentCache::pageKey($agency->id, $pageSlug);
        $page = Cache::remember($cacheKey, now()->addHour(), fn () => Page::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->where('slug', $pageSlug)
            ->firstOrFail());

        return $this->renderPublicPage($page, $agency, $renderer, $menuService);
    }

    /**
     * Redirect an unambiguous legacy page URL to its tenant-aware URL.
     */
    public function show(string $slug, PublicSiteUrl $urls)
    {
        $pages = Page::withoutGlobalScopes()
            ->published()
            ->where('slug', $slug)
            ->whereHas('agency', fn ($query) => $query->where('status', 'active'))
            ->with('agency')
            ->limit(2)
            ->get();

        abort_unless($pages->count() === 1, 404);

        $page = $pages->first();

        if ($slug === 'destinations') {
            return redirect()->to($urls->destinations($page->agency), 301);
        }

        if ($slug === 'offers') {
            return redirect()->to($urls->offers($page->agency), 301);
        }

        return redirect()->to($urls->page($page->agency, $page), 301);
    }

    private function renderPublicPage(
        Page $page,
        Agency $agency,
        PageRenderer $renderer,
        MenuService $menuService
    ) {
        AgencyContext::set($agency->id);

        $page = $this->pageService->ensureCanonicalStructure($page);

        $html = $renderer->render(
            $page->structure ?? [],
            'live'
        );

        $menuItems = $menuService->getMenu('main');

        return view('frontend.page', [
            'page' => $page,
            'html' => $html,
            'menu' => $menuItems,
            'siteAgency' => $agency,
            'metaTitle' => data_get($page->meta, 'title') ?: $page->title,
            'metaDescription' => data_get($page->meta, 'description') ?: str($page->title)->append(' — ', $agency->name),
        ]);
    }

    private function publicAgency(Request $request): Agency
    {
        $agency = $request->attributes->get('publicAgency');

        abort_unless($agency instanceof Agency, 404);

        return $agency;
    }

    /**
     * Edit form
     */
    public function edit(Page $page)
    {
        $this->authorize('update', $page);

        $page = $this->pageService->ensureCanonicalStructure($page);
        $menus = app(MenuService::class)->optionsForUser(auth()->user());
        $menuSelection = app(MenuService::class)->selectionForPage($page);

        return view('pages.edit', compact('page', 'menus', 'menuSelection'));
    }

    /**
     * Visual page builder
     */
    public function builder(Page $page)
    {
        $this->authorize('update', $page);

        $page = $this->pageService->ensureCanonicalStructure($page);
        $menuItems = app(MenuService::class)->menuForPagePreview($page);
        $builderStructure = $this->pageService->structureForBuilder($page->structure);
        $builderPages = Page::withoutGlobalScopes()
            ->where('agency_id', $page->agency_id)
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'status']);
        $builderThemeCss = $this->themeService->cssVariables($page->agency);

        $templateOnlyTypes = [
            'section',
            'row',
            'column',
        ];

        $widgets = Component::where('is_active', true)
            ->whereNotIn('type', $templateOnlyTypes)
            ->orderBy('category')
            ->get();

        $builderDataOptions = [
            'destinations' => Destination::published()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Destination $destination) => ['id' => $destination->id, 'label' => $destination->name])
                ->values(),
            'offers' => Offer::published()
                ->orderBy('title')
                ->get(['id', 'title'])
                ->map(fn (Offer $offer) => ['id' => $offer->id, 'label' => $offer->title])
                ->values(),
        ];

        return view('pages.builder', compact(
            'page',
            'builderStructure',
            'widgets',
            'menuItems',
            'builderPages',
            'builderThemeCss',
            'builderDataOptions'
        ));
    }

    public function preview(Page $page, PageRenderer $renderer)
    {
        $this->authorize('view', $page);
        AgencyContext::set($page->agency_id);
        $page = $this->pageService->ensureCanonicalStructure($page);

        return view('frontend.page', [
            'page' => $page,
            'html' => $renderer->render($page->structure ?? [], 'preview'),
            'menu' => app(MenuService::class)->menuForPagePreview($page),
            'siteAgency' => $page->agency,
            'previewMode' => true,
        ]);
    }

    /**
     * Update page
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        if ($request->expectsJson()) {
            $validated = $request->validated();
            $oldSlug = $page->slug;
            $page = $this->pageService->update(
                $page,
                array_intersect_key($validated, array_flip(['title', 'slug', 'structure'])),
                $request->user()
            );

            PublicContentCache::forgetPage($page->agency_id, $oldSlug, $page->slug);

            return response()->json([
                'success' => true,
                'message' => 'Page saved successfully',
                'page' => ['title' => $page->title, 'slug' => $page->slug],
            ]);
        }

        $oldSlug = $page->slug;

        $this->pageService->update(
            $page,
            $request->validated(),
            $request->user()
        );

        PublicContentCache::forgetPage($page->agency_id, $oldSlug, $page->slug);
        DashboardStatsService::forgetFor($request->user());

        return back()->with('success', 'Page mise à jour.');
    }

    /**
     * Publish page
     */
    public function publish(Page $page)
    {
        $this->authorize('update', $page);

        $this->pageService->publish($page);

        PublicContentCache::forgetPage($page->agency_id, $page->slug);
        DashboardStatsService::forgetFor(auth()->user());

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Page published successfully']);
        }

        return back()->with('success', 'Page publiée.');
    }

    /**
     * Delete page
     */
    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);

        $slug = $page->slug;

        $page->menuItems()->delete();
        $page->delete();

        PublicContentCache::forgetPage($page->agency_id, $slug);
        DashboardStatsService::forgetFor(auth()->user());

        return redirect()
            ->route('pages.index')
            ->with('success', 'Page supprimée.');
    }

    /**
     * Versions
     */
    public function versions(Page $page)
    {
        $this->authorize('view', $page);

        $versions = $page->versions()
            ->latest('version')
            ->paginate(20);

        return view('pages.versions', compact('page', 'versions'));
    }

    /**
     * Restore version
     */
    public function restore(Page $page, PageVersion $version)
    {
        $this->authorize('update', $page);

        abort_unless($version->page_id === $page->id, 404);

        $this->pageService->restore($page, $version, auth()->user());

        PublicContentCache::forgetPage($page->agency_id, $page->slug);

        return redirect()
            ->route('pages.builder', $page)
            ->with('success', 'Version restaurée.');
    }

    /**
     * Duplicate page
     */
    public function duplicate(Page $page)
    {
        $this->authorize('create', Page::class);

        $newPage = $this->pageService->duplicate($page, auth()->user());

        DashboardStatsService::forgetFor(auth()->user());

        return redirect()
            ->route('pages.builder', $newPage)
            ->with('success', 'Page dupliquée.');
    }
}
