@php
    $icon = $props['icon'] ?? 'map-pin';
    $layout = in_array($props['layout'] ?? 'horizontal', ['horizontal', 'vertical'], true) ? $props['layout'] : 'horizontal';
    $align = in_array($props['align'] ?? 'left', ['left', 'center', 'right'], true) ? $props['align'] : 'left';
    $size = is_numeric($props['size'] ?? null) ? max(16, min(96, (int) $props['size'])) : 36;
    $gap = is_numeric($props['gap'] ?? null) ? max(0, min(64, (int) $props['gap'])) : 12;
    $direction = $layout === 'vertical' ? 'column' : 'row';
    $items = $layout === 'vertical'
        ? ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'][$align]
        : 'flex-start';
    $textAlign = $align;
@endphp

<div
    @if($isEditor)
        data-type="icon-text"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} flex rounded-[var(--site-radius)] p-4 transition-all duration-150"
    style="
        flex-direction: {{ $direction }};
        align-items: {{ $items }};
        gap: {{ $gap }}px;
        text-align: {{ $textAlign }};
    "
>
    <span
        class="inline-flex shrink-0 items-center justify-center"
        style="color: {{ $props['iconColor'] ?? 'var(--site-primary, #2563eb)' }};"
    >
        <svg
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

    <div class="min-w-0">
        <h3
            @if($isEditor)
                contenteditable="true"
                data-field="title"
            @endif
            class="{{ $isEditor ? 'outline-none' : '' }} site-heading text-lg font-semibold"
            style="color: {{ $props['titleColor'] ?? 'var(--site-text, #111827)' }};"
        >
            {{ $props['title'] ?? 'Icon title' }}
        </h3>

        <p
            @if($isEditor)
                contenteditable="true"
                data-field="text"
            @endif
            class="{{ $isEditor ? 'outline-none' : '' }} mt-1 text-sm leading-6"
            style="color: {{ $props['textColor'] ?? 'var(--site-muted, #64748b)' }};"
        >
            {{ $props['text'] ?? 'Short supporting text.' }}
        </p>
    </div>
</div>
