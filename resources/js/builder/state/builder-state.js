window.Builder = {

    /*
    |--------------------------------------------------------------------------
    | Editor State
    |--------------------------------------------------------------------------
    */

    get selectedElement() {

        return BuilderEditorState.selectedElement;

    },

    set selectedElement(value) {

        BuilderEditorState.selectedElement = value;

    },

    get selectedNodeId() {

        return BuilderStore.selectedNodeId;

    },

    set selectedNodeId(value) {

        BuilderStore.selectedNodeId = value;

    },

    get viewport() {

        return BuilderEditorState.viewport;

    },

    set viewport(value) {

        BuilderEditorState.viewport = value;

    },

    get pageId() {

        return BuilderEditorState.pageId;

    },

    /*
    |--------------------------------------------------------------------------
    | Structure
    |--------------------------------------------------------------------------
    */

    get structure() {

        return BuilderStore.getStructure();

    },

    /*
    |--------------------------------------------------------------------------
    | Structure Methods
    |--------------------------------------------------------------------------
    */

    getStructure() {

        return BuilderStore.getStructure();

    },

    setStructure(structure = []) {

        BuilderStore.setStructure(
            structure
        );

    },

    addComponent(node) {

        BuilderStore.addRootComponent(
            node
        );

    },

    removeComponent(index) {

        BuilderStore.removeRootComponent(
            index
        );

    },

    updateComponent(index, data) {

        BuilderStore.updateRootComponent(
            index,
            data
        );

    },

    clear() {

        BuilderStore.clear();
    
    },

    /*
    |--------------------------------------------------------------------------
    | Traversal
    |--------------------------------------------------------------------------
    */

    findNodeById(id, nodes) {

        return BuilderNodeTraversal.findNodeById(
            id,
            nodes
        );

    },

    walk(nodes, callback) {

        return BuilderNodeTraversal.walk(
            nodes,
            callback
        );

    }

};
