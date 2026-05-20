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

        this.structure = BuilderStructureRules.normalizeTree(
            Array.isArray(structure)
                ? structure
                : []
        );

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Add Root Component
    |--------------------------------------------------------------------------
    */

    addRootComponent(component) {

        this.structure.push(component);

        BuilderStructureRules.normalizeStore();

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
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
            BuilderEvents.STRUCTURE_UPDATED
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

        BuilderStructureRules.normalizeStore();

        BuilderEventBus.emit(
            BuilderEvents.STRUCTURE_UPDATED
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
            BuilderEvents.STRUCTURE_UPDATED
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
            BuilderEvents.SELECTION_CHANGED,
            nodeId
        );

    },

    clearSelection() {

        this.selectedNodeId = null;

        this.selectedElement = null;

        BuilderEventBus.emit(
            BuilderEvents.SELECTION_CHANGED,
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
