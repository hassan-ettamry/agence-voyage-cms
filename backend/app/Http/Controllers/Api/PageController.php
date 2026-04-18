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
     * Liste des pages (pagination)
     */
    public function index(Request $request): JsonResponse
    {
        // Global scope applique automatiquement agency_id
        $pages = Page::latest()->paginate(15);

        return response()->json($pages);
    }

    /**
     * Récupérer une page
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        return response()->json($page);
    }

    /**
     * Créer une page
     */
    public function store(StorePageRequest $request): JsonResponse
    {
        $agencyId = $request->user()->agency_id;

        // Données validées
        $data = $request->validated();

        // Sécurité : empêcher injection agency_id
        unset($data['agency_id']);

        // On force l'agence côté serveur
        $data['agency_id'] = $agencyId;

        $page = Page::create($data);

        return response()->json($page, 201);
    }

    /**
     * Mettre à jour une page
     */
    public function update(UpdatePageRequest $request, string $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        $data = $request->validated();

        // Nettoyage des champs sensibles
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
     * Supprimer une page
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully'
        ]);
    }
}