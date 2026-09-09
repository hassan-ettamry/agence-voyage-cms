window.BuilderSelection = {
    delete() {
        return BuilderStore.selectedNodeId
            ? BuilderOverlayActions.delete(BuilderStore.selectedNodeId)
            : false;
    }
};
