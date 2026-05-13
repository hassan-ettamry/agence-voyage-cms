window.BuilderStateUtils = {

    /*
    |------------------------------------------------------------------
    | Find Node By ID
    |------------------------------------------------------------------
    */

    findNodeById(

        id,

        nodes = BuilderStructureState.structure

    ) {

        for (const node of nodes) {

            /*
            |----------------------------------------------------------
            | Match
            |----------------------------------------------------------
            */

            if (node.id === id) {

                return node;

            }

            /*
            |----------------------------------------------------------
            | Children
            |----------------------------------------------------------
            */

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
    |------------------------------------------------------------------
    | Recursive Walker
    |------------------------------------------------------------------
    */

    walk(

        nodes = BuilderStructureState.structure,

        callback

    ) {

        nodes.forEach(node => {

            callback(node);

            /*
            |----------------------------------------------------------
            | Walk Children
            |----------------------------------------------------------
            */

            if (node.children?.length) {

                this.walk(

                    node.children,

                    callback

                );

            }

        });

    }

};