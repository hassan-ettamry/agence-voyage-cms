<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    /**
     * LISTE DES PAGES
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Page::class);

        $query = Page::query();

        $search = trim($request->input('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (
            $request->filled('status') &&
            in_array($request->status, ['draft', 'published'])
        ) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && strtotime($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date') && strtotime($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        if (!in_array($sortBy, ['title', 'created_at', 'status'])) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $query->orderBy($sortBy, $sortDir);

        $perPage = max(1, min($request->input('per_page', 15), 100));

        $pages = $query->paginate($perPage)->appends($request->query());

        return PageResource::collection($pages);
    }

    /**
     * SHOW ONE PAGE
     */
    public function show(Page $page)
    {
        $this->authorize('view', $page);

        return new PageResource($page);
    }

    /**
     * CREATE PAGE
     */
    public function store(StorePageRequest $request)
    {
        $this->authorize('create', Page::class);

        $data = $request->validated();

        unset($data['agency_id']);

        $data['agency_id'] = $request->user()->agency_id;

        if (!isset($data['structure'])) {
            $data['structure'] = [
                'type' => 'page',
                'children' => []
            ];
        }

        $page = Page::create($data);

        return (new PageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * UPDATE PAGE
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->authorize('update', $page);

        $data = $request->validated();

        if (isset($data['structure']) && !is_array($data['structure'])) {
            return response()->json([
                'message' => 'Invalid structure format'
            ], 422);
        }

        unset(
            $data['agency_id'],
            $data['id'],
            $data['created_at'],
            $data['updated_at']
        );

        return DB::transaction(function () use ($page, $data, $request) {

            // Only version if something changed
            $shouldVersion =
                (isset($data['structure']) && $data['structure'] !== $page->structure) ||
                (isset($data['meta']) && $data['meta'] !== $page->meta);

            if ($shouldVersion) {

                $lastVersion = $page->versions()
                    ->lockForUpdate()
                    ->max('version') ?? 0;

                PageVersion::create([
                    'page_id' => $page->id,
                    'structure' => $page->structure,
                    'meta' => $page->meta,
                    'version' => $lastVersion + 1,
                    'created_by' => $request->user()->id,
                ]);
            }

            $page->update($data);

            return new PageResource($page);
        });
    }

    /**
     * PUBLISH PAGE
     */
    public function publish(Page $page)
    {
        $this->authorize('update', $page);

        $page->update([
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return new PageResource($page);
    }

    /**
     * LISTE DES VERSIONS
     */
    public function versions(Page $page)
    {
        $this->authorize('view', $page);

        return response()->json([
            'data' => $page->versions()
                ->with('author')
                ->orderByDesc('version')
                ->get()
        ]);
    }

    /**
     * RESTORE VERSION
     */
    public function restore(Page $page, PageVersion $version)
    {
        $this->authorize('update', $page);

        if ($version->page_id !== $page->id) {
            return response()->json([
                'message' => 'Invalid version'
            ], 400);
        }

        return DB::transaction(function () use ($page, $version) {

            $lastVersion = $page->versions()
                ->lockForUpdate()
                ->max('version') ?? 0;

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

            return new PageResource($page);
        });
    }

    /**
     * DELETE PAGE
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