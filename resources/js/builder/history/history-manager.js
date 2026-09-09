window.BuilderHistory = {
    stack: [],
    future: [],
    selectionStack: [],
    futureSelections: [],
    maxHistory: 100,
    isRestoring: false,
    pendingTimer: null,
    pendingKey: null,

    init() { this.push(); },

    captureSelection() {
        if (this.stack.length) this.selectionStack[this.stack.length - 1] = BuilderStore.selectedNodeId || null;
    },

    push() {
        if (this.isRestoring) return;
        BuilderStructureRules.normalizeStore();
        const snapshot = JSON.stringify(BuilderStore.getStructure());
        if (this.stack[this.stack.length - 1] === snapshot) return;
        this.stack.push(snapshot);
        this.selectionStack.push(BuilderStore.selectedNodeId || null);
        if (this.stack.length > this.maxHistory) {
            this.stack.shift();
            this.selectionStack.shift();
        }
        this.future = [];
        this.futureSelections = [];
    },

    schedulePush(key = 'update') {
        if (this.pendingTimer && this.pendingKey !== key) this.flushPending();
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

    async restore(snapshot, selectionId, source) {
        this.isRestoring = true;
        try {
            BuilderStore.setStructure(JSON.parse(snapshot));
            if (window.BuilderSelectionManager) {
                BuilderSelectionManager.queue(selectionId, { forceSettings: true });
            }
            await BuilderCanvas.render(source);
            window.BuilderStorage?.refreshDirtyState();
        } finally {
            this.isRestoring = false;
        }
    },

    async undo() {
        if (this.isRestoring) return false;
        this.flushPending();
        if (this.stack.length <= 1) return false;
        this.future.push(this.stack.pop());
        this.futureSelections.push(this.selectionStack.pop());
        await this.restore(this.stack[this.stack.length - 1],
            this.selectionStack[this.selectionStack.length - 1], 'history.undo');
        return true;
    },

    async redo() {
        if (this.isRestoring) return false;
        this.flushPending();
        if (!this.future.length) return false;
        const snapshot = this.future.pop();
        const selectionId = this.futureSelections.pop();
        this.stack.push(snapshot);
        this.selectionStack.push(selectionId);
        await this.restore(snapshot, selectionId, 'history.redo');
        return true;
    }
};
