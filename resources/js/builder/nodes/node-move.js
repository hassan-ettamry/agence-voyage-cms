window.BuilderNodeMove = {

    /*
    |--------------------------------------------------------------------------
    | Move Node
    |--------------------------------------------------------------------------
    */

    move({

        nodeId,
        targetId,
        position

    }) {

        BuilderLogger.group(
            'MOVE NODE',
            BuilderLogger.colors.warning
        );

        BuilderLogger.log(
            'NODE ID',
            nodeId
        );

        BuilderLogger.log(
            'TARGET ID',
            targetId
        );

        BuilderLogger.log(
            'POSITION',
            position
        );

        BuilderLogger.log(
            'BEFORE MOVE',
            structuredClone(
                BuilderStore.structure
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent Self Move
        |--------------------------------------------------------------------------
        */

        if (nodeId === targetId) {

            BuilderLogger.warn(
                'SELF MOVE PREVENTED'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Find Original Node
        |--------------------------------------------------------------------------
        */

        const originalNode =

            Builder.findNodeById(
                nodeId
            );

        if (!originalNode) {

            BuilderLogger.warn(
                'NODE NOT FOUND'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Clone Node
        |--------------------------------------------------------------------------
        */

        const node =
            structuredClone(
                originalNode
            );

        /*
        |--------------------------------------------------------------------------
        | Find Target
        |--------------------------------------------------------------------------
        */

        let target =

            Builder.findNodeById(
                targetId
            );

        if (!target) {

            BuilderLogger.warn(
                'TARGET NOT FOUND'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Parent Into Child
        |--------------------------------------------------------------------------
        */

        const isChild =

            BuilderNodeTraversal.isChildOf(
                targetId,
                nodeId
            );

        if (isChild) {

            BuilderLogger.warn(
                'INVALID PARENT MOVE'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Remove Existing
        |--------------------------------------------------------------------------
        */

        this.removeNode(
            nodeId
        );

        BuilderLogger.warn(
            'NODE REMOVED'
        );

        BuilderLogger.log(
            'STRUCTURE AFTER REMOVE',
            structuredClone(
                BuilderStore.structure
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Re-Fetch Target
        |--------------------------------------------------------------------------
        */

        target =

            Builder.findNodeById(
                targetId
            );

        if (!target) {

            BuilderLogger.warn(
                'TARGET LOST AFTER REMOVE'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Inside
        |--------------------------------------------------------------------------
        */

        if (position === 'inside') {

            /*
            |--------------------------------------------------------------------------
            | Ensure Children
            |--------------------------------------------------------------------------
            */

            if (!target.children) {

                target.children = [];

            }

            /*
            |--------------------------------------------------------------------------
            | Add Child
            |--------------------------------------------------------------------------
            */

            target.children.push(
                node
            );

            BuilderLogger.success(
                'NODE INSERTED INSIDE'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Before / After
        |--------------------------------------------------------------------------
        */

        else {

            /*
            |--------------------------------------------------------------------------
            | Parent
            |--------------------------------------------------------------------------
            */

            const parent =

                BuilderNodeTraversal.findParent(
                    targetId
                );

            /*
            |--------------------------------------------------------------------------
            | Collection
            |--------------------------------------------------------------------------
            */

            const collection =

                parent
                    ? parent.children
                    : BuilderStore.structure;

            /*
            |--------------------------------------------------------------------------
            | Target Index
            |--------------------------------------------------------------------------
            */

            const index =

                collection.findIndex(

                    item => item.id === targetId

                );

            if (index === -1) {

                BuilderLogger.warn(
                    'TARGET INDEX NOT FOUND'
                );

                BuilderLogger.end();

                return;

            }

            /*
            |--------------------------------------------------------------------------
            | Before
            |--------------------------------------------------------------------------
            */

            if (position === 'before') {

                collection.splice(
                    index,
                    0,
                    node
                );

                BuilderLogger.success(
                    'NODE INSERTED BEFORE'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | After
            |--------------------------------------------------------------------------
            */

            else if (position === 'after') {

                collection.splice(
                    index + 1,
                    0,
                    node
                );

                BuilderLogger.success(
                    'NODE INSERTED AFTER'
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | After Insert
        |--------------------------------------------------------------------------
        */

        BuilderLogger.log(
            'AFTER INSERT',
            structuredClone(
                BuilderStore.structure
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Tree
        |--------------------------------------------------------------------------
        */

        BuilderDebugValidator.validateTree();

        /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        */

        BuilderHistory.push();

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        BuilderRenderManager.requestRender(
            'node-move'
        );

        BuilderLogger.success(
            'MOVE COMPLETE'
        );

        BuilderLogger.end();

    },

    /*
    |--------------------------------------------------------------------------
    | Move To Root
    |--------------------------------------------------------------------------
    */

    moveToRoot({

        nodeId,
        position = 'after'

    }) {

        BuilderLogger.group(
            'MOVE TO ROOT',
            BuilderLogger.colors.warning
        );

        BuilderLogger.log(
            'NODE ID',
            nodeId
        );

        BuilderLogger.log(
            'POSITION',
            position
        );

        BuilderLogger.log(
            'BEFORE ROOT MOVE',
            structuredClone(
                BuilderStore.structure
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Find Original Node
        |--------------------------------------------------------------------------
        */

        const originalNode =

            Builder.findNodeById(
                nodeId
            );

        if (!originalNode) {

            BuilderLogger.warn(
                'NODE NOT FOUND'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Clone Node
        |--------------------------------------------------------------------------
        */

        const node =
            structuredClone(
                originalNode
            );

        BuilderLogger.log(
            'NODE BEFORE REMOVE',
            structuredClone(node)
        );

        /*
        |--------------------------------------------------------------------------
        | Remove Existing
        |--------------------------------------------------------------------------
        */

        this.removeNode(
            nodeId
        );

        BuilderLogger.warn(
            'NODE REMOVED FROM OLD POSITION'
        );

        BuilderLogger.log(
            'STRUCTURE AFTER REMOVE',
            structuredClone(
                BuilderStore.structure
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Empty Root
        |--------------------------------------------------------------------------
        */

        if (!BuilderStore.structure.length) {

            BuilderStore.structure.push(
                node
            );

            BuilderLogger.success(
                'NODE INSERTED INTO EMPTY ROOT'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Insert Position
        |--------------------------------------------------------------------------
        */

        else {

            /*
            |--------------------------------------------------------------------------
            | Insert First
            |--------------------------------------------------------------------------
            */

            if (position === 'before') {

                BuilderStore.structure.unshift(
                    node
                );

                BuilderLogger.success(
                    'NODE INSERTED AT ROOT START'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Insert Last
            |--------------------------------------------------------------------------
            */

            else {

                BuilderStore.structure.push(
                    node
                );

                BuilderLogger.success(
                    'NODE INSERTED AT ROOT END'
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | After Insert
        |--------------------------------------------------------------------------
        */

        BuilderLogger.log(
            'STRUCTURE AFTER ROOT INSERT',
            structuredClone(
                BuilderStore.structure
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Tree
        |--------------------------------------------------------------------------
        */

        BuilderDebugValidator.validateTree();

        /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        */

        BuilderHistory.push();

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        BuilderRenderManager.requestRender(
            'move-to-root'
        );

        BuilderLogger.success(
            'ROOT MOVE COMPLETE'
        );

        BuilderLogger.end();

    },

    /*
    |--------------------------------------------------------------------------
    | Remove Node
    |--------------------------------------------------------------------------
    */

    removeNode(nodeId) {

        BuilderLogger.warn(
            'REMOVE NODE',
            nodeId
        );

        /*
        |--------------------------------------------------------------------------
        | Parent
        |--------------------------------------------------------------------------
        */

        const parent =

            BuilderNodeTraversal.findParent(
                nodeId
            );

        /*
        |--------------------------------------------------------------------------
        | Nested Node
        |--------------------------------------------------------------------------
        */

        if (parent) {

            parent.children =

                parent.children.filter(

                    child => child.id !== nodeId

                );

            BuilderLogger.warn(
                'NODE REMOVED FROM PARENT'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Root Node
        |--------------------------------------------------------------------------
        */

        else {

            const index =

                BuilderStore.structure.findIndex(

                    child => child.id === nodeId

                );

            if (index !== -1) {

                BuilderStore.structure.splice(
                    index,
                    1
                );

                BuilderLogger.warn(
                    'NODE REMOVED FROM ROOT'
                );

            }

        }

    }

};