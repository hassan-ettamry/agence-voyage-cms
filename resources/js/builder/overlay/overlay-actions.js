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

        BuilderHistory.push();
        BuilderCanvas.render();

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

        BuilderHistory.push();
        BuilderCanvas.render();

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

        const parent = BuilderNodeTraversal.findParent(nodeId);
        const siblings = parent ? parent.children : BuilderStore.structure;

        const index = BuilderNodeTraversal.findNodeIndex(nodeId, siblings);

        // Insert right after the current node
        siblings.splice(index + 1, 0, cloned);

        BuilderHistory.push();
        BuilderCanvas.render();

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

        element.click();

    }

};