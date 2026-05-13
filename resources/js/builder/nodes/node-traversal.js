window.BuilderNodeTraversal = {

    /*
    |--------------------------------------------------------------------------
    | Find Parent
    |--------------------------------------------------------------------------
    */

    findParent(

        nodeId,

        nodes = Builder.structure,

        parent = null

    ) {

        for (const node of nodes) {

            if (node.id === nodeId) {
                return parent;
            }

            if (node.children?.length) {

                const found =

                    this.findParent(

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
    | Walk Tree
    |--------------------------------------------------------------------------
    */

    walk(nodes, callback) {

        nodes.forEach(node => {

            callback(node);

            if (node.children?.length) {

                this.walk(
                    node.children,
                    callback
                );

            }

        });

    }

};