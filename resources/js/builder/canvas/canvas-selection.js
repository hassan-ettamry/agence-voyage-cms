window.BuilderCanvasSelection = {

    /*
    |--------------------------------------------------------------------------
    | Bind Selection
    |--------------------------------------------------------------------------
    */

    bind(canvas) {

        if (!canvas) return;

        canvas.addEventListener(

            'click',

            (event) => {

                const element =

                    BuilderCanvasUtils
                        .getNodeElement(
                            event.target
                        );

                if (!element) return;

                event.stopPropagation();

                /*
                |--------------------------------------------------------------------------
                | Node ID
                |--------------------------------------------------------------------------
                */

                const nodeId =
                    element.dataset.nodeId;

                if (!nodeId) return;

                BuilderLogger.group(
                    'SELECTION',
                    BuilderLogger.colors.info
                );

                BuilderLogger.log(
                    'SELECTED NODE',
                    nodeId
                );

                /*
                |--------------------------------------------------------------------------
                | Find Node
                |--------------------------------------------------------------------------
                */

                const node =
                    Builder.findNodeById(
                        nodeId
                    );

                if (!node) {

                    BuilderLogger.warn(
                        'NODE NOT FOUND'
                    );

                    BuilderLogger.end();

                    return;

                }

                /*
                |--------------------------------------------------------------------------
                | Save Selection
                |--------------------------------------------------------------------------
                */

                BuilderStore.setSelection(
                    nodeId,
                    element
                );

                /*
                |--------------------------------------------------------------------------
                | Clear Old Selection
                |--------------------------------------------------------------------------
                */

                BuilderCanvasUtils
                    .clearSelectionStyles();

                /*
                |--------------------------------------------------------------------------
                | Apply Selection
                |--------------------------------------------------------------------------
                */

                BuilderCanvasUtils
                    .applySelectionStyles(
                        element
                    );

                /*
                |--------------------------------------------------------------------------
                | Open Controls
                |--------------------------------------------------------------------------
                */

                BuilderSidebar.switchTab(
                    'controls'
                );

                /*
                |--------------------------------------------------------------------------
                | Settings Panel
                |--------------------------------------------------------------------------
                */

                const schema =
                    BuilderSchema.get(
                        node.type
                    );

                BuilderSettingsPanel.render(

                    node,
                    schema,
                    BuilderStore.selectedNodeId

                );

                /*
                |--------------------------------------------------------------------------
                | Selection Changed Event
                |--------------------------------------------------------------------------
                */

                BuilderEventBus.emit(
                    'selection.changed',
                    nodeId
                );

                /*
                |--------------------------------------------------------------------------
                | Show Overlay
                |--------------------------------------------------------------------------
                */

                BuilderOverlay.show(
                    element,
                    BuilderStore.selectedNodeId
                );

                BuilderLogger.end();

            }

        );

    }

};