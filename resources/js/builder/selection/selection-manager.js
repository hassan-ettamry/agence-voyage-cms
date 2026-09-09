window.BuilderSelectionManager = {
    settingsNodeId: null,

    select(nodeId, element = null, options = {}) {
        const node = Builder.findNodeById(nodeId);
        const target = element || this.findElement(nodeId);
        if (!node || !target) return false;

        const changed = BuilderStore.selectedNodeId !== nodeId;
        BuilderStore.setSelection(nodeId, target);
        this.applyVisuals(nodeId, target);
        this.syncSettings(node, changed || options.forceSettings);
        BuilderSidebar.switchTab('controls');
        this.showSelectionToolbar(nodeId, target);
        if (options.scroll) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return true;
    },

    // State can reference a copied node before its canvas response arrives.
    // Never leave controls bound to the previous node while waiting for it.
    queue(nodeId, { forceSettings = false } = {}) {
        if (!nodeId || !Builder.findNodeById(nodeId)) {
            this.clear();
            return false;
        }
        if (this.settingsNodeId !== nodeId || forceSettings) this.clearSettingsPanel();
        BuilderStore.setSelection(nodeId, null);
        BuilderCanvasUtils.clearSelectionStyles();
        BuilderSidebar.setTitle(BuilderSidebar.componentLabel(Builder.findNodeById(nodeId).type));
        BuilderOverlay.hide();
        return true;
    },

    syncSettings(node, force = false) {
        if (!force && this.settingsNodeId === node.id) return;
        BuilderSidebar.setTitle(BuilderSidebar.componentLabel(node.type));
        const schema = BuilderSchema.get(node.type);
        const tabs = Object.keys(schema?.tabs || {});
        const hasTabPreference = tabs.some(tab => Object.prototype.hasOwnProperty.call(
            BuilderSettingsPanel.openSettingTabs,
            BuilderSettingsPanel.settingTabStateKey(node.id, tab)
        ));
        if (!hasTabPreference && tabs.length) {
            BuilderSettingsPanel.openSettingTabs[
                BuilderSettingsPanel.settingTabStateKey(node.id, tabs[0])
            ] = true;
        }
        BuilderSettingsPanel.render(node, schema, node.id);
        this.settingsNodeId = node.id;
    },

    selectParent(nodeId = BuilderStore.selectedNodeId) {
        const parent = BuilderNodeTraversal.findParent(nodeId);
        return parent ? this.select(parent.id, null, { source: 'parent' }) : false;
    },

    applyVisuals(nodeId, element = null) {
        const target = element || this.findElement(nodeId);
        if (!target) return false;
        BuilderCanvasUtils.clearSelectionStyles();
        const parent = BuilderNodeTraversal.findParent(nodeId);
        if (parent) {
            BuilderOverlayTheme.applyOutline(this.findElement(parent.id), 'parent',
                BuilderOverlayTheme.pathOutlineOptions(1));
        }
        BuilderCanvasUtils.applySelectionStyles(target);
        return true;
    },

    showSelectionToolbar(nodeId, element = null) {
        const target = element || this.findElement(nodeId);
        if (!target || nodeId !== BuilderStore.selectedNodeId) return false;
        BuilderOverlay.show(target, nodeId, { mode: 'selected' });
        return true;
    },

    restoreVisuals() {
        const nodeId = BuilderStore.selectedNodeId;
        const node = nodeId && Builder.findNodeById(nodeId);
        const target = node && this.findElement(nodeId);
        if (!target) return false;
        BuilderStore.selectedElement = target;
        this.applyVisuals(nodeId, target);
        this.syncSettings(node);
        this.showSelectionToolbar(nodeId, target);
        return true;
    },

    findElement(nodeId) {
        if (!nodeId) return null;
        return document.querySelector(`#canvas [data-node-id="${CSS.escape(nodeId)}"]`);
    },

    clear() {
        BuilderStore.clearSelection();
        BuilderCanvasUtils.clearSelectionStyles();
        BuilderOverlay.hide();
        BuilderSidebar.setTitle();
        this.clearSettingsPanel();
    },

    clearSettingsPanel() {
        this.settingsNodeId = null;
        const panel = document.getElementById('settings-panel');
        if (panel) panel.innerHTML = '';
    }
};
