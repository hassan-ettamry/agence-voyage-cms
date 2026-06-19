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

        this.applyVisuals(
            BuilderStore.selectedNodeId,
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

        BuilderSidebar.setTitle(
            BuilderSidebar.componentLabel(
                node.type
            )
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

        this.showSelectionToolbar(
            BuilderStore.selectedNodeId,
            selectedElement
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
    | Apply Selection Visuals
    |--------------------------------------------------------------------------
    */

    applyVisuals(nodeId, selectedElement = null) {

        const element =
            selectedElement
            ||
            this.findElement(nodeId);

        if (!nodeId || !element) {
            return false;
        }

        BuilderCanvasUtils
            .clearSelectionStyles();

        this.applyAncestorVisuals(
            nodeId
        );

        this.applyChildContainerVisuals(
            nodeId
        );

        BuilderCanvasUtils
            .applySelectionStyles(
                element
            );

        return true;

    },

    /*
    |--------------------------------------------------------------------------
    | Apply Ancestor Visuals
    |--------------------------------------------------------------------------
    */

    applyAncestorVisuals(nodeId) {

        const ancestors =
            BuilderNodeTraversal.findAncestors(
                nodeId
            )
            ||
            [];

        ancestors.forEach((ancestor, index) => {

            const element =
                this.findElement(
                    ancestor.id
                );

            if (!element) {
                return;
            }

            const isImmediateParent =
                index === ancestors.length - 1;

            const distanceFromSelected =
                ancestors.length - index;

            BuilderOverlayTheme.applyOutline(
                element,
                isImmediateParent ? 'parent' : 'ancestor',
                BuilderOverlayTheme.pathOutlineOptions(
                    distanceFromSelected
                )
            );

            element.dataset.builderHoverState =
                isImmediateParent
                    ? 'parent'
                    : 'ancestor';

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Apply Child Container Visuals
    |--------------------------------------------------------------------------
    */

    applyChildContainerVisuals(nodeId) {

        BuilderNodeTraversal
            .directChildrenByType(
                nodeId,
                'container'
            )
            .forEach(child => {

                const element =
                    this.findElement(
                        child.id
                    );

                if (!element) {
                    return;
                }

                BuilderOverlayTheme.applyOutline(
                    element,
                    'child',
                    BuilderOverlayTheme.childOutlineOptions()
                );

                element.dataset.builderHoverState =
                    'child';

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Show Selection Toolbar
    |--------------------------------------------------------------------------
    */

    showSelectionToolbar(nodeId, selectedElement = null) {

        const element =
            selectedElement
            ||
            this.findElement(nodeId);

        if (!nodeId || !element) {
            return false;
        }

        const ancestors =
            BuilderNodeTraversal.findAncestors(
                nodeId
            )
            ||
            [];

        const parent =
            ancestors[ancestors.length - 1];

        const parentElement =
            parent?.id
                ? this.findElement(parent.id)
                : null;

        if (parentElement) {

            BuilderOverlay.showGroup([
                {
                    element: parentElement,
                    nodeId: parent.id,
                    mode: 'parent-hover',
                    align: 'left',
                    height: 24
                },
                {
                    element,
                    nodeId,
                    mode: 'selected',
                    align: 'center',
                    height: 24
                }
            ]);

            return true;

        }

        BuilderOverlay.show(
            element,
            nodeId,
            {
                mode: 'selected',
                align: 'center',
                height: 24
            }
        );

        return true;

    },

    /*
    |--------------------------------------------------------------------------
    | Restore Current Visual State
    |--------------------------------------------------------------------------
    */

    restoreVisuals() {

        if (!BuilderStore.selectedNodeId) {
            return false;
        }

        const selectedElement =
            this.findElement(
                BuilderStore.selectedNodeId
            );

        if (!selectedElement) {
            return false;
        }

        this.applyVisuals(
            BuilderStore.selectedNodeId,
            selectedElement
        );

        this.showSelectionToolbar(
            BuilderStore.selectedNodeId,
            selectedElement
        );

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

                    'relative',
                    'z-[1]'

                );

                delete el.dataset.builderHoverState;

                BuilderOverlayTheme.clearOutline(
                    el
                );

            });

        /*
        |--------------------------------------------------------------------------
        | Hide Overlay
        |--------------------------------------------------------------------------
        */

        BuilderOverlay.hide();

        if (settings) {

            BuilderSidebar.setTitle();

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
