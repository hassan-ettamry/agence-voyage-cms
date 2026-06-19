window.BuilderNodeTraversal = {

    /*
    |--------------------------------------------------------------------------
    | Find Node By ID
    |--------------------------------------------------------------------------
    */

    findNodeById(

        id,

        nodes = BuilderStore.structure

    ) {

        for (const node of nodes) {

            if (node.id === id) {

                return node;

            }

            if (node.children?.length) {

                const found = this.findNodeById(

                    id,

                    node.children

                );

                if (found) {

                    return found;

                }

            }

        }

        return null;

    },

    /*
    |--------------------------------------------------------------------------
    | Find Parent
    |--------------------------------------------------------------------------
    */

    findParent(

        nodeId,

        nodes = BuilderStore.structure,

        parent = null

    ) {

        for (const node of nodes) {

            if (node.id === nodeId) {
                return parent;
            }

            if (node.children?.length) {

                const found = this.findParent(

                    nodeId,

                    node.children,

                    node

                );

                if (found) {
                    return found;
                }

            }

        }

        return null;

    },

    /*
    |--------------------------------------------------------------------------
    | Find Ancestors
    |--------------------------------------------------------------------------
    */

    findAncestors(

        nodeId,

        nodes = BuilderStore.structure,

        path = []

    ) {

        for (const node of nodes) {

            if (node.id === nodeId) {
                return path;
            }

            if (node.children?.length) {

                const found = this.findAncestors(

                    nodeId,

                    node.children,

                    [
                        ...path,
                        node
                    ]

                );

                if (found) {
                    return found;
                }

            }

        }

        return null;

    },

    /*
    |--------------------------------------------------------------------------
    | Direct Children By Type
    |--------------------------------------------------------------------------
    */

    directChildrenByType(

        nodeId,

        type

    ) {

        const node =
            this.findNodeById(
                nodeId
            );

        if (!node?.children?.length) {
            return [];
        }

        return node.children.filter(
            child => child.type === type
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Find Node Index
    |--------------------------------------------------------------------------
    */

    findNodeIndex(

        nodeId,

        nodes

    ) {

        return nodes.findIndex(

            node => node.id === nodeId

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Walk Tree
    |--------------------------------------------------------------------------
    */

    walk(

        nodes = BuilderStore.structure,

        callback

    ) {

        nodes.forEach(node => {

            callback(node);

            if (node.children?.length) {

                this.walk(

                    node.children,

                    callback

                );

            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Is Child Of
    |--------------------------------------------------------------------------
    */

    isChildOf(

        targetId,

        parentId

    ) {

        const parent =

            this.findNodeById(
                parentId
            );

        if (!parent || !parent.children) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Recursive Search
        |--------------------------------------------------------------------------
        */

        const search = (children) => {

            for (const child of children) {

                if (child.id === targetId) {
                    return true;
                }

                if (child.children?.length) {

                    const found =

                        search(
                            child.children
                        );

                    if (found) {
                        return true;
                    }

                }

            }

            return false;

        };

        return search(
            parent.children
        );

    }

};
