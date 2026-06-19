@php
    $address = trim((string) ($props['address'] ?? 'Marrakech, Morocco'));
    $embedUrl = trim((string) ($props['embedUrl'] ?? ''));
    $height = is_numeric($props['height'] ?? null) ? max(160, min(900, (int) $props['height'])) : 360;
    $zoom = is_numeric($props['zoom'] ?? null) ? max(1, min(20, (int) $props['zoom'])) : 12;
    $radius = is_numeric($props['borderRadius'] ?? null) ? max(0, min(80, (int) $props['borderRadius'])) : 12;

    $safeEmbed = preg_match('/^https?:\/\//i', $embedUrl) === 1 ? $embedUrl : '';
    $mapUrl = $safeEmbed ?: 'https://www.google.com/maps?q='.rawurlencode($address).'&z='.$zoom.'&output=embed';
@endphp

<div
    @if($isEditor)
        data-type="map"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} p-4 transition-all duration-150"
>
    <iframe
        src="{{ $mapUrl }}"
        title="Map: {{ $address ?: 'Location' }}"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        class="block w-full border border-slate-200 bg-slate-50"
        style="height: {{ $height }}px; border-radius: {{ $radius }}px;"
        allowfullscreen
    ></iframe>
</div>
