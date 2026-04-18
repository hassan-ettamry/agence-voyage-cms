<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     *  Liste des pages (pagination)
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Page::class);

        // Multi-tenant via global scope
        $pages = Page::latest()->paginate(15);

        return response()->json($pages);
    }

    /**
     *  Afficher une page
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        $this->authorize('view', $page);

        return response()->json($page);
    }

    /**
     *  Créer une page
     */
    public function store(StorePageRequest $request): JsonResponse
    {

        $this->authorize('create', Page::class);

        $agencyId = $request->user()->agency_id;

        // Données validées uniquement
        $data = $request->validated();

        //  Protection contre injection
        unset($data['agency_id']);

        // On force agency côté serveur
        $data['agency_id'] = $agencyId;

        $page = Page::create($data);

        return response()->json($page, 201);
    }

    /**
     *  Mettre à jour une page
     */
    public function update(UpdatePageRequest $request, string $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        $this->authorize('update', $page);

        $data = $request->validated();

        //  Nettoyage des champs sensibles
        unset(
            $data['agency_id'],
            $data['id'],
            $data['created_at'],
            $data['updated_at']
        );

        $page->update($data);

        return response()->json($page);
    }

    /**
     *  Supprimer une page
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        $this->authorize('delete', $page);

        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully'
        ]);
    }
}