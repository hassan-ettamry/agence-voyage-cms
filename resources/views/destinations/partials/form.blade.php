@php
    $selectedMedia = old('media_ids', $destination?->media?->pluck('id')->all() ?? []);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Name</label>
        <input name="name" value="{{ old('name', $destination->name ?? '') }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Slug</label>
        <input name="slug" value="{{ old('slug', $destination->slug ?? '') }}" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
    </div>
</div>

<div>
    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Country</label>
    <input name="country" value="{{ old('country', $destination->country ?? '') }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
</div>

<div>
    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Description</label>
    <textarea name="description" rows="4" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none">{{ old('description', $destination->description ?? '') }}</textarea>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Status</label>
        <select name="status" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="draft" @selected(old('status', $destination->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $destination->status ?? '') === 'published')>Published</option>
        </select>
    </div>
    <label class="mt-7 flex items-center gap-2 text-sm font-medium text-gray-600">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $destination->is_featured ?? false))>
        Featured destination
    </label>
</div>

<div>
    <div class="mb-3 flex items-center justify-between gap-3">
        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500">Images</label>
            <p class="mt-1 text-xs text-gray-500">Select one or more images. The first selected image is used as the card cover.</p>
        </div>
        <a href="{{ route('media.index') }}" class="shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800">Manage media</a>
    </div>
    @if($mediaAssets->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-5 text-center text-sm text-gray-600">
            Upload an image in the Media Library first, then return here to select it.
        </div>
    @else
        <div class="grid max-h-80 grid-cols-2 gap-3 overflow-y-auto rounded-xl border border-gray-200 p-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($mediaAssets as $media)
                <label class="group relative cursor-pointer overflow-hidden rounded-lg border bg-white has-[:checked]:border-indigo-500 has-[:checked]:ring-2 has-[:checked]:ring-indigo-200">
                    <input type="checkbox" name="media_ids[]" value="{{ $media->id }}" @checked(in_array($media->id, $selectedMedia, true)) class="absolute left-2 top-2 z-10 h-4 w-4 rounded border-white text-indigo-600 shadow">
                    <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: ($media->title ?: $media->original_name) }}" class="aspect-video w-full bg-gray-100 object-cover">
                    <span class="block truncate px-2 py-2 text-xs font-medium text-gray-700">{{ $media->title ?: $media->original_name }}</span>
                </label>
            @endforeach
        </div>
    @endif
</div>
