window.BuilderOverlayActions = {
    capabilities(nodeId) {
        const node = Builder.findNodeById(nodeId);
        const parent = node ? BuilderNodeTraversal.findParent(nodeId) : null;
        const siblings = parent ? parent.children : BuilderStore.structure;
        const index = siblings.findIndex(child => child.id === nodeId);
        return {
            node, parent,
            canMoveUp: !!node && index > 0,
            canMoveDown: !!node && index >= 0 && index < siblings.length - 1,
            canDrag: !!node, canDelete: !!node, canDuplicate: !!node
        };
    },

    moveUp(nodeId) { return this.reorder(nodeId, -1); },
    moveDown(nodeId) { return this.reorder(nodeId, 1); },

    reorder(nodeId, direction) {
        const capabilities = this.capabilities(nodeId);
        if (!(direction < 0 ? capabilities.canMoveUp : capabilities.canMoveDown)) return false;
        const siblings = capabilities.parent?.children || BuilderStore.structure;
        const index = siblings.findIndex(node => node.id === nodeId);
        return BuilderNodeMove.move({
            nodeId, targetId: siblings[index + direction].id,
            position: direction < 0 ? 'before' : 'after'
        });
    },

    duplicate(nodeId) {
        if (BuilderHistory.isRestoring) return false;
        BuilderHistory.flushPending();
        const node = Builder.findNodeById(nodeId);
        if (!node) return false;
        const cloned = this.cloneWithNewIds(BuilderComponentUtils.clone(node));
        const placement = BuilderStructureRules.findPlacement(cloned, nodeId, 'after');
        if (!placement) return false;
        BuilderHistory.captureSelection();
        placement.collection.splice(placement.index, 0, cloned);
        BuilderStructureRules.normalizeStore();
        BuilderSelectionManager.queue(cloned.id);
        BuilderEventBus.emit(BuilderEvents.STRUCTURE_UPDATED);
        BuilderHistory.push();
        BuilderRenderManager.requestRender('overlay.duplicate');
        return true;
    },

    cloneWithNewIds(node) {
        node.id = BuilderComponentUtils.generateId();
        node.children = (node.children || []).map(child => this.cloneWithNewIds(child));
        return node;
    },

    edit(nodeId) {
        return BuilderSelectionManager.select(nodeId, null, { source: 'overlay-edit', forceSettings: true });
    },

    delete(nodeId) {
        if (BuilderHistory.isRestoring) return false;
        const node = Builder.findNodeById(nodeId);
        if (!node) return false;
        if (node.children?.length && !window.confirm(
            `Delete this ${BuilderSidebar.componentLabel(node.type)} and all its child elements?`
        )) return false;

        const parent = BuilderNodeTraversal.findParent(nodeId);
        const siblings = parent?.children || BuilderStore.structure;
        const index = siblings.findIndex(child => child.id === nodeId);
        const fallbackId = siblings[index + 1]?.id || siblings[index - 1]?.id || parent?.id || null;
        BuilderHistory.flushPending();
        BuilderHistory.captureSelection();
        if (!BuilderNodes.remove(nodeId)) return false;
        if (fallbackId) BuilderSelectionManager.queue(fallbackId);
        else BuilderSelectionManager.clear();
        BuilderHistory.push();
        BuilderRenderManager.requestRender('overlay.delete');
        return true;
    }
};
