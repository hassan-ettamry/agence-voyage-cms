window.BuilderNodeMove = {
    // Resolve the actual destination before mutating anything. Preview and drop
    // both use this result, including existing parent compatibility rules.
    resolve({ nodeId, targetId = null, position = 'after' }) {
        const node = Builder.findNodeById(nodeId);
        if (!node || nodeId === targetId) return null;
        if (targetId && BuilderNodeTraversal.isChildOf(targetId, nodeId)) return null;
        const originParent = BuilderNodeTraversal.findParent(nodeId);
        const origin = originParent?.children || BuilderStore.structure;
        const fromIndex = origin.findIndex(child => child.id === nodeId);
        if (fromIndex < 0) return null;
        const placement = targetId
            ? BuilderStructureRules.findPlacement(node, targetId, position)
            : {
                collection: BuilderStore.structure,
                index: position === 'before' ? 0 : BuilderStore.structure.length,
                parent: null, target: null, position, normalized: false
            };
        if (!placement) return null;
        const index = placement.index - (placement.collection === origin && fromIndex < placement.index ? 1 : 0);
        return { ...placement, node, origin, fromIndex, index,
            unchanged: placement.collection === origin && index === fromIndex };
    },

    move(options) {
        if (BuilderHistory.isRestoring) return false;
        // A pending edit normalizes the tree; resolve against the current arrays.
        BuilderHistory.flushPending();
        const destination = this.resolve(options);
        if (!destination || destination.unchanged) return false;
        BuilderHistory.captureSelection();
        destination.origin.splice(destination.fromIndex, 1);
        destination.collection.splice(destination.index, 0, destination.node);
        BuilderStructureRules.normalizeStore();
        BuilderSelectionManager.queue(destination.node.id);
        BuilderEventBus.emit(BuilderEvents.STRUCTURE_UPDATED);
        BuilderHistory.push();
        BuilderRenderManager.requestRender('node-move');
        return true;
    },

    moveToRoot({ nodeId, position = 'after' }) {
        return this.move({ nodeId, targetId: null, position });
    },

    removeNode(nodeId) {
        const parent = BuilderNodeTraversal.findParent(nodeId);
        const siblings = parent?.children || BuilderStore.structure;
        const index = siblings.findIndex(child => child.id === nodeId);
        if (index < 0) return false;
        siblings.splice(index, 1);
        return true;
    }
};
