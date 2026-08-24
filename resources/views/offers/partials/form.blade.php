@php
    $itinerary = array_values(old('itinerary', $offer?->itinerary ?? []));
    $inclusions = array_values(old('inclusions', $offer?->inclusions ?? []));
    $exclusions = array_values(old('exclusions', $offer?->exclusions ?? []));
    $catalogCurrency = auth()->user()?->agency?->catalogCurrency() ?? 'MAD';
@endphp

<section class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
    <div>
        <h2 class="text-sm font-black text-slate-900">Offer overview</h2>
        <p class="mt-1 text-xs text-slate-500">The essential information displayed in cards and search results.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Title</span>
            <input name="title" value="{{ old('title', $offer->title ?? '') }}" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Slug</span>
            <input name="slug" value="{{ old('slug', $offer->slug ?? '') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
    </div>

    <label class="block">
        <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Short summary</span>
        <textarea name="summary" rows="2" maxlength="320" placeholder="A concise promise for offer cards." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('summary', $offer->summary ?? '') }}</textarea>
    </label>

    <label class="block">
        <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Full description</span>
        <textarea name="description" rows="5" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('description', $offer->description ?? '') }}</textarea>
    </label>

    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Destination</span>
            <select name="destination_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                <option value="">No destination</option>
                @foreach($destinations as $destination)
                    <option value="{{ $destination->id }}" @selected(old('destination_id', $offer->destination_id ?? '') === $destination->id)>{{ $destination->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Cover image</span>
            <select name="media_asset_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                <option value="">Use destination image / Builder fallback</option>
                @foreach($mediaAssets as $media)
                    <option value="{{ $media->id }}" @selected(old('media_asset_id', $offer->media_asset_id ?? '') === $media->id)>{{ $media->title ?: $media->original_name }}</option>
                @endforeach
            </select>
        </label>
    </div>

    @if($mediaAssets->isNotEmpty())
        <div class="grid max-h-48 grid-cols-3 gap-2 overflow-y-auto rounded-xl bg-white p-2 sm:grid-cols-5">
            @foreach($mediaAssets as $media)
                <button type="button" data-offer-media-id="{{ $media->id }}" class="overflow-hidden rounded-lg border border-slate-200 bg-white p-1">
                    <img src="{{ $media->url }}" alt="" class="aspect-video w-full rounded bg-slate-100 object-cover">
                </button>
            @endforeach
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Price from</span>
            <span class="flex overflow-hidden rounded-xl border border-slate-200 bg-white">
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $offer->price ?? '') }}" required class="min-w-0 flex-1 border-0 px-3 py-2.5 text-sm focus:ring-0">
                <span class="grid place-items-center border-l border-slate-200 bg-slate-50 px-3 text-xs font-black text-slate-600">{{ $catalogCurrency }}</span>
            </span>
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Duration days</span>
            <input type="number" min="1" max="365" name="duration_days" value="{{ old('duration_days', $offer->duration_days ?? 1) }}" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </label>
        <label class="block">
            <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Status</span>
            <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                <option value="draft" @selected(old('status', $offer->status ?? 'draft') === 'draft')>Draft</option>
                <option value="published" @selected(old('status', $offer->status ?? '') === 'published')>Published</option>
            </select>
        </label>
    </div>

    <label class="flex items-center gap-2 text-sm font-bold text-slate-600">
        <input type="checkbox" name="is_special" value="1" @checked(old('is_special', $offer->is_special ?? false))>
        Special offer
    </label>
</section>

<section class="space-y-4 rounded-2xl border border-slate-200 p-5" data-itinerary-editor>
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-slate-900">Day-by-day itinerary</h2>
            <p class="mt-1 text-xs text-slate-500">Up to 30 days. Use the arrows to reorder the programme.</p>
        </div>
        <button type="button" data-add-itinerary class="rounded-lg bg-indigo-50 px-3 py-2 text-xs font-black text-indigo-700">Add day</button>
    </div>
    <div data-itinerary-list class="space-y-3">
        @foreach($itinerary as $index => $day)
            <div data-itinerary-row class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid gap-3 md:grid-cols-[90px_1fr_auto]">
                    <input type="number" min="1" max="30" data-field="day" name="itinerary[{{ $index }}][day]" value="{{ $day['day'] ?? '' }}" placeholder="Day" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                    <input data-field="title" name="itinerary[{{ $index }}][title]" value="{{ $day['title'] ?? '' }}" placeholder="Day title" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                    <span class="flex gap-1">
                        <button type="button" data-move-row="up" class="rounded bg-white px-2 text-xs font-black text-slate-500">↑</button>
                        <button type="button" data-move-row="down" class="rounded bg-white px-2 text-xs font-black text-slate-500">↓</button>
                        <button type="button" data-remove-row class="rounded bg-red-50 px-2 text-xs font-black text-red-600">×</button>
                    </span>
                </div>
                <textarea data-field="description" name="itinerary[{{ $index }}][description]" rows="2" placeholder="Activities, transport and highlights" class="mt-3 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">{{ $day['description'] ?? '' }}</textarea>
            </div>
        @endforeach
    </div>
</section>

<div class="grid gap-4 lg:grid-cols-2">
    @foreach(['inclusions' => ['Included', $inclusions], 'exclusions' => ['Not included', $exclusions]] as $field => [$label, $items])
        <section class="space-y-4 rounded-2xl border border-slate-200 p-5" data-list-editor="{{ $field }}">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-black text-slate-900">{{ $label }}</h2>
                <button type="button" data-add-list-item class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-black text-slate-700">Add item</button>
            </div>
            <div data-list-items class="space-y-2">
                @foreach($items as $item)
                    <div data-list-row class="flex gap-2">
                        <input name="{{ $field }}[]" value="{{ $item }}" maxlength="255" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <button type="button" data-remove-list-item class="rounded-lg bg-red-50 px-3 text-sm font-black text-red-600">×</button>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</div>

<section class="rounded-2xl border border-slate-200 p-5">
    <label class="block">
        <span class="mb-1 block text-xs font-semibold uppercase text-slate-500">Practical information</span>
        <textarea name="practical_information" rows="4" placeholder="Required documents, equipment, meeting point and useful notes..." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('practical_information', $offer->practical_information ?? '') }}</textarea>
    </label>
</section>

<template data-itinerary-template>
    <div data-itinerary-row class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <div class="grid gap-3 md:grid-cols-[90px_1fr_auto]">
            <input type="number" min="1" max="30" data-field="day" placeholder="Day" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
            <input data-field="title" placeholder="Day title" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
            <span class="flex gap-1">
                <button type="button" data-move-row="up" class="rounded bg-white px-2 text-xs font-black text-slate-500">↑</button>
                <button type="button" data-move-row="down" class="rounded bg-white px-2 text-xs font-black text-slate-500">↓</button>
                <button type="button" data-remove-row class="rounded bg-red-50 px-2 text-xs font-black text-red-600">×</button>
            </span>
        </div>
        <textarea data-field="description" rows="2" placeholder="Activities, transport and highlights" class="mt-3 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"></textarea>
    </div>
</template>

<script>
    (() => {
        const form = document.currentScript.closest('form');
        if (!form || form.dataset.richOfferReady) return;
        form.dataset.richOfferReady = 'true';

        const editor = form.querySelector('[data-itinerary-editor]');
        const list = editor?.querySelector('[data-itinerary-list]');
        const template = form.querySelector('[data-itinerary-template]');
        const reindex = () => {
            list?.querySelectorAll('[data-itinerary-row]').forEach((row, index) => {
                row.querySelectorAll('[data-field]').forEach((field) => {
                    field.name = `itinerary[${index}][${field.dataset.field}]`;
                });
            });
        };

        editor?.addEventListener('click', (event) => {
            if (event.target.closest('[data-add-itinerary]')) {
                if (list.children.length >= 30) return;
                const row = template.content.firstElementChild.cloneNode(true);
                row.querySelector('[data-field="day"]').value = list.children.length + 1;
                list.append(row);
                reindex();
                return;
            }
            const row = event.target.closest('[data-itinerary-row]');
            if (!row) return;
            if (event.target.closest('[data-remove-row]')) row.remove();
            if (event.target.closest('[data-move-row="up"]') && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
            if (event.target.closest('[data-move-row="down"]') && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
            reindex();
        });

        form.querySelectorAll('[data-list-editor]').forEach((listEditor) => {
            const field = listEditor.dataset.listEditor;
            const items = listEditor.querySelector('[data-list-items]');
            listEditor.addEventListener('click', (event) => {
                if (event.target.closest('[data-add-list-item]')) {
                    if (items.children.length >= 50) return;
                    const row = document.createElement('div');
                    row.dataset.listRow = '';
                    row.className = 'flex gap-2';
                    row.innerHTML = `<input name="${field}[]" maxlength="255" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm"><button type="button" data-remove-list-item class="rounded-lg bg-red-50 px-3 text-sm font-black text-red-600">×</button>`;
                    items.append(row);
                    row.querySelector('input').focus();
                }
                const remove = event.target.closest('[data-remove-list-item]');
                if (remove) remove.closest('[data-list-row]').remove();
            });
        });

        form.querySelectorAll('[data-offer-media-id]').forEach((button) => {
            button.addEventListener('click', () => {
                const select = form.querySelector('select[name="media_asset_id"]');
                select.value = button.dataset.offerMediaId;
                form.querySelectorAll('[data-offer-media-id]').forEach((item) => item.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-200'));
                button.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-200');
            });
        });
        reindex();
    })();
</script>
