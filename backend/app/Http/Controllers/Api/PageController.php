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
     *  LISTE DES PAGES
     * Search + Filters + Sorting + Pagination
     */
    public function index(Request $request)
    {
        //  Authorization (Policy)
        $this->authorize('viewAny', Page::class);

        $query = Page::query();

        /**
         *  SEARCH (title + slug)
         */
        $search = trim($request->input('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        /**
         *  FILTER BY STATUS
         */
        if (
            $request->filled('status') &&
            in_array($request->status, ['draft', 'published'])
        ) {
            $query->where('status', $request->status);
        }

        /**
         *  FILTER BY DATE
         */
        if ($request->filled('from_date') && strtotime($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date') && strtotime($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        /**
         *  SORTING
         */
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        if (!in_array($sortBy, ['title', 'created_at', 'status'])) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $query->orderBy($sortBy, $sortDir);

        /**
         *  PAGINATION
         */
        $perPage = max(1, min($request->input('per_page', 15), 100));

        $pages = $query
            ->paginate($perPage)
            ->appends($request->query());

        return PageResource::collection($pages);
    }

    /**
     *  SHOW ONE PAGE
     */
    public function show(Page $page)
    {
        $this->authorize('view', $page);

        return new PageResource($page);
    }

    /**
     *  CREATE PAGE
     */
    public function store(StorePageRequest $request)
    {
        $this->authorize('create', Page::class);

        $agencyId = $request->user()->agency_id;

        $data = $request->validated();

        //  Prevent injection
        unset($data['agency_id']);

        // Force agency from auth user
        $data['agency_id'] = $agencyId;

        $page = Page::create($data);

        return (new PageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    /**
     *  UPDATE PAGE
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        $data = $request->validated();

        //  Remove sensitive fields
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
     *  DELETE PAGE
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