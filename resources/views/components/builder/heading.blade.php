@php
    $candidateTag = $props['tag'] ?? 'h2';
    $tag = in_array($candidateTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)
        ? $candidateTag
        : 'h2';
@endphp

<{{ $tag }}

    @if($isEditor)

    data-type="heading"

    data-node-id="{{ $nodeId }}"

    draggable="true"

    data-drag-action="reorder"

    contenteditable="true"

    data-field="text"

    @endif

    class="{{ $isEditor ? 'outline-none builder-node' : '' }} site-heading transition-all duration-150"

    style="
        color: {{ $props['color'] ?? 'var(--site-text, #111827)' }};
        font-family: var(--site-heading-font, ui-sans-serif, system-ui, sans-serif);
        font-size: {{ ($props['fontSize'] ?? 36) . 'px' }};
        font-weight: {{ $props['fontWeight'] ?? 700 }};
        line-height: {{ $props['lineHeight'] ?? 1.2 }};
        text-align: {{ $props['align'] ?? 'left' }};
        padding: {{ ($props['padding'] ?? 0) . 'px' }};
    "
>
    {{ $props['text'] ?? 'Heading Title' }}
</{{ $tag }}>
