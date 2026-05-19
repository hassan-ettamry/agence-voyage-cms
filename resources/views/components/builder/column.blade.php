<div

    @if($isEditor)

    data-type="column"

    data-node-id="{{ $nodeId }}"

    data-dropzone="true"

    draggable="true"

    data-drag-action="reorder"

    @endif

    class="
        {{ $isEditor ? 'min-h-[120px] border border-dashed border-gray-300 rounded p-4 builder-node' : 'p-4' }}
        transition-all
        duration-150
    "
>

    @if(!empty($children))

        {!! $children !!}

    @elseif($isEditor)

        <div class="text-xs text-gray-400">
            Drop component here
        </div>

    @endif

</div>
