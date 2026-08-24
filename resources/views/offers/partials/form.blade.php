<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Title</label>
        <input name="title" value="{{ old('title', $offer->title ?? '') }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Slug</label>
        <input name="slug" value="{{ old('slug', $offer->slug ?? '') }}" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
    </div>
</div>

<div>
    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Description</label>
    <textarea name="description" rows="4" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">{{ old('description', $offer->description ?? '') }}</textarea>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Destination</label>
        <select name="destination_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">No destination</option>
            @foreach($destinations as $destination)
                <option value="{{ $destination->id }}" @selected(old('destination_id', $offer->destination_id ?? '') === $destination->id)>{{ $destination->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <div class="mb-1 flex items-center justify-between gap-3">
            <label class="block text-xs font-semibold uppercase text-gray-500">Image</label>
            <a href="{{ route('media.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Manage media</a>
        </div>
        <select name="media_asset_id" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="">Use destination image / Builder fallback</option>
            @foreach($mediaAssets as $media)
                <option value="{{ $media->id }}" @selected(old('media_asset_id', $offer->media_asset_id ?? '') === $media->id)>{{ $media->title ?: $media->original_name }}</option>
            @endforeach
        </select>
        @if($mediaAssets->isNotEmpty())
            <div class="mt-3 grid max-h-44 grid-cols-3 gap-2 overflow-y-auto">
                @foreach($mediaAssets as $media)
                    <button type="button" data-offer-media-id="{{ $media->id }}" class="overflow-hidden rounded-lg border border-gray-200 bg-white text-left hover:border-indigo-400" title="Use {{ $media->title ?: $media->original_name }}">
                        <img src="{{ $media->url }}" alt="" class="aspect-video w-full bg-gray-100 object-cover">
                    </button>
                @endforeach
            </div>
            <script>
                document.querySelectorAll('[data-offer-media-id]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const select = button.closest('div').parentElement.querySelector('select[name="media_asset_id"]');
                        select.value = button.dataset.offerMediaId;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        button.closest('div').querySelectorAll('button').forEach((item) => item.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-200'));
                        button.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-200');
                    });
                });
            </script>
        @endif
    </div>
</div>

<div class="grid gap-4 md:grid-cols-3">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Price</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $offer->price ?? '') }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Duration Days</label>
        <input type="number" min="1" name="duration_days" value="{{ old('duration_days', $offer->duration_days ?? 1) }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Status</label>
        <select name="status" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
            <option value="draft" @selected(old('status', $offer->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $offer->status ?? '') === 'published')>Published</option>
        </select>
    </div>
</div>

<label class="flex items-center gap-2 text-sm font-medium text-gray-600">
    <input type="checkbox" name="is_special" value="1" @checked(old('is_special', $offer->is_special ?? false))>
    Special offer
</label>
