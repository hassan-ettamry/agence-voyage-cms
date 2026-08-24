@php
    $props = is_array($props ?? null) ? $props : [];
    $show = static function (string $key, bool $default = true) use ($props): bool {
        if (!array_key_exists($key, $props)) return $default;
        return !in_array($props[$key], ['no', 'false', false, 0, '0'], true);
    };
    $limit = max(2, min(4, (int) ($props['limit'] ?? 3)));
    $source = $props['source'] ?? 'latest';
    $sort = $props['sort'] ?? 'latest';
    $destinationId = $props['destination_id'] ?? null;
    $manualIds = collect($props['manual_ids'] ?? [])->filter(fn ($id) => is_string($id))->values()->all();
    $continent = trim((string) ($props['continent'] ?? ''));
    $travelType = trim((string) ($props['travelType'] ?? ''));
    $idealMonth = (int) ($props['idealMonth'] ?? 0);
    $query = \App\Models\Offer::published()
        ->with(['agency', 'media', 'destination.media'])
        ->when($continent || $travelType || $idealMonth, fn ($query) => $query->whereHas('destination', fn ($destination) => $destination
            ->published()
            ->inContinent($continent)
            ->ofTravelType($travelType)
            ->idealInMonth($idealMonth ?: null)));
    if ($source === 'special') $query->special();
    if ($destinationId) $query->where('destination_id', $destinationId);
    if ($source === 'manual') $query->whereIn('id', $manualIds ?: ['']);
    match ($sort) {
        'oldest' => $query->oldest(),
        'name' => $query->orderBy('title'),
        'price_low' => $query->orderBy('price'),
        'price_high' => $query->orderByDesc('price'),
        default => $query->latest(),
    };
    $offers = $source === 'manual'
        ? $query->get()->sortBy(fn ($offer) => array_search($offer->id, $manualIds, true))->take($limit)->values()
        : $query->limit($limit)->get();
    $fallbackImage = trim((string) ($props['fallbackImage'] ?? '/images/site-templates/sunset-luxe.png'));
    $showImage = $show('showImage');
    $showTitle = $show('showTitle');
    $showDescription = $show('showDescription');
    $showMeta = $show('showMeta');
    $showDestination = array_key_exists('showDestination', $props) ? $show('showDestination') : $showMeta;
    $showPrice = array_key_exists('showPrice', $props) ? $show('showPrice') : $showMeta;
    $showDuration = array_key_exists('showDuration', $props) ? $show('showDuration') : $showMeta;
    $showCta = $show('showCta');
    $sectionTone = in_array(($props['sectionTone'] ?? 'default'), ['default', 'surface', 'soft', 'dark'], true)
        ? ($props['sectionTone'] ?? 'default')
        : 'default';
    $toneClass = $sectionTone === 'default' ? '' : 'site-section--'.$sectionTone;
@endphp

<section @if($isEditor) data-type="offer-comparison" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $toneClass }} {{ $isEditor ? 'builder-node' : '' }}">
    <div class="site-container">
        @include('components.builder.partials.travel-section-heading', ['props' => $props, 'type' => 'offer-comparison', 'isEditor' => $isEditor])
        <div class="grid gap-5 lg:grid-cols-3">
            @forelse($offers as $offer)
                @php
                    $cover = $offer->media ?: $offer->destination?->media?->first();
                    $coverUrl = $cover?->url
                        ?: (collect($offer->destination?->images)->filter()->first() ?: $fallbackImage);
                @endphp
                <article class="site-card flex flex-col overflow-hidden">
                    @if($showImage && $coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $cover?->alt_text ?? $offer->title }}" loading="lazy" decoding="async" class="aspect-video w-full object-cover">
                    @endif
                    <div class="flex flex-1 flex-col p-6 sm:p-8">
                    @if($offer->is_special)<div class="site-eyebrow">Signature choice</div>@endif
                    @if($showTitle)<h3 class="site-heading mt-3 text-2xl font-bold">{{ $offer->title }}</h3>@endif
                    @if($showDescription)<p class="mt-3 line-clamp-3 leading-7" style="color: var(--site-muted);">{{ $offer->summary ?: $offer->description }}</p>@endif
                    @if($showDestination || $showDuration || $showPrice)
                        <dl class="mt-6 divide-y" style="border-color: var(--site-border);">
                            @if($showDestination)<div class="flex justify-between gap-4 py-3"><dt style="color: var(--site-muted);">Destination</dt><dd class="font-bold">{{ $offer->destination?->name ?? 'Flexible' }}</dd></div>@endif
                            @if($showDuration)<div class="flex justify-between gap-4 py-3"><dt style="color: var(--site-muted);">Duration</dt><dd class="font-bold">{{ $offer->duration_days }} days</dd></div>@endif
                            @if($showPrice)<div class="flex justify-between gap-4 py-3"><dt style="color: var(--site-muted);">From</dt><dd class="font-bold" style="color: var(--site-primary);">{{ number_format((float) $offer->price, 2) }} {{ $offer->agency->catalogCurrency() }}</dd></div>@endif
                        </dl>
                    @endif
                    @if($showCta)<a href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->offer($offer->agency, $offer) }}" @if($isEditor) onclick="return false" @endif class="site-button mt-7">{{ $props['buttonText'] ?? 'View journey' }}</a>@endif
                    </div>
                </article>
            @empty
                <div class="site-empty-state lg:col-span-3" style="color: var(--site-muted);">{{ $isEditor ? 'Add published offers to compare.' : 'New offers are coming soon.' }}</div>
            @endforelse
        </div>
    </div>
</section>
