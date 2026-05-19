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

        BuilderStore.selectedNodeId =
            nodeId;

        BuilderStore.selectedElement =
            element;

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Selection
    |--------------------------------------------------------------------------
    */

    clear(options = {}) {

        const {
            settings = false
        } = options;

        BuilderStore.clearSelection();

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

        if (settings) {

            this.clearSettingsPanel();

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Settings Panel
    |--------------------------------------------------------------------------
    */

    clearSettingsPanel() {

        const panel =
            document.getElementById(
                'settings-panel'
            );

        if (panel) {

            panel.innerHTML = '';

        }

    }

};
