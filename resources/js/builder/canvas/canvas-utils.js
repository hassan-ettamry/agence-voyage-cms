window.BuilderCanvasUtils = {

    /*
    |--------------------------------------------------------------------------
    | Get Canvas
    |--------------------------------------------------------------------------
    */

    getCanvas() {

        return document.getElementById(
            'canvas'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Get Node Element
    |--------------------------------------------------------------------------
    */

    getNodeElement(target) {

        return (target?.closest ? target : target?.parentElement)?.closest(
            '[data-node-id]'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Selection Styles
    |--------------------------------------------------------------------------
    */

    clearSelectionStyles() {

        this.getCanvas()
            ?.querySelectorAll(
                '[data-node-id]'
            )
            .forEach(el => {

                delete el.dataset.builderHoverState;

                BuilderOverlayTheme.clearOutline(
                    el
                );

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Apply Selection Styles
    |--------------------------------------------------------------------------
    */

    applySelectionStyles(element) {

        if (!element) return;

        BuilderOverlayTheme.applyOutline(
            element,
            'active',
            BuilderOverlayTheme.pathOutlineOptions(0)
        );

    }

};
