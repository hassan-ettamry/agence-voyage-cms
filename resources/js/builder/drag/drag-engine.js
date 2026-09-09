window.BuilderDragDrop = {
    start(event, type) {
        BuilderDragStart.start(event, type);
    },

    resolve(event) {
        if (!event.target.closest('#canvas')) return null;
        const nodeId = BuilderDragState.getDraggedNode();
        const type = BuilderDragState.componentType;
        if (!nodeId && !type) return null;
        const element = event.target.closest('[data-node-id]');
        const canvas = BuilderCanvasUtils.getCanvas();
        const position = element
            ? BuilderDragHitbox.detect(element, event)
            : (event.clientY - canvas.getBoundingClientRect().top < canvas.getBoundingClientRect().height / 2 ? 'before' : 'after');
        if (nodeId) {
            return BuilderNodeMove.resolve({ nodeId, targetId: element?.dataset.nodeId || null, position });
        }
        if (!element) {
            return { collection: BuilderStore.structure, parent: null, target: null, position,
                index: position === 'before' ? 0 : BuilderStore.structure.length };
        }
        return BuilderStructureRules.findPlacement({ type, props: {}, children: [] },
            element.dataset.nodeId, position);
    },

    allowDrop(event) {
        if (!BuilderDragState.getDraggedNode() && !BuilderDragState.componentType) return;
        event.preventDefault();
        const placement = this.resolve(event);
        if (!placement || placement.unchanged) {
            if (event.dataTransfer) event.dataTransfer.dropEffect = 'none';
            BuilderDragPreview.hide();
            return;
        }
        if (event.dataTransfer) event.dataTransfer.dropEffect = BuilderDragState.getDraggedNode() ? 'move' : 'copy';
        const target = placement.target
            ? BuilderSelectionManager.findElement(placement.target.id)
            : BuilderCanvasUtils.getCanvas();
        BuilderDragState.setDropPosition(placement.position);
        BuilderDragPreview.show(target, placement.position);
    },

    drop(event) {
        return BuilderDragDropAction.drop(event);
    }
};
