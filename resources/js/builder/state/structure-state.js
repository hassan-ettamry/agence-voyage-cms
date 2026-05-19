window.BuilderStructureState = {

    /*
    |------------------------------------------------------------------
    | Init
    |------------------------------------------------------------------
    */

    init() {

        BuilderStore.init();

        console.log(

            'Builder Structure Initialized:',

            BuilderStore.getStructure()

        );

    },

    /*
    |------------------------------------------------------------------
    | Get Structure
    |------------------------------------------------------------------
    */

    getStructure() {

        return BuilderStore.getStructure();

    },

    /*
    |------------------------------------------------------------------
    | Set Structure
    |------------------------------------------------------------------
    */

    setStructure(structure = []) {

        BuilderStore.setStructure(
            structure
        );

    },

    /*
    |------------------------------------------------------------------
    | Add Component
    |------------------------------------------------------------------
    */

    addComponent(node) {

        BuilderStore.addRootComponent(
            node
        );

    },

    /*
    |------------------------------------------------------------------
    | Remove Component
    |------------------------------------------------------------------
    */

    removeComponent(index) {

        BuilderStore.removeRootComponent(
            index
        );

    },

    /*
    |------------------------------------------------------------------
    | Update Component
    |------------------------------------------------------------------
    */

    updateComponent(index, data) {

        BuilderStore.updateRootComponent(
            index,
            data
        );

    },

    /*
    |------------------------------------------------------------------
    | Clear
    |------------------------------------------------------------------
    */

    clear() {

        BuilderStore.clear();

    }

};
