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

        return BuilderEditorState.selectedNodeId;

    },

    set selectedNodeId(value) {

        BuilderEditorState.selectedNodeId = value;

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

        return BuilderStore.structure;;

    },

    /*
    |--------------------------------------------------------------------------
    | Structure Methods
    |--------------------------------------------------------------------------
    */

    getStructure() {

        return BuilderStructureState.getStructure();

    },

    setStructure(structure = []) {

        BuilderStore.updateStructure(
            structure
        );

    },

    addComponent(node) {

        BuilderStore.addRootComponent(
            node
        );

    },

    removeComponent(index) {

        BuilderStructureState.removeComponent(
            index
        );

    },

    updateComponent(index, data) {

        BuilderStructureState.updateComponent(
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