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

        return target.closest(
            '[data-node-id]'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Selection Styles
    |--------------------------------------------------------------------------
    */

    clearSelectionStyles() {

        document
            .querySelectorAll(
                '[data-node-id]'
            )
            .forEach(el => {

                el.classList.remove(

                    'relative',
                    'z-[1]'

                );

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

        element.classList.add(

            'relative',
            'z-[1]'

        );

    }

};
