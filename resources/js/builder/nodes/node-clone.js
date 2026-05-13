window.BuilderNodeClone = {

    /*
    |--------------------------------------------------------------------------
    | Clone Node
    |--------------------------------------------------------------------------
    */

    clone(nodeId) {

        const node =

            Builder.findNodeById(
                nodeId
            );

        if (!node) {
            return null;
        }

        return structuredClone(node);

    }

};