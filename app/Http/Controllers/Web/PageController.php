<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Component;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\DashboardStatsService;
use App\Services\AgencyThemeService;
use App\Services\MenuService;
use App\Services\PageIndexService;
use App\Services\PageService;
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
        $this->middleware('auth')->except(['show']);
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
    public function show(string $slug, PageRenderer $renderer, MenuService $menuService)
    {
        $page = Page::withoutGlobalScopes()
            ->where('slug', $slug)
            ->where('status', Page::STATUS_PUBLISHED)
            ->firstOrFail();

        AgencyContext::set($page->agency_id);

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
        ]);
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
            'destination-grid',
            'featured-destinations',
            'offer-grid',
            'special-offers',
            'offer-card',
        ];

        $widgets = Component::where('is_active', true)
            ->whereNotIn('type', $templateOnlyTypes)
            ->orderBy('category')
            ->get();

        return view('pages.builder', compact(
            'page',
            'builderStructure',
            'widgets',
            'menuItems',
            'builderPages'
            ,
            'builderThemeCss'
        ));
    }

    /**
     * Update page
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        if ($request->expectsJson()) {
            $this->pageService->updateStructure(
                $page,
                $request->validated()['structure'] ?? [],
                $request->user()
            );

            Cache::forget("page_{$page->slug}");

            return response()->json([
                'success' => true,
                'message' => 'Page saved successfully',
            ]);
        }

        $oldSlug = $page->slug;

        $this->pageService->update(
            $page,
            $request->validated(),
            $request->user()
        );

        Cache::forget("page_{$oldSlug}");
        Cache::forget("page_{$page->slug}");
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

        Cache::forget("page_{$page->slug}");
        DashboardStatsService::forgetFor(auth()->user());

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

        Cache::forget("page_{$slug}");
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

        Cache::forget("page_{$page->slug}");

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
