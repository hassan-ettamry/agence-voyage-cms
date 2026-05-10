window.Builder = {

    /*
    |--------------------------------------------------------------------------
    | UI STATE
    |--------------------------------------------------------------------------
    */

    selectedElement: null,

    selectedNodeId: null,

    viewport: 'desktop',

    pageId: window.pageId || null,

    /*
    |--------------------------------------------------------------------------
    | PAGE STRUCTURE STATE
    |--------------------------------------------------------------------------
    */

    structure: [],

    /*
    |--------------------------------------------------------------------------
    | Init
    |--------------------------------------------------------------------------
    */

    init() {

        this.structure = Array.isArray(window.initialStructure)
            ? window.initialStructure
            : [];

        console.log(
            'Builder initialized:',
            this.structure
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Get Structure
    |--------------------------------------------------------------------------
    */

    getStructure() {

        return this.structure;

    },

    /*
    |--------------------------------------------------------------------------
    | Set Structure
    |--------------------------------------------------------------------------
    */

    setStructure(structure = []) {

        this.structure = structure;

    },

    /*
    |--------------------------------------------------------------------------
    | Add Component
    |--------------------------------------------------------------------------
    */

    addComponent(node) {

        this.structure.push(node);

    },

    /*
    |--------------------------------------------------------------------------
    | Add Child Component
    |--------------------------------------------------------------------------
    */

    addChild(parentId, child) {

        const parent =
            this.findNodeById(parentId);

        if (!parent) return;

        if (!parent.children) {
            parent.children = [];
        }

        parent.children.push(child);

    },

    /*
    |--------------------------------------------------------------------------
    | Remove Component
    |--------------------------------------------------------------------------
    */

    removeComponent(index) {

        this.structure.splice(index, 1);

    },

    /*
    |--------------------------------------------------------------------------
    | Update Component
    |--------------------------------------------------------------------------
    */

    updateComponent(index, data) {

        if (!this.structure[index]) return;

        this.structure[index] = {
            ...this.structure[index],
            ...data
        };

    },

    /*
    |--------------------------------------------------------------------------
    | Find Node By ID
    |--------------------------------------------------------------------------
    */

    findNodeById(id, nodes = this.structure) {

        for (const node of nodes) {

            if (node.id === id) {
                return node;
            }

            if (node.children?.length) {

                const found =
                    this.findNodeById(
                        id,
                        node.children
                    );

                if (found) {
                    return found;
                }

            }

        }

        return null;

    },

    /*
    |--------------------------------------------------------------------------
    | Recursive Walker
    |--------------------------------------------------------------------------
    */

    walk(nodes = this.structure, callback) {

        nodes.forEach(node => {

            callback(node);

            if (node.children?.length) {

                this.walk(
                    node.children,
                    callback
                );

            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */

    clear() {

        this.structure = [];

    }

};

/*
|--------------------------------------------------------------------------
| Init Builder
|--------------------------------------------------------------------------
*/

window.Builder.init();