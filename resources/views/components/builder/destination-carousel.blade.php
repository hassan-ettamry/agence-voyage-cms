@php
    $props = is_array($props ?? null) ? $props : [];
    $show = static function (string $key, bool $default = true) use ($props): bool {
        if (!array_key_exists($key, $props)) return $default;
        return !in_array($props[$key], ['no', 'false', false, 0, '0'], true);
    };
    $limit = max(1, min(12, (int) ($props['limit'] ?? 6)));
    $source = $props['source'] ?? 'featured';
    $sort = $props['sort'] ?? 'latest';
    $manualIds = collect($props['manual_ids'] ?? [])->filter(fn ($id) => is_string($id))->values()->all();
    $query = \App\Models\Destination::published()
        ->with(['agency', 'media'])
        ->inContinent(trim((string) ($props['continent'] ?? '')))
        ->ofTravelType(trim((string) ($props['travelType'] ?? '')))
        ->idealInMonth((int) ($props['idealMonth'] ?? 0) ?: null);
    if ($source === 'featured') $query->featured();
    if ($source === 'manual') $query->whereIn('id', $manualIds ?: ['']);
    match ($sort) {
        'oldest' => $query->oldest(),
        'name' => $query->orderBy('name'),
        default => $query->latest(),
    };
    $destinations = $source === 'manual'
        ? $query->get()->sortBy(fn ($destination) => array_search($destination->id, $manualIds, true))->take($limit)->values()
        : $query->limit($limit)->get();
    $fallbackImage = trim((string) ($props['fallbackImage'] ?? '/images/site-templates/culture-journey.png'));
    $showImage = $show('showImage');
    $showTitle = $show('showTitle');
    $showDescription = $show('showDescription');
    $showMeta = $show('showMeta');
    $showLocation = array_key_exists('showLocation', $props) ? $show('showLocation') : $showMeta;
    $showTravelTypes = array_key_exists('showTravelTypes', $props) ? $show('showTravelTypes') : $showMeta;
    $showCta = $show('showCta');
    $cardVariant = ($props['cardVariant'] ?? 'overlay') === 'compact' ? 'compact' : 'overlay';
    $cardHeight = $cardVariant === 'compact' ? 'min-h-[340px]' : 'min-h-[420px]';
    $gap = max(8, min(48, (int) ($props['gap'] ?? 20)));
@endphp

<section @if($isEditor) data-type="destination-carousel" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section overflow-hidden {{ $isEditor ? 'builder-node' : '' }}">
    <div class="site-container">
        @if(!empty($props['title']))
            <h2 class="site-heading text-[clamp(2.25rem,5vw,4rem)] font-bold">{{ $props['title'] }}</h2>
        @endif
        <div class="mt-9 flex snap-x snap-mandatory overflow-x-auto pb-6" style="scrollbar-width: thin; gap: {{ $gap }}px;">
            @forelse($destinations as $destination)
                @php
                    $cover = $destination->media->first();
                    $coverUrl = $cover?->url ?: (collect($destination->images)->filter()->first() ?: $fallbackImage);
                @endphp
                <a href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->destination($destination->agency, $destination) }}" @if($isEditor) onclick="return false" @endif class="site-card group relative {{ $cardHeight }} min-w-[82%] snap-start overflow-hidden no-underline sm:min-w-[48%] lg:min-w-[31%]">
                    @if($showImage && $coverUrl)<img src="{{ $coverUrl }}" alt="{{ $cover?->alt_text ?? $destination->name }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">@endif
                    <div class="absolute inset-0 {{ $showImage ? 'bg-gradient-to-t from-black/85 via-black/15 to-transparent' : '' }}" style="{{ $showImage ? '' : 'background: var(--site-secondary, #12372f);' }}"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                        @if($showLocation)<div class="text-xs font-bold uppercase tracking-[.15em] text-white/70">{{ collect([$destination->region, $destination->country])->filter()->join(', ') }}</div>@endif
                        @if($showTitle)<h3 class="site-heading mt-2 text-3xl font-bold">{{ $destination->name }}</h3>@endif
                        @if($showDescription)<p class="mt-2 line-clamp-2 text-sm text-white/80">{{ $destination->description }}</p>@endif
                        @if($showTravelTypes && $destination->travel_types)<p class="mt-2 text-sm text-white/80">{{ collect(array_slice($destination->travel_types, 0, 2))->map(fn ($type) => \App\Support\TravelCatalog::travelTypeLabel($type))->join(' · ') }}</p>@endif
                        @if($showCta)<span class="mt-4 inline-flex text-sm font-bold">{{ $props['buttonText'] ?? 'View destination' }} &rarr;</span>@endif
                    </div>
                </a>
            @empty
                <div class="site-card w-full border-dashed p-8 text-center" style="color: var(--site-muted);">{{ $isEditor ? 'Add published destinations to populate this carousel.' : 'New destinations are coming soon.' }}</div>
            @endforelse
        </div>
    </div>
</section>
