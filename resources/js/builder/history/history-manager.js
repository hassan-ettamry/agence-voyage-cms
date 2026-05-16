window.BuilderHistory = {

    /*
    |--------------------------------------------------------------------------
    | History Stack
    |--------------------------------------------------------------------------
    */

    stack: [],

    future: [],

    maxHistory: 100,

    isRestoring: false,

    /*
    |--------------------------------------------------------------------------
    | Init
    |--------------------------------------------------------------------------
    */

    init() {

        this.push();

    },

    /*
    |--------------------------------------------------------------------------
    | Push Snapshot
    |--------------------------------------------------------------------------
    */

    push() {

        if (this.isRestoring) {
            return;
        }

        const snapshot = JSON.stringify(
            Builder.getStructure()
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Snapshots
        |--------------------------------------------------------------------------
        */

        const last =
            this.stack[
                this.stack.length - 1
            ];

        if (last === snapshot) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Push
        |--------------------------------------------------------------------------
        */

        this.stack.push(snapshot);

        /*
        |--------------------------------------------------------------------------
        | Limit
        |--------------------------------------------------------------------------
        */

        if (
            this.stack.length >
            this.maxHistory
        ) {

            this.stack.shift();

        }

        /*
        |--------------------------------------------------------------------------
        | Clear Redo Stack
        |--------------------------------------------------------------------------
        */

        this.future = [];

        BuilderLogger.group(
            'HISTORY PUSH',
            BuilderLogger.colors.history
        );

        BuilderLogger.log(
            'STACK SIZE',
            this.stack.length
        );

        BuilderLogger.log(
            'FUTURE SIZE',
            this.future.length
        );

        BuilderLogger.end();

    },

    /*
    |--------------------------------------------------------------------------
    | Undo
    |--------------------------------------------------------------------------
    */

    async undo() {

        BuilderLogger.warn(
            'UNDO'
        );

        if (this.stack.length <= 1) {
            return;
        }

        this.isRestoring = true;

        /*
        |--------------------------------------------------------------------------
        | Current Snapshot
        |--------------------------------------------------------------------------
        */

        const current =
            this.stack.pop();

        this.future.push(current);

        /*
        |--------------------------------------------------------------------------
        | Previous Snapshot
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Restore
        |--------------------------------------------------------------------------
        */

        Builder.setStructure(
            JSON.parse(previous)
        );

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        await BuilderCanvas.render();

        /*
        |--------------------------------------------------------------------------
        | Refresh Settings Panel
        |--------------------------------------------------------------------------
        */

        if (BuilderStore.selectedNodeId) {

            const node =
                Builder.findNodeById(
                    BuilderStore.selectedNodeId
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

    },

    /*
    |--------------------------------------------------------------------------
    | Redo
    |--------------------------------------------------------------------------
    */

    async redo() {

        BuilderLogger.warn(
            'REDO'
        );

        if (!this.future.length) {
            return;
        }

        this.isRestoring = true;

        /*
        |--------------------------------------------------------------------------
        | Restore Snapshot
        |--------------------------------------------------------------------------
        */

        const snapshot =
            this.future.pop();

        this.stack.push(snapshot);

        Builder.setStructure(
            JSON.parse(snapshot)
        );

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        await BuilderCanvas.render();

        /*
        |--------------------------------------------------------------------------
        | Refresh Settings Panel
        |--------------------------------------------------------------------------
        */

        if (BuilderStore.selectedNodeId) {

            const node =
                Builder.findNodeById(
                    BuilderStore.selectedNodeId
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

    }

};