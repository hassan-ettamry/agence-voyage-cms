window.BuilderSelectionManager = {

    /*
    |--------------------------------------------------------------------------
    | Select Node
    |--------------------------------------------------------------------------
    */

    select(nodeId, element = null) {

        if (!nodeId) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Save Selection
        |--------------------------------------------------------------------------
        */

        Builder.selectedNodeId =
            nodeId;

        Builder.selectedElement =
            element;

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Selection
    |--------------------------------------------------------------------------
    */

    clear() {

        Builder.selectedNodeId =
            null;

        Builder.selectedElement =
            null;

        /*
        |--------------------------------------------------------------------------
        | Remove Selection Styles
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Hide Overlay
        |--------------------------------------------------------------------------
        */

        BuilderOverlay.hide();

    }

};