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

            'node.updated',
        
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

        if (!parent.children) {

            parent.children = [];

        }

        parent.children.push(child);

    },

    /*
    |--------------------------------------------------------------------------
    | Remove Node
    |--------------------------------------------------------------------------
    */

    remove(nodeId) {

        this.removeRecursive(
            nodeId,
            BuilderStore.structure
        );

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