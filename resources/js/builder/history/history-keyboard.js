window.BuilderHistoryKeyboard = {
    bound: false,
    bind() {
        if (this.bound) return;
        this.bound = true;
        document.addEventListener('keydown', event => this.handle(event));
    },

    handle(event) {
        const active = document.activeElement;
        if (event.defaultPrevented || active?.closest?.(
            'input, textarea, select, [contenteditable]:not([contenteditable="false"]), [role="dialog"], [aria-modal="true"]'
        )) return;
        const key = event.key.toLowerCase();
        if (event.ctrlKey || event.metaKey) {
            if (key === 'z') {
                event.preventDefault();
                return event.shiftKey ? BuilderHistory.redo() : BuilderHistory.undo();
            }
            if (key === 'y') {
                event.preventDefault();
                return BuilderHistory.redo();
            }
            return;
        }
        if ((key === 'delete' || key === 'backspace') && BuilderStore.selectedNodeId) {
            event.preventDefault();
            return BuilderSelection.delete();
        }
    }
};
