@php
    $props = is_array($props ?? null) ? $props : [];

    $show = static function (string $key, bool $default = true) use ($props): bool {
        if (!array_key_exists($key, $props)) {
            return $default;
        }

        return !in_array($props[$key], ['no', 'false', false, 0, '0'], true);
    };

    $limit = max(1, min((int) ($props['limit'] ?? 6), 12));
    $columns = max(1, min((int) ($props['columns'] ?? 3), 4));
    $gap = max(8, min((int) ($props['gap'] ?? 24), 64));
    $source = $props['source'] ?? 'latest';
    $sort = $props['sort'] ?? 'latest';
    $destinationId = $props['destination_id'] ?? null;
    $continent = trim((string) ($props['continent'] ?? ''));
    $travelType = trim((string) ($props['travelType'] ?? ''));
    $idealMonth = (int) ($props['idealMonth'] ?? 0);
    $manualIds = collect($props['manual_ids'] ?? [])->filter(fn ($id) => is_string($id))->values()->all();

    $query = \App\Models\Offer::published()->with(['agency', 'destination.media', 'media']);

    if ($source === 'special') {
        $query->special();
    }

    if ($destinationId) {
        $query->where('destination_id', $destinationId);
    }

    if ($source === 'manual') {
        $query->whereIn('id', $manualIds ?: ['']);
    }

    if ($continent || $travelType || $idealMonth) {
        $query->whereHas('destination', fn ($destination) => $destination
            ->published()
            ->inContinent($continent)
            ->ofTravelType($travelType)
            ->idealInMonth($idealMonth ?: null));
    }

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

    $showImage = $show('showImage');
    $showTitle = $show('showTitle');
    $showDescription = $show('showDescription');
    $showMeta = $show('showMeta');
    $showDestination = array_key_exists('showDestination', $props) ? $show('showDestination') : $showMeta;
    $showPrice = array_key_exists('showPrice', $props) ? $show('showPrice') : $showMeta;
    $showDuration = array_key_exists('showDuration', $props) ? $show('showDuration') : $showMeta;
    $showCta = $show('showCta');
    $buttonText = $props['buttonText'] ?? 'View offer';
    $cardVariant = in_array(($props['cardVariant'] ?? 'standard'), ['standard', 'compact', 'deal'], true)
        ? ($props['cardVariant'] ?? 'standard')
        : 'standard';
    $cardPadding = $cardVariant === 'compact' ? 'p-4' : 'p-5';
    $imageRatio = match ($props['imageRatio'] ?? '16/9') {
        'square' => 'aspect-square',
        '4/3' => 'aspect-[4/3]',
        default => 'aspect-video',
    };
    $fallbackImage = trim((string) ($props['fallbackImage'] ?? '/images/site-templates/sunset-luxe.png'));
    $sectionPadding = is_numeric($props['padding'] ?? null)
        ? max(0, min((int) $props['padding'], 120))
        : null;
    $marginTop = is_numeric($props['marginTop'] ?? null) ? (int) $props['marginTop'] : 0;
    $marginBottom = is_numeric($props['marginBottom'] ?? null) ? (int) $props['marginBottom'] : 0;
    $sectionTone = in_array(($props['sectionTone'] ?? 'default'), ['default', 'surface', 'soft', 'dark'], true)
        ? ($props['sectionTone'] ?? 'default')
        : 'default';
    $toneClass = $sectionTone === 'default' ? '' : 'site-section--'.$sectionTone;
@endphp

<section
    data-node-id="{{ $nodeId }}"
    data-type="{{ $type }}"
    class="site-section {{ $toneClass }} {{ $isEditor ? 'builder-node' : '' }}"
    style="@if($sectionPadding !== null) padding-top: {{ $sectionPadding }}px; padding-bottom: {{ $sectionPadding }}px; @endif margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;"
>
    <div class="site-container">
    @include('components.builder.partials.travel-section-heading', compact('props', 'type', 'isEditor'))

    @if($offers->isEmpty())
        <div class="site-empty-state text-sm" style="color: var(--site-muted);">
            {{ $isEditor ? 'No published offers match this source.' : 'New travel offers are coming soon.' }}
        </div>
    @endif

    <div class="site-card-grid grid" style="--site-card-columns: {{ $columns }}; gap: {{ $gap }}px;">
        @foreach($offers as $offer)
            @php
                $cover = $offer->media ?: $offer->destination?->media?->first();
                $coverUrl = $cover?->url
                    ?: (collect($offer->destination?->images)->filter()->first() ?: $fallbackImage);
            @endphp
            <a
                href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->offer($offer->agency, $offer) }}"
                @if($isEditor) onclick="return false" @endif
                class="site-card group overflow-hidden no-underline"
                style="background-color: var(--site-surface, #ffffff); border-color: {{ $cardVariant === 'deal' ? 'var(--site-accent, #e9bd62)' : 'var(--site-border, #f3f4f6)' }}; border-radius: var(--site-radius, 14px); box-shadow: {{ $cardVariant === 'deal' ? '0 16px 40px rgba(15, 23, 42, .12)' : 'var(--site-shadow, none)' }};"
            >
                @if($showImage)
                    <div class="{{ $imageRatio }}" style="background-color: color-mix(in srgb, var(--site-accent, #38bdf8) 14%, white);">
                        @if($coverUrl)
                            <img src="{{ $coverUrl }}" alt="{{ $cover?->alt_text ?? $offer->title }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @elseif($isEditor)
                            <div class="flex h-full items-center justify-center text-sm" style="color: var(--site-muted, #94a3b8);">
                                Offer image
                            </div>
                        @endif
                    </div>
                @endif

                <div class="{{ $cardPadding }}">
                    <div class="flex items-start justify-between gap-3">
                        @if($showTitle)
                            <h3 class="site-heading text-xl font-semibold" style="color: var(--site-text, #111827);">
                                {{ $offer->title }}
                            </h3>
                        @endif

                        @if($offer->is_special)
                            <span class="rounded-full px-2 py-1 text-xs font-semibold" style="background-color: var(--site-accent, #e9bd62); color: var(--site-secondary, #12372f);">
                                Special
                            </span>
                        @endif
                    </div>

                    @if($showDestination && $offer->destination)
                        <div class="mt-2 text-xs font-semibold uppercase tracking-wide" style="color: var(--site-primary, #059669);">
                            {{ $offer->destination->name }}
                        </div>
                    @endif

                    @if($showDescription)
                        <p class="mt-2 line-clamp-2 text-sm" style="color: var(--site-muted, #6b7280);">
                            {{ $offer->summary ?: $offer->description }}
                        </p>
                    @endif

                    @if($showPrice || $showDuration)
                        <div class="mt-4 flex items-center justify-between text-sm">
                            @if($showPrice)
                                <span class="font-semibold" style="color: var(--site-primary, #059669);">
                                    {{ number_format((float) $offer->price, 2) }} {{ $offer->agency->catalogCurrency() }}
                                </span>
                            @endif
                            @if($showDuration)
                                <span style="color: var(--site-muted, #9ca3af);">
                                    {{ $offer->duration_days }} days
                                </span>
                            @endif
                        </div>
                    @endif

                    @if($showCta)
                        <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold" style="color: var(--site-primary, #2563eb);">
                            {{ $buttonText }} <span aria-hidden="true">&rarr;</span>
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
    </div>
</section>
