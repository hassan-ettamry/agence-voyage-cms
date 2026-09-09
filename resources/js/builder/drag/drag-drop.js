window.BuilderDragDropAction = {
    drop(event) {
        if (!BuilderDragState.getDraggedNode() && !BuilderDragState.componentType) return false;
        event.preventDefault();
        event.stopPropagation();
        try {
            if (BuilderHistory.isRestoring) return false;
            BuilderHistory.flushPending();
            const placement = BuilderDragDrop.resolve(event);
            if (!placement || placement.unchanged) return false;
            const nodeId = BuilderDragState.getDraggedNode();
            if (nodeId) {
                return BuilderNodeMove.move({
                    nodeId, targetId: placement.target?.id || null, position: placement.position
                });
            }
            const component = BuilderComponentFactory.create(BuilderDragState.componentType);
            if (!component) return false;
            BuilderHistory.captureSelection();
            placement.collection.splice(placement.index, 0, component);
            BuilderStructureRules.normalizeStore();
            BuilderSelectionManager.queue(component.id);
            BuilderEventBus.emit(BuilderEvents.STRUCTURE_UPDATED);
            BuilderHistory.push();
            BuilderRenderManager.requestRender('drag-drop.add');
            return true;
        } finally {
            BuilderDragReorder.end();
        }
    }
};
