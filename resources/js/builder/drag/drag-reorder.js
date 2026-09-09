window.BuilderDragReorder = {
    activeElement: null,
    start(event, nodeId) {
        if (!Builder.findNodeById(nodeId) || BuilderHistory.isRestoring) {
            event.preventDefault();
            return false;
        }
        event.stopPropagation();
        BuilderSelectionManager.select(nodeId);
        BuilderDragState.setDraggedNode(nodeId);
        event.dataTransfer.setData('reorder-node-id', nodeId);
        event.dataTransfer.effectAllowed = 'move';
        this.activeElement = BuilderSelectionManager.findElement(nodeId);
        if (this.activeElement) this.activeElement.dataset.builderDragging = 'true';
        return true;
    },

    end(event) {
        event?.stopPropagation();
        BuilderDragPreview.hide();
        if (this.activeElement) delete this.activeElement.dataset.builderDragging;
        this.activeElement = null;
        BuilderDragState.reset();
        BuilderSelectionManager.restoreVisuals();
    }
};
