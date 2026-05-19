<div

    @if($isEditor)

    data-type="button"

    data-node-id="{{ $nodeId }}"

    draggable="true"

    data-drag-action="reorder"

    @endif

    class="
        p-4
        {{ $isEditor ? 'builder-node' : '' }}
        transition-all
        duration-150
    "
>

    <button

        @if($isEditor)

        contenteditable="true"

        data-field="text"

        @endif

        class="
            px-5
            py-2
            rounded-lg
            {{ $isEditor ? 'outline-none' : '' }}
        "

        style="
            background-color:
                {{ $props['backgroundColor'] ?? '#2563eb' }};

            color:
                {{ $props['textColor'] ?? '#ffffff' }};
        "
    >

        {{ $props['text'] ?? 'Button' }}

    </button>

</div>
