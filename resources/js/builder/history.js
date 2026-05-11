window.BuilderHistory = {

    /*
    |------------------------------------------------------------------
    | History Stack
    |------------------------------------------------------------------
    */

    stack: [],

    future: [],

    maxHistory: 100,

    isRestoring: false,

    /*
    |------------------------------------------------------------------
    | Init
    |------------------------------------------------------------------
    */

    init() {

        this.push();

        this.bindKeyboardShortcuts();

    },

    /*
    |------------------------------------------------------------------
    | Push Snapshot
    |------------------------------------------------------------------
    */

    push() {

        if (this.isRestoring) {
            return;
        }

        const snapshot = JSON.stringify(
            Builder.getStructure()
        );

        /*
        |--------------------------------------------------------------
        | Prevent Duplicate Snapshots
        |--------------------------------------------------------------
        */

        const last =
            this.stack[
                this.stack.length - 1
            ];

        if (last === snapshot) {
            return;
        }

        /*
        |--------------------------------------------------------------
        | Push
        |--------------------------------------------------------------
        */

        this.stack.push(snapshot);

        /*
        |--------------------------------------------------------------
        | Limit
        |--------------------------------------------------------------
        */

        if (
            this.stack.length >
            this.maxHistory
        ) {

            this.stack.shift();

        }

        /*
        |--------------------------------------------------------------
        | Clear Redo Stack
        |--------------------------------------------------------------
        */

        this.future = [];

        console.log(
            'HISTORY PUSH:',
            this.stack.length
        );

    },

    /*
    |------------------------------------------------------------------
    | Undo
    |------------------------------------------------------------------
    */

    undo() {

        if (this.stack.length <= 1) {
            return;
        }

        this.isRestoring = true;

        /*
        |--------------------------------------------------------------
        | Current Snapshot
        |--------------------------------------------------------------
        */

        const current =
            this.stack.pop();

        this.future.push(current);

        /*
        |--------------------------------------------------------------
        | Previous Snapshot
        |--------------------------------------------------------------
        */

        const previous =
            this.stack[
                this.stack.length - 1
            ];

        if (!previous) {

            this.isRestoring = false;

            return;

        }

        /*
        |--------------------------------------------------------------
        | Restore
        |--------------------------------------------------------------
        */

        Builder.setStructure(
            JSON.parse(previous)
        );

        /*
        |--------------------------------------------------------------
        | Render
        |--------------------------------------------------------------
        */

        BuilderCanvas.render();

        /*
        |--------------------------------------------------------------
        | Refresh Settings Panel
        |--------------------------------------------------------------
        */

        if (Builder.selectedNodeId) {

            const node =
                Builder.findNodeById(
                    Builder.selectedNodeId
                );

            if (node) {

                const schema =
                    BuilderSchema.get(
                        node.type
                    );

                BuilderSettingsPanel.render(
                    node,
                    schema,
                    node.id
                );

            }

        }

        this.isRestoring = false;

        console.log('UNDO');

    },

    /*
    |------------------------------------------------------------------
    | Redo
    |------------------------------------------------------------------
    */

    redo() {

        if (!this.future.length) {
            return;
        }

        this.isRestoring = true;

        /*
        |--------------------------------------------------------------
        | Restore Snapshot
        |--------------------------------------------------------------
        */

        const snapshot =
            this.future.pop();

        this.stack.push(snapshot);

        Builder.setStructure(
            JSON.parse(snapshot)
        );

        /*
        |--------------------------------------------------------------
        | Render
        |--------------------------------------------------------------
        */

        BuilderCanvas.render();

        /*
        |--------------------------------------------------------------
        | Refresh Settings Panel
        |--------------------------------------------------------------
        */

        if (Builder.selectedNodeId) {

            const node =
                Builder.findNodeById(
                    Builder.selectedNodeId
                );

            if (node) {

                const schema =
                    BuilderSchema.get(
                        node.type
                    );

                BuilderSettingsPanel.render(
                    node,
                    schema,
                    node.id
                );

            }

        }

        this.isRestoring = false;

        console.log('REDO');

    },

    /*
    |------------------------------------------------------------------
    | Keyboard Shortcuts
    |------------------------------------------------------------------
    */

    bindKeyboardShortcuts() {

        document.addEventListener(

            'keydown',

            (event) => {

                /*
                |------------------------------------------------------
                | CTRL + Z
                |------------------------------------------------------
                */

                if (

                    event.ctrlKey
                    &&

                    event.key === 'z'

                ) {

                    event.preventDefault();

                    this.undo();

                }

                /*
                |------------------------------------------------------
                | CTRL + Y
                |------------------------------------------------------
                */

                if (

                    event.ctrlKey
                    &&

                    event.key === 'y'

                ) {

                    event.preventDefault();

                    this.redo();

                }

            }

        );

    }

};

/*
|----------------------------------------------------------------------
| Init
|----------------------------------------------------------------------
*/

document.addEventListener(

    'DOMContentLoaded',

    () => {

        BuilderHistory.init();

    }

);