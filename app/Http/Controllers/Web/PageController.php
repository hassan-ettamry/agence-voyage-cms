<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\PageService;
use App\Services\Renderer\PageRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Scopes\AgencyScope;

class PageController extends Controller
{
    private PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->middleware('auth')->except(['show']);
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update']);

        $this->pageService = $pageService;
    }

    /**
     * Liste des pages
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('title', 'like', "%{$search}%")
                       ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return view('pages.index', compact('pages'));
    }

    /**
     * Create form
     */
    public function create()
    {
        $this->authorize('create', Page::class);

        return view('pages.create');
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

        Cache::forget('dashboard_stats_' . auth()->id());

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', 'Page créée avec succès.');
    }

    /**
     * PUBLIC PAGE
     */
    public function show(string $slug, PageRenderer $renderer)
    {
        $page = Cache::remember("page_{$slug}", 3600, function () use ($slug) {

            return Page::withoutGlobalScope(AgencyScope::class)
                ->where('slug', $slug)
                ->where('status', Page::STATUS_PUBLISHED)
                ->whereNotNull('published_at')
                ->firstOrFail();
        });

        $html = $renderer->render($page->structure ?? []);

        return view('pages.show', compact('page', 'html'));
    }

    /**
     * Edit form
     */
    public function edit(Page $page)
    {
        $this->authorize('update', $page);

        return view('pages.edit', compact('page'));
    }

    /**
     * Update page
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        $oldSlug = $page->slug;

        $this->pageService->update(
            $page,
            $request->validated(),
            $request->user()
        );

        Cache::forget("page_{$oldSlug}");
        Cache::forget("page_{$page->slug}");
        Cache::forget('dashboard_stats_' . auth()->id());

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

        return back()->with('success', 'Page publiée.');
    }

    /**
     * Delete page
     */
    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);

        $slug = $page->slug;

        $page->delete();

        Cache::forget("page_{$slug}");
        Cache::forget('dashboard_stats_' . auth()->id());

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
            ->route('pages.edit', $page)
            ->with('success', 'Version restaurée.');
    }

    /**
     * Duplicate page
     */
    public function duplicate(Page $page)
    {
        $this->authorize('create', Page::class);

        $newPage = $this->pageService->duplicate($page, auth()->user());

        return redirect()
            ->route('pages.edit', $newPage)
            ->with('success', 'Page dupliquée.');
    }
}