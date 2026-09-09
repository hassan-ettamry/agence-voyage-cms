window.BuilderDebugValidator = {

    validateTree() {

        if (!BuilderConfig.validateTree) {
            return true;
        }

        BuilderLogger.group(
            'TREE VALIDATION',
            BuilderLogger.colors.state
        );

        const ids = new Set();

        let hasError = false;

        const walk = (
            nodes,
            parent = null
        ) => {

            for (const node of nodes) {

                /*
                |--------------------------------------------------------------------------
                | Missing ID
                |--------------------------------------------------------------------------
                */

                if (!node.id) {

                    BuilderLogger.error(
                        'NODE WITHOUT ID',
                        node
                    );

                    hasError = true;
                }

                /*
                |--------------------------------------------------------------------------
                | Duplicate ID
                |--------------------------------------------------------------------------
                */

                if (ids.has(node.id)) {

                    BuilderLogger.error(
                        'DUPLICATE NODE ID',
                        node.id
                    );

                    hasError = true;
                }

                ids.add(node.id);

                /*
                |--------------------------------------------------------------------------
                | Invalid Parent / Child Placement
                |--------------------------------------------------------------------------
                */

                if (
                    parent
                    &&
                    window.BuilderStructureRules
                    &&
                    !BuilderStructureRules.canAccept(
                        parent,
                        node
                    )
                ) {

                    BuilderLogger.error(
                        'INVALID CHILD PLACEMENT',
                        {
                            parentId: parent.id,
                            parentType: parent.type,
                            childId: node.id,
                            childType: node.type
                        }
                    );

                    hasError = true;
                }

                /*
                |--------------------------------------------------------------------------
                | Invalid Children
                |--------------------------------------------------------------------------
                */

                if (
                    node.children &&
                    !Array.isArray(node.children)
                ) {

                    BuilderLogger.error(
                        'INVALID CHILDREN',
                        node
                    );

                    hasError = true;
                }

                /*
                |--------------------------------------------------------------------------
                | Circular Reference
                |--------------------------------------------------------------------------
                */

                if (
                    parent &&
                    parent.id === node.id
                ) {

                    BuilderLogger.error(
                        'CIRCULAR REFERENCE',
                        node
                    );

                    hasError = true;
                }

                /*
                |--------------------------------------------------------------------------
                | Recursive
                |--------------------------------------------------------------------------
                */

                if (node.children?.length) {

                    walk(
                        node.children,
                        node
                    );

                }

            }

        };

        walk(BuilderStore.structure);

        if (!hasError) {

            BuilderLogger.success(
                'TREE VALID'
            );

        }

        BuilderLogger.end();

        return !hasError;

    }

};
