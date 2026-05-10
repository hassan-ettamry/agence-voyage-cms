<section

    data-type="section"

    data-node-id="{{ $nodeId }}"

    data-dropzone="true"

    ondragover="
        BuilderDragDrop.allowDrop(event)
    "

    ondrop="
        BuilderDragDrop.drop(event)
    "

    class="
        relative
        w-full
    "

    style="
        background-color:
            {{ $props['backgroundColor'] ?? '#ffffff' }};

        padding-top:
            {{ ($props['paddingTop'] ?? 60) . 'px' }};

        padding-bottom:
            {{ ($props['paddingBottom'] ?? 60) . 'px' }};

        padding-left:
            {{ ($props['paddingLeft'] ?? 20) . 'px' }};

        padding-right:
            {{ ($props['paddingRight'] ?? 20) . 'px' }};
    "
>

    <div

        class="mx-auto"

        style="
            max-width:
                {{ ($props['maxWidth'] ?? 1200) . 'px' }};
        "
    >

        @if(!empty($props['title']))

            <h2
                contenteditable="true"
                data-field="title"
                class="
                    text-3xl
                    font-bold
                    mb-6
                    outline-none
                "
            >

                {{ $props['title'] }}

            </h2>

        @endif

        {!! $children !!}

    </div>

</section>