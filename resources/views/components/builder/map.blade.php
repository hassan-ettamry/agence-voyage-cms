@php
    $address = trim((string) ($props['address'] ?? 'Marrakech, Morocco'));
    $embedUrl = trim((string) ($props['embedUrl'] ?? ''));
    $height = is_numeric($props['height'] ?? null) ? max(160, min(900, (int) $props['height'])) : 360;
    $zoom = is_numeric($props['zoom'] ?? null) ? max(1, min(20, (int) $props['zoom'])) : 12;
    $radius = is_numeric($props['borderRadius'] ?? null) ? max(0, min(80, (int) $props['borderRadius'])) : 12;
    $standalone = ($props['presentation'] ?? 'embedded') === 'standalone';
    $showDirections = ($props['showDirections'] ?? 'yes') !== 'no';
    $markerLabel = trim((string) ($props['markerLabel'] ?? 'Find us')) ?: 'Find us';
    $safeEmbed = preg_match('/^https?:\/\//i', $embedUrl) === 1 ? $embedUrl : '';
    $mapUrl = $safeEmbed ?: 'https://www.google.com/maps?q='.rawurlencode($address).'&z='.$zoom.'&output=embed';
    $directionsUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($address);
@endphp

<section
    @if($isEditor)
        data-type="map"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} {{ $standalone ? 'site-section pt-0' : 'p-4' }} transition-all duration-150"
>
    <div class="{{ $standalone ? 'site-container' : '' }}">
        @if($standalone)
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <div class="site-eyebrow">VISIT US</div>
                    <h2 class="site-heading mt-3 text-3xl sm:text-4xl">{{ $markerLabel }}</h2>
                    @if($address)<p class="site-copy mt-2" style="color: var(--site-muted);">{{ $address }}</p>@endif
                </div>
                @if($showDirections && $address)
                    <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer" class="site-button site-button--secondary">Open in Maps</a>
                @endif
            </div>
        @endif

        <iframe
            src="{{ $mapUrl }}"
            title="Map: {{ $address ?: 'Location' }}"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            class="block w-full border bg-slate-50"
            style="height: {{ $height }}px; border-color: var(--site-border); border-radius: {{ $radius }}px;"
            allowfullscreen
        ></iframe>
    </div>
</section>
