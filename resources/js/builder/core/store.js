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
    | Init
    |--------------------------------------------------------------------------
    */

    init(structure = window.initialStructure) {

        this.setStructure(
            structure
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Structure
    |--------------------------------------------------------------------------
    */

    getStructure() {

        return this.structure;

    },

    setStructure(structure = []) {

        this.structure = Array.isArray(structure)
            ? structure
            : [];

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
    | Remove Root Component
    |--------------------------------------------------------------------------
    */

    removeRootComponent(index) {

        if (!this.structure[index]) {
            return;
        }

        this.structure.splice(index, 1);

        BuilderEventBus.emit(
            'structure.updated'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Update Root Component
    |--------------------------------------------------------------------------
    */

    updateRootComponent(index, data) {

        if (!this.structure[index]) {
            return;
        }

        this.structure[index] = {

            ...this.structure[index],

            ...data

        };

        BuilderEventBus.emit(
            'structure.updated'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Update Structure
    |--------------------------------------------------------------------------
    */

    updateStructure(structure = []) {

        this.setStructure(
            structure
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
