<p

    @if($isEditor)

    data-type="text"

    data-node-id="{{ $nodeId }}"

    draggable="true"

    ondragstart="
        BuilderDragReorder.start(
            event,
            '{{ $nodeId }}'
        )
    "

    ondragend="
        BuilderDragReorder.end(event)
    "

    contenteditable="true"

    data-field="text"

    @endif

    class="
        {{ $isEditor ? 'outline-none builder-node' : '' }}
        transition-all
        duration-150
    "

    style="
        color:
            {{ $props['color'] ?? '#374151' }};

        font-size:
            {{ ($props['fontSize'] ?? 16) . 'px' }};

        font-weight:
            {{ $props['fontWeight'] ?? 400 }};

        line-height:
            {{ $props['lineHeight'] ?? 1.6 }};

        text-align:
            {{ $props['align'] ?? 'left' }};

        padding:
            {{ ($props['padding'] ?? 16) . 'px' }};
    "
>

    {{ $props['text'] ?? 'Text here' }}

</p>
