<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    /**
     * Service métier pour gérer la logique des pages
     */
    private PageService $pageService;

    public function __construct(PageService $pageService)
    {
        // Middleware : utilisateur connecté obligatoire
        $this->middleware('auth');

        // Vérification email obligatoire pour créer/modifier
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update']);

        $this->pageService = $pageService;
    }

    /**
     * Liste des pages avec filtres (recherche + statut)
     */
    public function index(Request $request)
    {
        $pages = Page::query()

            // Filtre par recherche (titre ou slug)
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('title', 'like', "%{$search}%")
                       ->orWhere('slug', 'like', "%{$search}%");
                });
            })

            // Filtre par statut (draft, published...)
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })

            // Tri par date de création (plus récent en premier)
            ->latest()

            // Pagination (15 par défaut)
            ->paginate($request->integer('per_page', 15))

            // Conserver les paramètres dans la pagination
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
     * Enregistrer une nouvelle page
     */
    public function store(StorePageRequest $request)
    {
        $page = $this->pageService->create(
            $request->validated(),
            $request->user()
        );

        // Nettoyage du cache du dashboard
        Cache::forget('dashboard_stats_' . auth()->id());

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', 'Page créée avec succès.');
    }

    /**
     * Affichage public d'une page (via slug)
     * Mise en cache pour performance
     */
    public function show(string $slug)
    {
        $page = Cache::remember("page_{$slug}", 3600, function () use ($slug) {
            return Page::with('author')
                ->where('slug', $slug)
                ->where('status', Page::STATUS_PUBLISHED)
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
     * Mise à jour d'une page
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        // Sauvegarde de l'ancien slug (important si modifié)
        $oldSlug = $page->slug;

        $this->pageService->update(
            $page,
            $request->validated(),
            $request->user()
        );

        // Nettoyage du cache (ancien + nouveau slug)
        Cache::forget("page_{$oldSlug}");
        Cache::forget("page_{$page->slug}");

        // Nettoyage du dashboard
        Cache::forget('dashboard_stats_' . auth()->id());

        return back()->with('success', 'Page mise à jour.');
    }

    /**
     * Publication d'une page
     */
    public function publish(Page $page)
    {
        $this->authorize('update', $page);

        $this->pageService->publish($page);

        // Nettoyage du cache public
        Cache::forget("page_{$page->slug}");

        return back()->with('success', 'Page publiée.');
    }

    /**
     * Suppression d'une page
     */
    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);

        $slug = $page->slug;

        $page->delete();

        // Nettoyage cache
        Cache::forget("page_{$slug}");
        Cache::forget('dashboard_stats_' . auth()->id());

        return redirect()
            ->route('pages.index')
            ->with('success', 'Page supprimée.');
    }

    /**
     * Liste des versions d'une page
     */
    public function versions(Page $page)
    {
        $this->authorize('view', $page);

        $versions = $page->versions()
            ->with('author')
            ->latest('version')
            ->paginate(20);

        return view('pages.versions', compact('page', 'versions'));
    }

    /**
     * Restaurer une version précédente
     */
    public function restore(Page $page, PageVersion $version)
    {
        $this->authorize('update', $page);

        // Sécurité : vérifier que la version appartient à la page
        abort_unless($version->page_id === $page->id, 404);

        $this->pageService->restore($page, $version, auth()->user());

        Cache::forget("page_{$page->slug}");

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', 'Version restaurée.');
    }

    /**
     * Dupliquer une page
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