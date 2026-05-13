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

                    'outline',
                    'outline-2',
                    'outline-dashed',
                    'outline-pink-500',
                    'outline-offset-[-2px]',
                    'relative',
                    'z-[1]'

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

        element.classList.add(

            'outline',
            'outline-2',
            'outline-dashed',
            'outline-pink-500',
            'outline-offset-[-2px]',
            'relative',
            'z-[1]'

        );

    }

};