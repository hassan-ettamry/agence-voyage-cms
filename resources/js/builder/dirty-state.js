export class DirtyTracker {
    constructor(snapshot = '') {
        this.savedSnapshot = snapshot;
    }

    markSaved(snapshot) {
        this.savedSnapshot = snapshot;
    }

    isDirty(snapshot) {
        return snapshot !== this.savedSnapshot;
    }
}

if (typeof window !== 'undefined') window.BuilderDirtyTracker = DirtyTracker;
