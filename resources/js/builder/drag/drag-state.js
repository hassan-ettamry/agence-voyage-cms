window.BuilderDragState = {

    /*
    |--------------------------------------------------------------------------
    | Component Type
    |--------------------------------------------------------------------------
    */

    componentType: null,

    /*
    |--------------------------------------------------------------------------
    | Reorder State
    |--------------------------------------------------------------------------
    */

    draggedNodeId: null,

    isReordering: false,

    /*
    |--------------------------------------------------------------------------
    | Drop Position
    |--------------------------------------------------------------------------
    */

    dropPosition: null,

    /*
    |--------------------------------------------------------------------------
    | Set Component Type
    |--------------------------------------------------------------------------
    */

    setType(type) {

        BuilderLogger.log(
            'SET COMPONENT TYPE',
            type
        );

        this.componentType = type;

    },

    /*
    |--------------------------------------------------------------------------
    | Set Reorder Node
    |--------------------------------------------------------------------------
    */

    setDraggedNode(nodeId) {

        BuilderLogger.group(
            'SET DRAGGED NODE',
            BuilderLogger.colors.drag
        );

        BuilderLogger.log(
            'NODE ID',
            nodeId
        );

        this.draggedNodeId = nodeId;

        this.isReordering = true;

        BuilderLogger.success(
            'DRAG STATE SAVED'
        );

        BuilderLogger.log(
            'DRAGGED NODE ID',
            this.draggedNodeId
        );

        BuilderLogger.log(
            'IS REORDERING',
            this.isReordering
        );

        BuilderLogger.end();

    },

    /*
    |--------------------------------------------------------------------------
    | Get Dragged Node
    |--------------------------------------------------------------------------
    */

    getDraggedNode() {

        BuilderLogger.log(
            'GET DRAGGED NODE',
            this.draggedNodeId
        );

        return this.draggedNodeId;

    },

    /*
    |--------------------------------------------------------------------------
    | Is Dragging
    |--------------------------------------------------------------------------
    */

    isDragging() {

        return !!this.draggedNodeId;

    },

    /*
    |--------------------------------------------------------------------------
    | Set Drop Position
    |--------------------------------------------------------------------------
    */

    setDropPosition(position) {

        BuilderLogger.log(
            'SET DROP POSITION',
            position
        );

        this.dropPosition = position;

    },

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    reset() {

        BuilderLogger.group(
            'RESET DRAG STATE',
            BuilderLogger.colors.warning
        );

        BuilderLogger.log(
            'PREVIOUS NODE',
            this.draggedNodeId
        );

        this.componentType = null;

        this.draggedNodeId = null;

        this.isReordering = false;

        this.dropPosition = null;

        BuilderLogger.success(
            'DRAG STATE RESET'
        );

        BuilderLogger.end();

    }

};