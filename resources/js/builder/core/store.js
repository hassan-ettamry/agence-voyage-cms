window.BuilderStore = {

    /*
    |--------------------------------------------------------------------------
    | Core State
    |--------------------------------------------------------------------------
    */

    structure: [],

    selectedNodeId: null,

    selectedElement: null,

    draggedNodeId: null,

    dropPosition: null,

    /*
    |--------------------------------------------------------------------------
    | Structure
    |--------------------------------------------------------------------------
    */

    getStructure() {

        return this.structure;

    },

    setStructure(structure) {

        this.structure = structure;

        BuilderEventBus.emit(
            'structure.updated'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Add Root Component
    |--------------------------------------------------------------------------
    */

    addRootComponent(component) {

        this.structure.push(component);

        BuilderEventBus.emit(
            'structure.updated'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Update Structure
    |--------------------------------------------------------------------------
    */

    updateStructure(structure) {

        this.structure = structure;

        BuilderEventBus.emit(
            'structure.updated'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Structure
    |--------------------------------------------------------------------------
    */

    clear() {

        this.structure = [];

        BuilderEventBus.emit(
            'structure.updated'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Selection
    |--------------------------------------------------------------------------
    */

    setSelection(nodeId, element = null) {

        this.selectedNodeId = nodeId;

        this.selectedElement = element;

        BuilderEventBus.emit(
            'selection.changed',
            nodeId
        );

    },

    clearSelection() {

        this.selectedNodeId = null;

        this.selectedElement = null;

        BuilderEventBus.emit(
            'selection.changed',
            null
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Drag
    |--------------------------------------------------------------------------
    */

    setDraggedNode(nodeId) {

        this.draggedNodeId = nodeId;

    },

    clearDraggedNode() {

        this.draggedNodeId = null;

    },

    setDropPosition(position) {

        this.dropPosition = position;

    }

};