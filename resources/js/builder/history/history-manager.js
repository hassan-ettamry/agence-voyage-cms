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

    pendingTimer: null,

    pendingKey: null,

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

        BuilderStructureRules.normalizeStore();

        const snapshot = JSON.stringify(
            BuilderStore.getStructure()
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

    schedulePush(key = 'update') {

        if (this.pendingTimer && this.pendingKey !== key) {
            this.flushPending();
        }

        this.pendingKey = key;
        clearTimeout(this.pendingTimer);
        this.pendingTimer = setTimeout(() => this.flushPending(), 450);

    },

    flushPending() {

        if (!this.pendingTimer) return;

        clearTimeout(this.pendingTimer);
        this.pendingTimer = null;
        this.pendingKey = null;
        this.push();

    },

    /*
    |--------------------------------------------------------------------------
    | Undo
    |--------------------------------------------------------------------------
    */

    async undo() {

        this.flushPending();

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

        BuilderStore.setStructure(
            JSON.parse(previous)
        );

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        await BuilderCanvas.render(
            'history.undo'
        );

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

        this.flushPending();

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

        BuilderStore.setStructure(
            JSON.parse(snapshot)
        );

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        await BuilderCanvas.render(
            'history.redo'
        );

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
