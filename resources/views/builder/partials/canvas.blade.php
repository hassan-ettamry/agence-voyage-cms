<div class="flex-1 overflow-auto bg-gray-100 flex flex-col items-center py-6 px-4">

    <div
        id="canvas-wrapper"
        class="w-full max-w-full transition-all duration-300 mx-auto"
    >

        <div
            id="canvas"

            ondragover="
                BuilderDragDrop.allowDrop(event)
            "

            ondrop="
                BuilderDragDrop.drop(event)
            "

            class="
                bg-white
                min-h-[600px]
                border
                border-dashed
                border-gray-300
                shadow-sm
            "
        >

            @include('builder.components.empty-state')

        </div>

    </div>

</div>