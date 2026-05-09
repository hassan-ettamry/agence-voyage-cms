window.BuilderSettings = {

    selected: null,

    selectedIndex: null,

    select(element) {

        if (this.selected) {
            this.selected.classList.remove('selected');
        }

        this.selected = element;

        element.classList.add('selected');

        const index =
            parseInt(element.dataset.index);

        this.selectedIndex = index;

        const node =
            Builder.structure[index];

        if (!node) return;

        const component =
            window.builderComponents.find(
                c => c.type === node.type
            );

        if (!component) return;

        BuilderSidebar.switchTab('controls');

        BuilderSettingsPanel.render(
            node,
            component.schema_json,
            index
        );
    },

    clear() {

        if (this.selected) {
            this.selected.classList.remove('selected');
        }

        this.selected = null;

        this.selectedIndex = null;

        document.getElementById(
            'settings-panel'
        ).innerHTML = '';
    },

    deleteSelected() {

        if (this.selectedIndex === null) return;

        Builder.removeComponent(
            this.selectedIndex
        );

        BuilderCanvas.render();

        this.clear();
    }
};