@php
    $props = is_array($props ?? null) ? $props : [];

    $show = static function (string $key, bool $default = true) use ($props): bool {
        if (!array_key_exists($key, $props)) {
            return $default;
        }

        return !in_array($props[$key], ['no', 'false', false, 0, '0'], true);
    };

    $offer = null;

    if (!empty($props['offer_id'])) {
        $offer = \App\Models\Offer::published()
            ->with(['destination.media', 'media'])
            ->where('id', $props['offer_id'])
            ->first();
    }

    $offer ??= \App\Models\Offer::published()
        ->with(['destination.media', 'media'])
        ->latest()
        ->first();

    $showImage = $show('showImage');
    $showTitle = $show('showTitle');
    $showDescription = $show('showDescription');
    $showMeta = $show('showMeta');
    $showCta = $show('showCta');
    $buttonText = $props['buttonText'] ?? 'View offer';
    $fallbackImage = trim((string) ($props['fallbackImage'] ?? '/images/site-templates/sunset-luxe.png'));
@endphp

<div data-node-id="{{ $nodeId }}" data-type="{{ $type }}" class="py-6">
    @if($offer)
        @php
            $cover = $offer->media ?: $offer->destination?->media?->first();
            $coverUrl = $cover?->url
                ?: (collect($offer->destination?->images)->filter()->first() ?: $fallbackImage);
        @endphp
        <a
            href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->offer($offer->agency, $offer) }}"
            @if($isEditor) onclick="return false" @endif
            class="site-card group block max-w-sm overflow-hidden no-underline"
            style="background-color: var(--site-surface, #ffffff); border-color: var(--site-border, #f3f4f6); border-radius: var(--site-radius, 14px); box-shadow: var(--site-shadow, none);"
        >
            @if($showImage)
                <div class="aspect-video" style="background-color: color-mix(in srgb, var(--site-accent, #38bdf8) 14%, white);">
                    @if($coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $cover?->alt_text ?? $offer->title }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @elseif($isEditor)
                        <div class="flex h-full items-center justify-center text-sm" style="color: var(--site-muted, #94a3b8);">
                            Offer image
                        </div>
                    @endif
                </div>
            @endif

            <div class="p-5">
                @if($showTitle)
                    <h3 class="site-heading text-xl font-semibold" style="color: var(--site-text, #111827);">
                        {{ $offer->title }}
                    </h3>
                @endif

                @if($showDescription)
                    <p class="mt-2 line-clamp-2 text-sm" style="color: var(--site-muted, #6b7280);">
                        {{ $offer->description }}
                    </p>
                @endif

                @if($showMeta)
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="font-semibold" style="color: var(--site-primary, #059669);">
                            {{ number_format((float) $offer->price, 2) }}
                        </span>
                        <span style="color: var(--site-muted, #9ca3af);">
                            {{ $offer->duration_days }} days
                        </span>
                    </div>
                @endif

                @if($showCta)
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold" style="color: var(--site-primary, #2563eb);">
                        {{ $buttonText }} <span aria-hidden="true">&rarr;</span>
                    </span>
                @endif
            </div>
        </a>
    @elseif($isEditor)
        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-400">
            No published offer available.
        </div>
    @endif
</div>
