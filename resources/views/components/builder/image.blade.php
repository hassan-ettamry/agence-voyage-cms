<div

    data-type="image"

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

    class="
        p-4
        builder-node
        transition-all
        duration-150
    "
>

    <div

        class="
            border-2
            rounded-xl
            flex
            items-center
            justify-center
            overflow-hidden
        "

        style="
            height:
                {{ ($props['height'] ?? 256) . 'px' }};

            background-color:
                {{ $props['backgroundColor'] ?? '#f3f4f6' }};

            border-style:
                {{ $props['borderStyle'] ?? 'dashed' }};

            border-color:
                {{ $props['borderColor'] ?? '#d1d5db' }};
        "
    >

        @if(!empty($props['src']))

            <img
                src="{{ $props['src'] }}"
                alt="{{ $props['alt'] ?? '' }}"
                class="w-full h-full object-cover"
            >

        @else

            <div class="text-gray-400 text-sm">

                {{ $props['alt'] ?? 'Image' }}

            </div>

        @endif

    </div>

</div>