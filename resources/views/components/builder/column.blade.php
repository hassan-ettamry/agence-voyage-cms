<div

    data-type="column"

    data-node-id="{{ $nodeId }}"

    data-dropzone="true"

    ondragover="
        BuilderDragDrop.allowDrop(event)
    "

    ondrop="
        BuilderDragDrop.drop(event)
    "

    class="
        min-h-[120px]
        border
        border-dashed
        border-gray-300
        rounded
        p-4
    "
>

    @if(!empty($children))

        {!! $children !!}

    @else

        <div class="text-xs text-gray-400">
            Drop component here
        </div>

    @endif

</div>