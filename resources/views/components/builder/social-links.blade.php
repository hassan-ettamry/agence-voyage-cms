@php
    $props = is_array($props ?? null) ? $props : [];
    $links = collect(preg_split('/\r\n|\r|\n/', (string) ($props['items'] ?? '')))->filter()->map(function ($line) {
        [$network, $url] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        return compact('network', 'url');
    })->filter(fn ($link) => filter_var($link['url'], FILTER_VALIDATE_URL) && in_array(parse_url($link['url'], PHP_URL_SCHEME), ['http', 'https'], true));
    $alignment = in_array(($props['align'] ?? 'left'), ['left', 'center', 'right'], true) ? $props['align'] : 'left';
@endphp
<div @if($isEditor) data-type="social-links" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-container flex flex-wrap gap-3 py-6 {{ $isEditor ? 'builder-node' : '' }} {{ $alignment === 'center' ? 'justify-center' : ($alignment === 'right' ? 'justify-end' : 'justify-start') }}">@foreach($links as $link)<a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="site-button site-button--secondary" aria-label="Visit us on {{ $link['network'] }}">{{ $link['network'] }}</a>@endforeach</div>
