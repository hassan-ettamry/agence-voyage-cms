window.BuilderCanvas = {

    /*
    |--------------------------------------------------------------------------
    | Render Version
    |--------------------------------------------------------------------------
    */

    renderVersion: 0,

    /*
    |--------------------------------------------------------------------------
    | Invalidate Current Render
    |--------------------------------------------------------------------------
    */

    invalidateCurrentRender() {

        this.renderVersion++;

    },

    /*
    |--------------------------------------------------------------------------
    | Render Canvas
    |--------------------------------------------------------------------------
    */

    async render(source = 'direct', reason = null) {

        const version =
            ++this.renderVersion;

        const startedAt =
            performance.now();

        const context = {
            version,
            source,
            reason,
            startedAt
        };

        BuilderEventBus.emitSafe(
            BuilderEvents.CANVAS_RENDER_STARTED,
            context
        );

        if (BuilderLogger.shouldLog('render')) {

            BuilderLogger.group(
                `RENDER ${version}`,
                BuilderLogger.colors.render
            );

            BuilderLogger.log(
                'SOURCE',
                source
            );

            if (reason) {

                BuilderLogger.log(
                    'REASON',
                    reason
                );

            }
        
        }

        const canvas =
            BuilderCanvasUtils.getCanvas();

        if (!canvas) {

            BuilderLogger.warn(
                'Canvas not found'
            );

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDER_FAILED,
                {
                    ...context,
                    error: 'canvas_not_found',
                    duration: performance.now() - startedAt
                }
            );

            return false;

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

            this.clearStaleSelection();

            BuilderLogger.success(
                `RENDER ${version} COMPLETE`
            );

            if (BuilderLogger.shouldLog('render')) {

                BuilderLogger.log(
                    'DURATION',
                    `${(performance.now() - startedAt).toFixed(2)}ms`
                );

            }

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDERED,
                {
                    ...context,
                    empty: true,
                    duration: performance.now() - startedAt
                }
            );

            return true;

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

                            mode: 'editor',

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

                BuilderEventBus.emitSafe(
                    BuilderEvents.CANVAS_RENDER_CANCELLED,
                    {
                        ...context,
                        duration: performance.now() - startedAt
                    }
                );

                return false;
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
            | Restore Selection
            |--------------------------------------------------------------------------
            */

            this.restoreSelection();

            BuilderLogger.success(
                `RENDER ${version} COMPLETE`
            );

            if (BuilderLogger.shouldLog('render')) {

                BuilderLogger.log(
                    'DURATION',
                    `${(performance.now() - startedAt).toFixed(2)}ms`
                );

            }

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDERED,
                {
                    ...context,
                    empty: false,
                    duration: performance.now() - startedAt
                }
            );

            return true;

        } catch (error) {

            console.error(
                'Canvas render error:',
                error
            );

            BuilderLogger.error(
                error
            );

            if (BuilderLogger.shouldLog('render')) {

                BuilderLogger.log(
                    'FAILED AFTER',
                    `${(performance.now() - startedAt).toFixed(2)}ms`
                );

            }

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDER_FAILED,
                {
                    ...context,
                    error: error?.message || String(error),
                    duration: performance.now() - startedAt
                }
            );

            return false;

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

        if (!selected) {

            this.clearStaleSelection();

            return;

        }

        if (!document.body.contains(selected)) {

            BuilderLogger.warn(
                'INVALID SELECTION RESTORE'
            );

            this.clearStaleSelection();

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

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Stale Selection
    |--------------------------------------------------------------------------
    */

    clearStaleSelection() {

        if (window.BuilderSelectionManager) {

            BuilderSelectionManager.clear({
                settings: true
            });

            return;

        }

        BuilderStore.clearSelection();
        BuilderOverlay.hide();

        const panel =
            document.getElementById(
                'settings-panel'
            );

        if (panel) {

            panel.innerHTML = '';

        }

    }

};
