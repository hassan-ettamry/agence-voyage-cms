<div
    id="builder-workspace"
    class="
        flex-1
        overflow-auto
        bg-gray-100
        flex
        flex-col
        items-center
        py-6
        px-4
        relative
    "
>

    <div
        id="canvas-wrapper"
        class="
            w-full
            max-w-full
            transition-all
            duration-300
            mx-auto
            relative
        "
    >

        {{-- Overlay Root --}}
        <div
            id="builder-overlay-root"
            class="
                absolute
                inset-0
                pointer-events-none
                z-50
            "
        ></div>

        {{-- Canvas --}}
        <div

            id="canvas"

            data-root-dropzone="true"

            class="
                bg-white
                min-h-[600px]
                border
                border-dashed
                border-gray-300
                shadow-sm
                relative
            "
        >

            @include('builder.components.empty-state')

        </div>

    </div>

</div>
