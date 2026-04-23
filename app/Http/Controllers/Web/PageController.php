<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update']);
    }

    /**
     * Liste des pages
     */
    public function index(Request $request)
    {
        $pages = Page::query()
            ->when($request->search, fn($q, $search) => 
                $q->where(function($sq) use ($search) {
                    $sq->where('title', 'like', "%{$search}%")
                       ->orWhere('slug', 'like', "%{$search}%");
                })
            )
            ->when($request->status, fn($q, $status) => 
                $q->where('status', $status)
            )
            ->when($request->sort_by === 'title', fn($q) => 
                $q->orderBy('title', $request->sort_dir ?? 'asc')
            )
            ->when(!$request->sort_by || $request->sort_by === 'created_at', fn($q) => 
                $q->orderBy('created_at', $request->sort_dir ?? 'desc')
            )
            ->paginate($request->per_page ?? 15)
            ->withQueryString();

        return view('pages.index', compact('pages'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('pages.create');
    }

    /**
     * Créer une page
     */
    public function store(StorePageRequest $request)
    {
        $data = $request->validated();
        $data['agency_id'] = auth()->user()->agency_id;

        if (!isset($data['structure'])) {
            $data['structure'] = ['type' => 'page', 'children' => []];
        }

        $page = Page::create($data);

        // Nettoyer le cache du dashboard
        Cache::forget('dashboard_stats_' . auth()->id());

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', 'Page créée avec succès.');
    }

    /**
     * Afficher une page (front)
     */
    public function show($slug)
    {
        $page = Cache::remember("page_{$slug}", 3600, function () use ($slug) {
            return Page::where('slug', $slug)
                ->where('status', 'published')
                ->firstOrFail();
        });

        return view('pages.show', compact('page'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Page $page)
    {
        $this->authorize('update', $page);
        return view('pages.edit', compact('page'));
    }

    /**
     * Mettre à jour une page
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        $data = $request->validated();

        DB::transaction(function () use ($page, $data, $request) {
            $shouldVersion = isset($data['structure']) && $data['structure'] !== $page->structure;

            if ($shouldVersion) {
                $lastVersion = $page->versions()->max('version') ?? 0;

                PageVersion::create([
                    'page_id' => $page->id,
                    'structure' => $page->structure,
                    'meta' => $page->meta,
                    'version' => $lastVersion + 1,
                    'created_by' => auth()->id(),
                ]);
            }

            $page->update($data);
        });

        // Nettoyer le cache
        Cache::forget("page_{$page->slug}");
        Cache::forget('dashboard_stats_' . auth()->id());

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', 'Page mise à jour.');
    }

    /**
     * Publier une page
     */
    public function publish(Page $page)
    {
        $this->authorize('update', $page);

        $page->update([
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        Cache::forget("page_{$page->slug}");

        return back()->with('success', 'Page publiée.');
    }

    /**
     * Supprimer une page
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
     * Historique des versions
     */
    public function versions(Page $page)
    {
        $this->authorize('view', $page);

        $versions = $page->versions()
            ->with('author')
            ->orderByDesc('version')
            ->paginate(20);

        return view('pages.versions', compact('page', 'versions'));
    }

    /**
     * Restaurer une version
     */
    public function restore(Page $page, PageVersion $version)
    {
        $this->authorize('update', $page);

        if ($version->page_id !== $page->id) {
            abort(400);
        }

        DB::transaction(function () use ($page, $version) {
            $lastVersion = $page->versions()->max('version') ?? 0;

            PageVersion::create([
                'page_id' => $page->id,
                'structure' => $page->structure,
                'meta' => $page->meta,
                'version' => $lastVersion + 1,
                'created_by' => auth()->id(),
            ]);

            $page->update([
                'structure' => $version->structure,
                'meta' => $version->meta,
            ]);
        });

        Cache::forget("page_{$page->slug}");

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', 'Version restaurée avec succès.');
    }

    /**
     * Dupliquer une page
     */
    public function duplicate(Page $page)
    {
        $this->authorize('create', Page::class);

        $newPage = $page->replicate();
        $newPage->title = $page->title . ' (copie)';
        $newPage->slug = $page->slug . '-copy';
        $newPage->status = 'draft';
        $newPage->published_at = null;
        $newPage->save();

        return redirect()
            ->route('pages.edit', $newPage)
            ->with('success', 'Page dupliquée avec succès.');
    }
}