window.BuilderCanvasFocus = {

    /*
    |--------------------------------------------------------------------------
    | Bind Outside Click
    |--------------------------------------------------------------------------
    */

    bind() {

        document.addEventListener(

            'click',

            (event) => {

                if (
                    BuilderInteractionBoundaries
                        .preservesSelection(
                            event.target
                        )
                ) {

                    return;

                }

                if (BuilderLogger.shouldLog('interaction')) {

                    BuilderLogger.log(
                        'OUTSIDE CLICK CLEARS SELECTION AND OVERLAY',
                        {
                            surface:
                                BuilderInteractionBoundaries
                                    .surface(event.target)
                        }
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Clear Selection
                |--------------------------------------------------------------------------
                */

                BuilderStore.clearSelection();

                /*
                |--------------------------------------------------------------------------
                | Remove Selection Styles
                |--------------------------------------------------------------------------
                */

                BuilderCanvasUtils
                    .clearSelectionStyles();

                /*
                |--------------------------------------------------------------------------
                | Hide Overlay
                |--------------------------------------------------------------------------
                */

                BuilderOverlay.hide();

            }

        );

    }

};
