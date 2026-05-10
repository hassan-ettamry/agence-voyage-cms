<div

    data-type="container"

    data-node-id="{{ $nodeId }}"

    data-dropzone="true"

    ondragover="
        BuilderDragDrop.allowDrop(event)
    "

    ondrop="
        BuilderDragDrop.drop(event)
    "

    class="
        w-full
        mx-auto
        min-h-[120px]
    "

    style="
        max-width:
            {{ ($props['maxWidth'] ?? 1200) . 'px' }};

        padding-top:
            {{ ($props['paddingTop'] ?? 0) . 'px' }};

        padding-bottom:
            {{ ($props['paddingBottom'] ?? 0) . 'px' }};

        padding-left:
            {{ ($props['paddingLeft'] ?? 0) . 'px' }};

        padding-right:
            {{ ($props['paddingRight'] ?? 0) . 'px' }};

        background-color:
            {{ $props['backgroundColor'] ?? 'transparent' }};
    "
>

    @if(!empty($children))

        {!! $children !!}

    @else

        <div
            class="
                min-h-[120px]
                border
                border-dashed
                border-gray-300
                rounded-lg
                flex
                items-center
                justify-center
                text-sm
                text-gray-400
            "
        >

            Container

        </div>

    @endif

</div>