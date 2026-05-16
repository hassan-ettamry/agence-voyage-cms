<div

    data-type="button"

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

    <button

        contenteditable="true"

        data-field="text"

        class="
            px-5
            py-2
            rounded-lg
            outline-none
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