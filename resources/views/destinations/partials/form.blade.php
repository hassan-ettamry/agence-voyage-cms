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
    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Images</label>
    <select name="media_ids[]" multiple class="min-h-32 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
        @foreach($mediaAssets as $media)
            <option value="{{ $media->id }}" @selected(in_array($media->id, $selectedMedia, true))>{{ $media->title ?: $media->original_name }}</option>
        @endforeach
    </select>
</div>
