@php
    $rawUrl = trim((string) ($props['url'] ?? ''));
    $title = $props['title'] ?? 'Embedded content';
    $height = is_numeric($props['height'] ?? null) ? max(160, min(1200, (int) $props['height'])) : 420;
    $radius = is_numeric($props['borderRadius'] ?? null) ? max(0, min(80, (int) $props['borderRadius'])) : 12;
    $allowFullscreen = ($props['allowFullscreen'] ?? 'yes') === 'yes';
    $safeUrl = preg_match('/^https?:\/\//i', $rawUrl) === 1 ? $rawUrl : '';
@endphp

<div
    @if($isEditor)
        data-type="iframe"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} p-4 transition-all duration-150"
>
    @if($safeUrl)
        <iframe
            src="{{ $safeUrl }}"
            title="{{ $title }}"
            loading="lazy"
            class="block w-full border border-slate-200 bg-slate-50"
            style="height: {{ $height }}px; border-radius: {{ $radius }}px;"
            @if($allowFullscreen)
                allowfullscreen
            @endif
        ></iframe>
    @elseif($isEditor)
        <div
            class="flex w-full items-center justify-center border border-dashed border-slate-300 bg-slate-50 text-sm text-slate-400"
            style="height: {{ $height }}px; border-radius: {{ $radius }}px;"
        >
            Add an HTTPS embed URL
        </div>
    @endif
</div>
