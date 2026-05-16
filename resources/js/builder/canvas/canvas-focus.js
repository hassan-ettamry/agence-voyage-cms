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

                const clickedNode =
                    event.target.closest(
                        '[data-node-id]'
                    );

                const clickedOverlay =
                    event.target.closest(
                        '#builder-overlay-root'
                    );

                if (
                    clickedNode ||
                    clickedOverlay
                ) {

                    return;

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