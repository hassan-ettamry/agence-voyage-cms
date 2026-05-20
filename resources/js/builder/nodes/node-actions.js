window.BuilderNodes = {

    /*
    |--------------------------------------------------------------------------
    | Update Props
    |--------------------------------------------------------------------------
    */

    updateProps(
        nodeId,
        key,
        value
    ) {

        const node =

            Builder.findNodeById(
                nodeId
            );

        if (!node) {
            return;
        }

        if (!node.props) {

            node.props = {};

        }

        node.props[key] = value;

        BuilderEventBus.emit(

            BuilderEvents.NODE_UPDATED,
        
            {
                nodeId,
                key,
                value
            }
        
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Add Child
    |--------------------------------------------------------------------------
    */

    addChild(parentId, child) {

        const parent =

            Builder.findNodeById(
                parentId
            );

        if (!parent) {
            return;
        }

        const placement =
            BuilderStructureRules.insertNode(
                child,
                parentId,
                'inside'
            );

        if (!placement) {
            return null;
        }

        BuilderStructureRules.normalizeStore();

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
        );

        return placement;

    },

    /*
    |--------------------------------------------------------------------------
    | Remove Node
    |--------------------------------------------------------------------------
    */

    remove(nodeId) {

        const removed =
            this.removeRecursive(
                nodeId,
                BuilderStore.structure
            );

        if (!removed) {
            return false;
        }

        BuilderStructureRules.normalizeStore();

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
        );

        return true;

    },

    /*
    |--------------------------------------------------------------------------
    | Recursive Remove
    |--------------------------------------------------------------------------
    */

    removeRecursive(nodeId, nodes) {

        const index =

            nodes.findIndex(
                node => node.id === nodeId
            );

        if (index !== -1) {

            nodes.splice(index, 1);

            return true;

        }

        for (const node of nodes) {

            if (node.children?.length) {

                const removed =

                    this.removeRecursive(
                        nodeId,
                        node.children
                    );

                if (removed) {
                    return true;
                }

            }

        }

        return false;

    }

};
