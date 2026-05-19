window.BuilderCanvas = {

    /*
    |--------------------------------------------------------------------------
    | Render Version
    |--------------------------------------------------------------------------
    */

    renderVersion: 0,

    /*
    |--------------------------------------------------------------------------
    | Render Canvas
    |--------------------------------------------------------------------------
    */

    async render() {

        const version =
            ++this.renderVersion;

        if (BuilderLogger.shouldLog('render')) {

            BuilderLogger.group(
                `RENDER ${version}`,
                BuilderLogger.colors.render
            );
        
        }

        const canvas =
            BuilderCanvasUtils.getCanvas();

        if (!canvas) {

            BuilderLogger.warn(
                'Canvas not found'
            );

            BuilderLogger.end();

            return;

        }

        const structure =
            BuilderStore.getStructure();

        if (BuilderLogger.shouldLog('render')) {

            BuilderLogger.log(
                'STRUCTURE BEFORE RENDER',
                structuredClone(
                    structure
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        if (!structure.length) {

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

            BuilderLogger.success(
                `RENDER ${version} COMPLETE`
            );

            BuilderLogger.end();

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
                                structuredClone(
                                    structure
                                )

                        })

                    }

                );

            if (!response.ok) {

                throw new Error(
                    `Builder render failed with status ${response.status}`
                );

            }

            /*
            |--------------------------------------------------------------------------
            | HTML
            |--------------------------------------------------------------------------
            */

            const html =
                await response.text();

            BuilderLogger.success(
                'HTML RECEIVED'
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent Old Render
            |--------------------------------------------------------------------------
            */

            if (
                version !== this.renderVersion
            ) {

                BuilderLogger.warn(
                    'OLD RENDER CANCELLED',
                    version
                );

                BuilderLogger.end();

                return;
            }

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
            | Structure Updated Event
            |--------------------------------------------------------------------------
            */

            BuilderEventBus.emit(
                'structure.updated'
            );

            /*
            |--------------------------------------------------------------------------
            | Restore Selection
            |--------------------------------------------------------------------------
            */

            this.restoreSelection();

            BuilderLogger.success(
                `RENDER ${version} COMPLETE`
            );

            BuilderLogger.end();

        } catch (error) {

            console.error(
                'Canvas render error:',
                error
            );

            BuilderLogger.error(
                error
            );

            BuilderLogger.end();

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Restore Selection
    |--------------------------------------------------------------------------
    */

    restoreSelection() {

        if (!BuilderStore.selectedNodeId) {
            return;
        }

        const selected =
            document.querySelector(

                `[data-node-id="${CSS.escape(BuilderStore.selectedNodeId)}"]`

            );

        if (!selected) return;

        if (!document.body.contains(selected)) {

            BuilderLogger.warn(
                'INVALID SELECTION RESTORE'
            );

            return;
        }

        BuilderCanvasUtils
            .applySelectionStyles(
                selected
            );

        BuilderStore.selectedElement =
            selected;

        BuilderOverlay.show(

            selected,
            BuilderStore.selectedNodeId

        );

    }

};
