<section

    @if($isEditor)

    data-type="hero"

    data-node-id="{{ $nodeId }}"

    draggable="true"

    data-drag-action="reorder"

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
            {{ $props['backgroundColor'] ?? 'var(--site-secondary, #111827)' }};

        color:
            {{ $props['textColor'] ?? 'var(--site-background, #ffffff)' }};
    "
>

    <h1
        @if($isEditor)

        contenteditable="true"
        data-field="title"

        @endif

        class="text-5xl font-bold mb-4 {{ $isEditor ? 'outline-none' : '' }}"
        style="font-family: var(--site-heading-font, ui-sans-serif, system-ui, sans-serif);"
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
