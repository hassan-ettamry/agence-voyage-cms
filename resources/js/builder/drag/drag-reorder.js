window.BuilderDragReorder = {

    /*
    |--------------------------------------------------------------------------
    | Start Reorder Drag
    |--------------------------------------------------------------------------
    */

    start(event, nodeId) {

        /*
        |--------------------------------------------------------------------------
        | Prevent Nested Drag Bubbling
        |--------------------------------------------------------------------------
        */

        event.stopPropagation();

        BuilderLogger.group(
            'DRAG START',
            BuilderLogger.colors.drag
        );

        BuilderLogger.log(
            'NODE ID',
            nodeId
        );

        BuilderLogger.log(
            'EVENT TARGET',
            event.target
        );

        BuilderLogger.log(
            'CURRENT TARGET',
            event.currentTarget
        );

        BuilderLogger.log(
            'CURRENT STRUCTURE',
            structuredClone(
                BuilderStore.structure
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Node
        |--------------------------------------------------------------------------
        */

        const exists =

            Builder.findNodeById(
                nodeId
            );

        if (!exists) {

            BuilderLogger.warn(
                'DRAG NODE NOT FOUND'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Double Drag Start
        |--------------------------------------------------------------------------
        */

        const currentDragged =

            BuilderDragState
                .reorderNodeId;

        if (
            currentDragged &&
            currentDragged !== nodeId
        ) {

            BuilderLogger.warn(
                'DRAG ALREADY ACTIVE',
                currentDragged
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Save State
        |--------------------------------------------------------------------------
        */

        BuilderDragState.setDraggedNode(
            nodeId
        );

        BuilderLogger.success(
            'DRAG STATE SAVED'
        );

        /*
        |--------------------------------------------------------------------------
        | Transfer Data
        |--------------------------------------------------------------------------
        */

        event.dataTransfer.setData(

            'reorder-node-id',
            nodeId

        );

        /*
        |--------------------------------------------------------------------------
        | Effect
        |--------------------------------------------------------------------------
        */

        event.dataTransfer.effectAllowed =
            'move';

        /*
        |--------------------------------------------------------------------------
        | Add Dragging Style
        |--------------------------------------------------------------------------
        */

        const target =

            event.target.closest(
                '[data-node-id]'
            );

        if (target) {

            target.classList.add(
                'opacity-50'
            );

            BuilderLogger.success(
                'DRAG STYLE APPLIED'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Final State
        |--------------------------------------------------------------------------
        */

        BuilderLogger.log(
            'ACTIVE DRAG NODE',
            BuilderDragState.reorderNodeId
        );

        BuilderLogger.end();

    },

    /*
    |--------------------------------------------------------------------------
    | End Drag
    |--------------------------------------------------------------------------
    */

    end(event) {

        /*
        |--------------------------------------------------------------------------
        | Prevent Nested Bubbling
        |--------------------------------------------------------------------------
        */

        event.stopPropagation();

        BuilderLogger.group(
            'DRAG END',
            BuilderLogger.colors.drag
        );

        BuilderLogger.log(
            'ACTIVE DRAG NODE',
            BuilderDragState.reorderNodeId
        );

        /*
        |--------------------------------------------------------------------------
        | Hide Preview
        |--------------------------------------------------------------------------
        */

        BuilderDragPreview.hide();

        BuilderLogger.success(
            'PREVIEW HIDDEN'
        );

        /*
        |--------------------------------------------------------------------------
        | Remove Dragging Style
        |--------------------------------------------------------------------------
        */

        const target =

            event.target.closest(
                '[data-node-id]'
            );

        if (target) {

            target.classList.remove(
                'opacity-50'
            );

            BuilderLogger.success(
                'DRAG STYLE REMOVED'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        BuilderDragState.reset();

        BuilderLogger.success(
            'DRAG STATE RESET'
        );

        BuilderLogger.end();

    }

};
