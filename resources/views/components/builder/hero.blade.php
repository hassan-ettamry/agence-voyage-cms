<section

    data-type="hero"

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
        px-16
        py-20
        builder-node
        transition-all
        duration-150
    "

    style="
        background-color:
            {{ $props['backgroundColor'] ?? '#111827' }};

        color:
            {{ $props['textColor'] ?? '#ffffff' }};
    "
>

    <h1
        contenteditable="true"
        data-field="title"
        class="text-5xl font-bold mb-4 outline-none"
    >

        {{ $props['title'] ?? 'Hero Title' }}

    </h1>

    <p
        contenteditable="true"
        data-field="description"
        class="max-w-xl outline-none"
    >

        {{ $props['description'] ?? '' }}

    </p>

</section>