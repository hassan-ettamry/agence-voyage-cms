window.BuilderMediaPicker = {
    targetNodeId: null,
    targetKey: null,
    repeaterTarget: null,
    searchTimer: null,

    async open(nodeId, key, repeaterTarget = null) {
        this.targetNodeId = nodeId;
        this.targetKey = key;
        this.repeaterTarget = repeaterTarget;
        this.ensureModal();
        document.getElementById('builder-media-picker')?.classList.remove('hidden');
        await this.load('');
    },

    close() {
        document.getElementById('builder-media-picker')?.classList.add('hidden');
    },

    choose(url) {
        if (!this.targetNodeId || !this.targetKey || !url) return;
        if (this.repeaterTarget) {
            BuilderRepeater.update(
                this.targetNodeId,
                this.targetKey,
                this.repeaterTarget.index,
                this.repeaterTarget.field,
                url
            );
        } else {
            BuilderSettingsUpdater.updateField(this.targetNodeId, this.targetKey, url);
        }
        BuilderSettingsUpdater.refreshSettingsPanel(this.targetNodeId);
        this.close();
    },

    clear(nodeId, key) {
        BuilderSettingsUpdater.updateField(nodeId, key, '');
        BuilderSettingsUpdater.refreshSettingsPanel(nodeId);
    },

    clearRepeater(nodeId, key, index, field) {
        BuilderRepeater.update(nodeId, key, index, field, '');
        BuilderSettingsUpdater.refreshSettingsPanel(nodeId);
    },

    search(value) {
        clearTimeout(this.searchTimer);
        this.searchTimer = setTimeout(() => this.load(value), 250);
    },

    async load(search = '') {
        const grid = document.getElementById('builder-media-picker-grid');
        if (!grid) return;
        grid.innerHTML = '<div class="col-span-full py-12 text-center text-sm text-slate-400">Loading media…</div>';

        try {
            const endpoint = window.builderMediaPickerUrl || '/media/picker';
            const response = await fetch(`${endpoint}?search=${encodeURIComponent(search)}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Media library could not be loaded.');
            const assets = await response.json();
            grid.innerHTML = assets.length ? assets.map(asset => this.card(asset)).join('')
                : '<div class="col-span-full py-12 text-center text-sm text-slate-400">No images found.</div>';
        } catch (error) {
            grid.innerHTML = `<div class="col-span-full py-12 text-center text-sm text-red-600">${BuilderHtmlEscape.html(error.message)}</div>`;
        }
    },

    card(asset) {
        const url = BuilderHtmlEscape.attribute(asset.url || '');
        const title = BuilderHtmlEscape.html(asset.title || asset.original_name || 'Media image');
        return `
            <button type="button" data-action="choose-builder-media" data-media-url="${url}" class="overflow-hidden rounded-lg border border-slate-200 bg-white text-left transition hover:border-blue-500 hover:shadow-sm">
                <img src="${url}" alt="" class="aspect-video w-full bg-slate-100 object-cover">
                <span class="block truncate px-3 py-2 text-xs font-medium text-slate-700">${title}</span>
            </button>
        `;
    },

    ensureModal() {
        if (document.getElementById('builder-media-picker')) return;
        const modal = document.createElement('div');
        modal.id = 'builder-media-picker';
        modal.className = 'fixed inset-0 z-[100] hidden bg-slate-950/55 p-4';
        modal.innerHTML = `
            <div class="mx-auto flex h-full max-h-[760px] max-w-5xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex items-center gap-3 border-b border-slate-200 p-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Choose an image</h2>
                        <p class="text-xs text-slate-500">Images from the current agency media library.</p>
                    </div>
                    <input type="search" data-builder-media-search placeholder="Search media…" class="ml-auto min-h-10 w-64 rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-blue-500">
                    <button type="button" data-action="close-builder-media" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Close</button>
                </div>
                <div id="builder-media-picker-grid" class="grid flex-1 grid-cols-2 gap-3 overflow-y-auto p-4 sm:grid-cols-3 lg:grid-cols-4"></div>
            </div>
        `;
        modal.querySelector('[data-builder-media-search]')?.addEventListener('input', event => this.search(event.target.value));
        document.body.appendChild(modal);
    }
};
