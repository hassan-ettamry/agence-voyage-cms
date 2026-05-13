window.BuilderCanvas = {

    /*
    |--------------------------------------------------------------------------
    | Render Canvas
    |--------------------------------------------------------------------------
    */

    async render() {

        const canvas =
            BuilderCanvasUtils.getCanvas();

        if (!canvas) return;

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        if (!Builder.getStructure().length) {

            canvas.innerHTML = `

                <div
                    id="canvas-empty-state"
                    class="
                        h-[500px]
                        flex
                        items-center
                        justify-center
                        text-gray-400
                    "
                >

                    Drag components here

                </div>

            `;

            BuilderOverlay.hide();

            return;

        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Request HTML
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(

                    '/builder/render',

                    {

                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':

                                document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .content

                        },

                        body: JSON.stringify({

                            structure:
                                Builder.getStructure()

                        })

                    }

                );

            /*
            |--------------------------------------------------------------------------
            | HTML
            |--------------------------------------------------------------------------
            */

            const html =
                await response.text();

            /*
            |--------------------------------------------------------------------------
            | Inject
            |--------------------------------------------------------------------------
            */

            canvas.innerHTML = html;

            /*
            |--------------------------------------------------------------------------
            | Bind Events
            |--------------------------------------------------------------------------
            */

            BuilderCanvasEvents.bind();

            /*
            |--------------------------------------------------------------------------
            | Render Layers
            |--------------------------------------------------------------------------
            */

            BuilderRightSidebar.render();

            /*
            |--------------------------------------------------------------------------
            | Restore Selection
            |--------------------------------------------------------------------------
            */

            this.restoreSelection();

        } catch (error) {

            console.error(
                'Canvas render error:',
                error
            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Restore Selection
    |--------------------------------------------------------------------------
    */

    restoreSelection() {

        if (!Builder.selectedNodeId) {
            return;
        }

        const selected =
            document.querySelector(

                `[data-node-id="${Builder.selectedNodeId}"]`

            );

        if (!selected) return;

        BuilderCanvasUtils
            .applySelectionStyles(
                selected
            );

        BuilderOverlay.show(

            selected,
            Builder.selectedNodeId

        );

    }

};