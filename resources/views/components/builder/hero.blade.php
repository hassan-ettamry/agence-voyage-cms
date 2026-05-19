<section

    @if($isEditor)

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

    @endif

    class="
        px-16
        py-20
        {{ $isEditor ? 'builder-node' : '' }}
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
        @if($isEditor)

        contenteditable="true"
        data-field="title"

        @endif

        class="text-5xl font-bold mb-4 {{ $isEditor ? 'outline-none' : '' }}"
    >

        {{ $props['title'] ?? 'Hero Title' }}

    </h1>

    <p
        @if($isEditor)

        contenteditable="true"
        data-field="description"

        @endif

        class="max-w-xl {{ $isEditor ? 'outline-none' : '' }}"
    >

        {{ $props['description'] ?? '' }}

    </p>

</section>
