window.Builder = {

    /*
    |------------------------------------------------------------------
    | Editor State
    |------------------------------------------------------------------
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
    |------------------------------------------------------------------
    | Structure
    |------------------------------------------------------------------
    */

    get structure() {

        return BuilderStructureState.structure;

    },

    /*
    |------------------------------------------------------------------
    | Structure Methods
    |------------------------------------------------------------------
    */

    getStructure() {

        return BuilderStructureState.getStructure();

    },

    setStructure(structure = []) {

        BuilderStructureState.setStructure(
            structure
        );

    },

    addComponent(node) {

        BuilderStructureState.addComponent(
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

        BuilderStructureState.clear();

    },

    /*
    |------------------------------------------------------------------
    | Utils
    |------------------------------------------------------------------
    */

    findNodeById(id, nodes) {

        return BuilderStateUtils.findNodeById(
            id,
            nodes
        );

    },

    walk(nodes, callback) {

        return BuilderStateUtils.walk(
            nodes,
            callback
        );

    }

};