<div

    data-type="row"

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
        grid
        p-4
        builder-node
        transition-all
        duration-150
    "

    style="
        grid-template-columns:
            repeat(
                {{ $props['columns'] ?? 2 }},
                minmax(0, 1fr)
            );

        gap:
            {{ ($props['gap'] ?? 16) . 'px' }};
    "
>

    @if(!empty($children))

        {!! $children !!}

    @else

        @for($i = 0; $i < ($props['columns'] ?? 2); $i++)

            <div
                class="
                    bg-gray-100
                    rounded
                    h-32
                    border
                    border-dashed
                    border-gray-300
                "
            ></div>

        @endfor

    @endif

</div>