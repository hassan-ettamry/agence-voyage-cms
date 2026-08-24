@php
    use App\Support\TravelCatalog;

    $selectedMedia = array_values(old('media_ids', $destination?->media?->pluck('id')->all() ?? []));
    $selectedTypes = array_values(old('travel_types', $destination?->travel_types ?? []));
    $selectedMonths = array_map('strval', array_values(old('ideal_months', $destination?->ideal_months ?? [])));
    $orderedMedia = collect($selectedMedia)
        ->map(fn ($id) => $mediaAssets->firstWhere('id', $id))
        ->filter()
        ->concat($mediaAssets->reject(fn ($media) => in_array($media->id, $selectedMedia, true)));
@endphp

<section class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
    <div>
        <h2 class="text-sm font-black text-slate-900">General information</h2>
        <p class="mt-1 text-xs text-slate-500">Name, public address and destination story.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Name</span>
            <input name="name" value="{{ old('name', $destination->name ?? '') }}" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Slug</span>
            <input name="slug" value="{{ old('slug', $destination->slug ?? '') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Country</span>
            <input name="country" value="{{ old('country', $destination->country ?? '') }}" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Continent</span>
            <select name="continent" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                <option value="">Select a continent</option>
                @foreach(TravelCatalog::CONTINENTS as $value => $label)
                    <option value="{{ $value }}" @selected(old('continent', $destination->continent ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Region</span>
            <input name="region" value="{{ old('region', $destination->region ?? '') }}" placeholder="Atlas, Algarve, Tuscany..." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
    </div>

    <label class="block">
        <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Description</span>
        <textarea name="description" rows="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-indigo-400 focus:outline-none">{{ old('description', $destination->description ?? '') }}</textarea>
    </label>
</section>

<section class="space-y-5 rounded-2xl border border-slate-200 p-5">
    <div>
        <h2 class="text-sm font-black text-slate-900">Travel profile</h2>
        <p class="mt-1 text-xs text-slate-500">Classify the experience and indicate the best months to visit.</p>
    </div>

    <fieldset>
        <legend class="mb-2 text-xs font-semibold uppercase text-slate-500">Travel types</legend>
        <div class="flex flex-wrap gap-2">
            @foreach(TravelCatalog::TRAVEL_TYPES as $value => $label)
                <label class="cursor-pointer rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-700">
                    <input type="checkbox" name="travel_types[]" value="{{ $value }}" @checked(in_array($value, $selectedTypes, true)) class="sr-only">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </fieldset>

    <fieldset>
        <legend class="mb-2 text-xs font-semibold uppercase text-slate-500">Ideal months</legend>
        <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-6">
            @foreach(TravelCatalog::MONTHS as $value => $label)
                <label class="cursor-pointer rounded-lg border border-slate-200 bg-white px-2 py-2 text-center text-xs font-bold text-slate-600 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                    <input type="checkbox" name="ideal_months[]" value="{{ $value }}" @checked(in_array((string) $value, $selectedMonths, true)) class="sr-only">
                    {{ str($label)->substr(0, 3) }}
                </label>
            @endforeach
        </div>
    </fieldset>

    <label class="block">
        <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Practical information</span>
        <textarea name="practical_information" rows="4" placeholder="Visa, transport, climate, local advice..." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('practical_information', $destination->practical_information ?? '') }}</textarea>
    </label>

    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Latitude</span>
            <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $destination->latitude ?? '') }}" placeholder="31.6294723" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Longitude</span>
            <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $destination->longitude ?? '') }}" placeholder="-7.9810845" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
    </div>
</section>

<section class="space-y-4 rounded-2xl border border-slate-200 p-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-sm font-black text-slate-900">Gallery</h2>
            <p class="mt-1 text-xs text-slate-500">Use the arrow buttons to set the order. The first selected image is the cover.</p>
        </div>
        <a href="{{ route('media.index') }}" class="shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800">Manage media</a>
    </div>

    @if($mediaAssets->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center text-sm text-slate-600">
            Upload an image in the Media Library first, then return here to select it.
        </div>
    @else
        <div data-destination-media-list class="grid max-h-[28rem] grid-cols-2 gap-3 overflow-y-auto rounded-xl bg-slate-50 p-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($orderedMedia as $media)
                <label data-media-card class="group relative cursor-pointer overflow-hidden rounded-xl border bg-white has-[:checked]:border-indigo-500 has-[:checked]:ring-2 has-[:checked]:ring-indigo-200">
                    <input type="checkbox" name="media_ids[]" value="{{ $media->id }}" @checked(in_array($media->id, $selectedMedia, true)) class="absolute left-2 top-2 z-10 h-4 w-4 rounded border-white text-indigo-600 shadow">
                    <span data-cover-label class="absolute right-2 top-2 z-10 hidden rounded-full bg-slate-950/85 px-2 py-1 text-[10px] font-black uppercase text-white">Cover</span>
                    <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: ($media->title ?: $media->original_name) }}" class="aspect-video w-full bg-slate-100 object-cover">
                    <span class="block truncate px-2 py-2 text-xs font-medium text-slate-700">{{ $media->title ?: $media->original_name }}</span>
                    <span class="flex gap-1 border-t border-slate-100 p-2">
                        <button type="button" data-media-move="up" class="flex-1 rounded bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">←</button>
                        <button type="button" data-media-move="down" class="flex-1 rounded bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">→</button>
                    </span>
                </label>
            @endforeach
        </div>
    @endif
</section>

<section class="grid gap-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-5 md:grid-cols-2">
    <label class="block">
        <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Status</span>
        <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <option value="draft" @selected(old('status', $destination->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $destination->status ?? '') === 'published')>Published</option>
        </select>
    </label>
    <label class="mt-7 flex items-center gap-2 text-sm font-bold text-slate-600">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $destination->is_featured ?? false))>
        Featured destination
    </label>
</section>

@if($mediaAssets->isNotEmpty())
    <script>
        (() => {
            const form = document.currentScript.closest('form');
            const list = form?.querySelector('[data-destination-media-list]');
            if (!list || list.dataset.ready) return;
            list.dataset.ready = 'true';

            const updateCover = () => {
                list.querySelectorAll('[data-cover-label]').forEach((label) => label.classList.add('hidden'));
                const firstChecked = [...list.querySelectorAll('[data-media-card]')]
                    .find((card) => card.querySelector('input[type="checkbox"]')?.checked);
                firstChecked?.querySelector('[data-cover-label]')?.classList.remove('hidden');
            };

            list.addEventListener('click', (event) => {
                const button = event.target.closest('[data-media-move]');
                if (!button) return;
                event.preventDefault();
                const card = button.closest('[data-media-card]');
                if (button.dataset.mediaMove === 'up' && card.previousElementSibling) list.insertBefore(card, card.previousElementSibling);
                if (button.dataset.mediaMove === 'down' && card.nextElementSibling) list.insertBefore(card.nextElementSibling, card);
                updateCover();
            });
            list.addEventListener('change', updateCover);
            updateCover();
        })();
    </script>
@endif
