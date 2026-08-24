window.BuilderOverlayActions = {

    /*
    |--------------------------------------------------------------------------
    | Move Up
    |--------------------------------------------------------------------------
    */

    moveUp(nodeId) {

        const parent = BuilderNodeTraversal.findParent(nodeId);
        const siblings = parent ? parent.children : BuilderStore.structure;

        const index = BuilderNodeTraversal.findNodeIndex(nodeId, siblings);

        if (index <= 0) return;

        // Swap with previous sibling
        const temp = siblings[index - 1];
        siblings[index - 1] = siblings[index];
        siblings[index] = temp;

        BuilderStructureRules.normalizeStore();

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
        );

        BuilderDebugValidator.validateTree();

        BuilderHistory.push();
        BuilderRenderManager.requestRender(
            'overlay.moveUp'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Move Down
    |--------------------------------------------------------------------------
    */

    moveDown(nodeId) {

        const parent = BuilderNodeTraversal.findParent(nodeId);
        const siblings = parent ? parent.children : BuilderStore.structure;

        const index = BuilderNodeTraversal.findNodeIndex(nodeId, siblings);

        if (index < 0 || index >= siblings.length - 1) return;

        // Swap with next sibling
        const temp = siblings[index + 1];
        siblings[index + 1] = siblings[index];
        siblings[index] = temp;

        BuilderStructureRules.normalizeStore();

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
        );

        BuilderDebugValidator.validateTree();

        BuilderHistory.push();
        BuilderRenderManager.requestRender(
            'overlay.moveDown'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Duplicate
    |--------------------------------------------------------------------------
    */

    duplicate(nodeId) {

        const node = Builder.findNodeById(nodeId);

        if (!node) return;

        // Deep clone with new IDs
        const cloned = this.cloneWithNewIds(
            BuilderComponentUtils.clone(node)
        );

        const placement =
            BuilderStructureRules.insertNode(
                cloned,
                nodeId,
                'after'
            );

        if (!placement) {
            return;
        }

        BuilderStructureRules.normalizeStore();

        BuilderStore.setSelection(cloned.id, null);

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
        );

        BuilderDebugValidator.validateTree();

        BuilderHistory.push();
        BuilderRenderManager.requestRender(
            'overlay.duplicate'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Clone With New IDs (recursive)
    |--------------------------------------------------------------------------
    */

    cloneWithNewIds(node) {

        node.id = BuilderComponentUtils.generateId();

        if (node.children && node.children.length) {
            node.children = node.children.map(
                child => this.cloneWithNewIds(child)
            );
        }

        return node;

    },

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    edit(nodeId) {

        const element =
            BuilderOverlayElements.getElement(nodeId);

        if (!element) return;

        if (
            BuilderLogger.shouldLog('selection')
            ||
            BuilderLogger.shouldLog('interaction')
        ) {

            BuilderLogger.log(
                'SELECTION REQUEST SOURCE',
                {
                    source: 'overlay-edit',
                    nodeId
                }
            );

        }

        BuilderSelectionManager.select(
            nodeId,
            element,
            {
                source: 'overlay-edit'
            }
        );

    }

    ,

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    delete(nodeId) {

        if (!nodeId) {
            return;
        }

        const node = Builder.findNodeById(nodeId);
        if (!node) return;

        if (node.children?.length && !window.confirm(`Delete this ${node.type} and its ${node.children.length} child element(s)?`)) {
            return;
        }

        if (!BuilderNodes.remove(nodeId)) return;

        if (BuilderStore.selectedNodeId === nodeId) {
            BuilderSelectionManager.clear({
                settings: true
            });
        }

        BuilderHistory.push();
        BuilderRenderManager.requestRender(
            'overlay.delete'
        );

    }

};
