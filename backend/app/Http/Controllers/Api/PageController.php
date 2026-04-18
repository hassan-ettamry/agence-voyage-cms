<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     *  Liste des pages (pagination)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Page::class);

        // Multi-tenant via global scope
        $pages = Page::latest()->paginate(15);

        //  Resource collection 
        return PageResource::collection($pages);
    }

    /**
     *  Afficher une page
     */
    public function show(Page $page)
    {
        $this->authorize('view', $page);

        return new PageResource($page);
    }

    /**
     *  Créer une page
     */
    public function store(StorePageRequest $request)
    {
        $this->authorize('create', Page::class);

        $agencyId = $request->user()->agency_id;

        // Données validées
        $data = $request->validated();

        //  Protection
        unset($data['agency_id']);

        // Force agency côté serveur
        $data['agency_id'] = $agencyId;

        $page = Page::create($data);

        return (new PageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    /**
     *  Mettre à jour une page
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        $data = $request->validated();

        //  Nettoyage
        unset(
            $data['agency_id'],
            $data['id'],
            $data['created_at'],
            $data['updated_at']
        );

        $page->update($data);

        return new PageResource($page);
    }

    /**
     *  Supprimer une page
     */
    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);

        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully'
        ]);
    }
}