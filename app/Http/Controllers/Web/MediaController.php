<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Models\MediaAsset;
use App\Services\MediaIndexService;
use App\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(
        private MediaService $mediaService,
        private MediaIndexService $mediaIndexService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', MediaAsset::class);

        if ($request->expectsJson()) {
            return response()->json($this->mediaIndexService->picker($request->query()));
        }

        return view('media.index', $this->mediaIndexService->build($request->query(), $request->user()));
    }

    public function picker(Request $request)
    {
        $this->authorize('viewAny', MediaAsset::class);

        return response()->json($this->mediaIndexService->picker($request->query()));
    }

    public function create(Request $request)
    {
        $this->authorize('create', MediaAsset::class);

        return view('media.index', array_merge(
            $this->mediaIndexService->build($request->query(), $request->user()),
            ['openCreateModal' => true]
        ));
    }

    public function store(StoreMediaRequest $request)
    {
        $this->authorize('create', MediaAsset::class);

        $files = $request->file('files', []);
        $files = is_array($files) ? $files : [];
        $singleFile = $request->file('file');

        if ($singleFile) {
            $files = [$singleFile];
        }

        $metadata = collect($request->validated())
            ->except(['file', 'files'])
            ->all();

        if (count($files) > 1) {
            unset($metadata['title'], $metadata['alt_text']);
        }

        $mediaAssets = $this->mediaService->uploadMany($files, $metadata, $request->user());

        if ($request->expectsJson()) {
            $items = $mediaAssets->map(fn (MediaAsset $media) => [
                'id' => $media->id,
                'title' => $media->title,
                'alt_text' => $media->alt_text,
                'url' => $media->url,
                'path' => $media->path,
            ])->values();

            return response()->json($items->count() === 1 ? $items->first() : ['items' => $items], 201);
        }

        $message = $mediaAssets->count() === 1
            ? 'Media ajoute avec succes.'
            : $mediaAssets->count().' medias ajoutes avec succes.';

        return redirect()->route('media.index')->with('success', $message);
    }

    public function edit(MediaAsset $medium)
    {
        $this->authorize('update', $medium);

        return view('media.edit', ['media' => $medium]);
    }

    public function update(UpdateMediaRequest $request, MediaAsset $medium)
    {
        $this->authorize('update', $medium);

        $this->mediaService->update($medium, $request->validated());

        return redirect()->route('media.index')->with('success', 'Media mis a jour.');
    }

    public function destroy(Request $request, MediaAsset $medium)
    {
        $this->authorize('delete', $medium);

        try {
            $this->mediaService->delete($medium);
        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()->route('media.index')->with('success', 'Media supprime.');
    }
}
