@php
    $html = $props['html'] ?? $props['content']
        ?? 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Mollitia, quis! Iste debitis id nulla, nesciunt minus enim, ea sed, doloremque inventore tenetur magnam minima fugit sint consequatur repudiandae! Numquam, perspiciatis.';

    $allowed = '<p><br><strong><b><em><i><u><s><sub><sup><h1><h2><h3><h4><ul><ol><li><blockquote><a><span><div><pre><code><table><thead><tbody><tr><th><td><img><iframe><video><audio><source>';
    $html = strip_tags($html, $allowed);
@endphp

<div
    @if($isEditor)
        data-type="richtext"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
        contenteditable="true"
        data-field="html"
        data-richtext-canvas="true"
    @endif
    class="
        {{ $isEditor ? 'outline-none builder-node' : '' }}
        transition-all
        duration-150
        richtext-content site-richtext
    "
    style="
        color: {{ $props['color'] ?? 'var(--site-text, #374151)' }};
        font-size: {{ ($props['fontSize'] ?? 16) . 'px' }};
        font-weight: {{ $props['fontWeight'] ?? 400 }};
        line-height: {{ $props['lineHeight'] ?? 1.65 }};
        text-align: {{ $props['align'] ?? 'left' }};
        padding: {{ ($props['padding'] ?? 16) . 'px' }};
    "
>
    {!! $html !!}
</div>
