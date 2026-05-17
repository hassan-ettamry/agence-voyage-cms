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

                const clickedBuilderUi =
                    event.target.closest(
                        [
                            '#left-panel',
                            '#right-panel',
                            '#builder-topbar',
                            '#settings-panel',
                            '#layers-panel',
                            'input',
                            'textarea',
                            'select',
                            'button',
                            'a',
                            'label',
                            '[contenteditable="true"]'
                        ].join(',')
                    );

                if (
                    clickedNode ||
                    clickedOverlay ||
                    clickedBuilderUi
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
