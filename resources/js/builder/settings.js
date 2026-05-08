window.BuilderSettings = {

    selected: null,

    select(element, type = 'Element') {

        if (this.selected) {
            this.selected.classList.remove('selected');
        }

        this.selected = element;

        element.classList.add('selected');

        document.getElementById('settings-empty')?.classList.add('hidden');

        document.getElementById('settings-content')?.classList.remove('hidden');
    },

    clear() {

        if (this.selected) {
            this.selected.classList.remove('selected');
        }

        this.selected = null;

        document.getElementById('settings-empty')?.classList.remove('hidden');

        document.getElementById('settings-content')?.classList.add('hidden');
    },

    applyStyle(property, value) {

        if (!this.selected) return;

        this.selected.style[property] = value;
    },

    remove(element) {

        element.remove();

        this.clear();

        BuilderCanvas.refresh();
    },

    deleteSelected() {

        if (!this.selected) return;

        this.selected.remove();

        this.clear();

        BuilderCanvas.refresh();
    }
};