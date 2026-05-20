window.BuilderSelectionManager = {

    /*
    |--------------------------------------------------------------------------
    | Select Node
    |--------------------------------------------------------------------------
    */

    select(nodeId, element = null, options = {}) {

        if (!nodeId) {
            return false;
        }

        const {
            source = 'unknown',
            scroll = false
        } = options;

        const selectedElement =
            element
            ||
            this.findElement(nodeId);

        if (!selectedElement) {
            return false;
        }

        BuilderLogger.group(
            'SELECTION',
            BuilderLogger.colors.info
        );

        BuilderLogger.log(
            'SELECTED NODE',
            nodeId
        );

        const node =
            Builder.findNodeById(
                nodeId
            );

        if (!node) {

            BuilderLogger.warn(
                'NODE NOT FOUND'
            );

            BuilderLogger.end();

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Save Selection
        |--------------------------------------------------------------------------
        */

        BuilderStore.setSelection(
            nodeId,
            selectedElement
        );

        if (
            BuilderLogger.shouldLog('selection')
            ||
            BuilderLogger.shouldLog('interaction')
        ) {

            BuilderLogger.log(
                'SELECTION APPLIED SOURCE',
                {
                    source,
                    nodeId
                }
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Selection Styles
        |--------------------------------------------------------------------------
        */

        BuilderCanvasUtils
            .clearSelectionStyles();

        BuilderCanvasUtils
            .applySelectionStyles(
                selectedElement
            );

        /*
        |--------------------------------------------------------------------------
        | Controls And Settings
        |--------------------------------------------------------------------------
        */

        BuilderSidebar.switchTab(
            'controls'
        );

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
        | Overlay
        |--------------------------------------------------------------------------
        */

        BuilderOverlay.show(
            selectedElement,
            BuilderStore.selectedNodeId
        );

        if (scroll) {

            selectedElement.scrollIntoView({

                behavior: 'smooth',

                block: 'center'

            });

        }

        BuilderLogger.end();

        return true;

    },

    /*
    |--------------------------------------------------------------------------
    | Find Element
    |--------------------------------------------------------------------------
    */

    findElement(nodeId) {

        return document.querySelector(
            `[data-node-id="${CSS.escape(nodeId)}"]`
        );

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
