window.BuilderSavedBlocks = {
    blocks: [],
    loaded: false,
    formState: null,

    async init() {
        if (!this.loaded) await this.load();
    },

    csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    },

    async load() {
        try {
            const response = await fetch(window.builderSavedBlocksUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Saved blocks could not be loaded.');
            this.blocks = await response.json();
            this.loaded = true;
            BuilderTemplateLibrary?.render();
        } catch (error) {
            console.error(error);
        }
    },

    templates(search = '', category = 'all') {
        return this.blocks.filter(block => {
            const matchesSearch = !search || block.name.toLowerCase().includes(search);
            const matchesCategory = category === 'all' || block.category === category;
            return matchesSearch && matchesCategory;
        }).map(block => ({ id: `saved:${block.id}`, title: block.name, category: block.category || 'My Blocks', savedBlock: block }));
    },

    async saveSection(nodeId) {
        const node = Builder.findNodeById(nodeId);
        if (!node || node.type !== 'section') return;
        this.openForm({ mode: 'create', nodeId, name: '', category: '' });
    },

    async submitForm() {
        const name = document.getElementById('saved-block-name')?.value.trim() || '';
        const category = document.getElementById('saved-block-category')?.value.trim() || '';
        const error = document.getElementById('saved-block-form-error');
        if (!name) {
            if (error) error.textContent = 'Enter a name for this block.';
            return;
        }

        if (this.formState?.mode === 'rename') {
            await this.update(this.formState.id, { name, category: category || null }, error);
            return;
        }

        const node = Builder.findNodeById(this.formState?.nodeId);
        if (!node || node.type !== 'section') {
            if (error) error.textContent = 'The selected Section is no longer available.';
            return;
        }

        try {
            const response = await fetch(window.builderSavedBlocksUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                body: JSON.stringify({ name, category: category || null, structure: [structuredClone(node)] })
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || 'The block could not be saved.');
            this.blocks.unshift(data);
            this.closeForm();
            BuilderTemplateLibrary.open('my-templates');
        } catch (error) {
            const output = document.getElementById('saved-block-form-error');
            if (output) output.textContent = error.message;
        }
    },

    insert(id) {
        const block = this.blocks.find(item => item.id === id);
        if (!block) return;
        const nodes = BuilderTemplateLibrary.cloneNodes(block.structure || []);
        nodes.forEach(node => BuilderStore.addRootComponent(node));
        if (nodes[0]) BuilderStore.setSelection(nodes[0].id, null);
        BuilderHistory.push();
        BuilderRenderManager.requestRender('saved-block.insert');
        BuilderTemplateLibrary.close();
    },

    async rename(id) {
        const block = this.blocks.find(item => item.id === id);
        if (!block) return;
        this.openForm({ mode: 'rename', id, name: block.name, category: block.category || '' });
    },

    async update(id, payload, errorOutput = null) {
        try {
            const response = await fetch(`${window.builderSavedBlocksUrl}/${encodeURIComponent(id)}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                body: JSON.stringify(payload)
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || 'The block could not be updated.');
            this.blocks = this.blocks.map(block => block.id === id ? data : block);
            this.closeForm();
            BuilderTemplateLibrary.render();
        } catch (error) {
            if (errorOutput) errorOutput.textContent = error.message;
        }
    },

    async remove(id) {
        const block = this.blocks.find(item => item.id === id);
        if (!block || !window.confirm(`Delete “${block.name}”? Existing pages will not be changed.`)) return;
        try {
            const response = await fetch(`${window.builderSavedBlocksUrl}/${encodeURIComponent(id)}`, {
                method: 'DELETE',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf() }
            });
            if (!response.ok) throw new Error('The block could not be deleted.');
            this.blocks = this.blocks.filter(item => item.id !== id);
            BuilderTemplateLibrary.render();
        } catch (error) {
            console.error(error);
        }
    },

    openForm(state) {
        this.formState = state;
        this.ensureForm();
        document.getElementById('saved-block-form-title').textContent = state.mode === 'rename' ? 'Rename saved block' : 'Save Section as a block';
        document.getElementById('saved-block-name').value = state.name || '';
        document.getElementById('saved-block-category').value = state.category || '';
        document.getElementById('saved-block-form-error').textContent = '';
        document.getElementById('builder-saved-block-form').classList.remove('hidden');
        document.getElementById('saved-block-name').focus();
    },

    closeForm() {
        this.formState = null;
        document.getElementById('builder-saved-block-form')?.classList.add('hidden');
    },

    ensureForm() {
        if (document.getElementById('builder-saved-block-form')) return;
        const modal = document.createElement('div');
        modal.id = 'builder-saved-block-form';
        modal.className = 'fixed inset-0 z-[110] hidden bg-slate-950/55 p-4';
        modal.innerHTML = `
            <div class="mx-auto mt-[12vh] w-full max-w-md rounded-xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="saved-block-form-title">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="saved-block-form-title" class="text-lg font-semibold text-slate-900"></h2>
                        <p class="mt-1 text-xs text-slate-500">The inserted copy remains independent from this saved block.</p>
                    </div>
                    <button type="button" data-action="close-saved-block-form" aria-label="Close saved block form" class="rounded px-2 py-1 text-slate-500 hover:bg-slate-100">×</button>
                </div>
                <label class="mt-5 block text-xs font-semibold text-slate-700">Name
                    <input id="saved-block-name" type="text" maxlength="120" class="mt-1 min-h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-blue-500">
                </label>
                <label class="mt-4 block text-xs font-semibold text-slate-700">Category <span class="font-normal text-slate-400">(optional)</span>
                    <input id="saved-block-category" type="text" maxlength="80" class="mt-1 min-h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-blue-500">
                </label>
                <p id="saved-block-form-error" class="mt-3 min-h-5 text-xs text-red-600" role="alert"></p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" data-action="close-saved-block-form" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="button" data-action="submit-saved-block-form" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save block</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
};
