@php
    $icon = $props['icon'] ?? 'star';
    $size = is_numeric($props['size'] ?? null) ? max(16, min(120, (int) $props['size'])) : 40;
    $padding = is_numeric($props['padding'] ?? null) ? max(0, min(80, (int) $props['padding'])) : 0;
    $radius = is_numeric($props['borderRadius'] ?? null) ? max(0, min(120, (int) $props['borderRadius'])) : 0;
    $align = in_array($props['align'] ?? 'left', ['left', 'center', 'right'], true) ? $props['align'] : 'left';
    $justify = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'][$align];
@endphp

<div
    @if($isEditor)
        data-type="icon"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} flex transition-all duration-150"
    style="justify-content: {{ $justify }}; padding: 12px;"
>
    <span
        class="inline-flex items-center justify-center"
        style="
            width: {{ $size + ($padding * 2) }}px;
            height: {{ $size + ($padding * 2) }}px;
            padding: {{ $padding }}px;
            border-radius: {{ $radius }}px;
            color: {{ $props['color'] ?? 'var(--site-primary, #2563eb)' }};
            background-color: {{ $props['backgroundColor'] ?? 'transparent' }};
        "
    >
        <svg
            class="block"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
            viewBox="0 0 24 24"
            style="width: {{ $size }}px; height: {{ $size }}px;"
            aria-hidden="true"
        >
            @include('components.builder.partials.icon-svg', ['name' => $icon])
        </svg>
    </span>
</div>
