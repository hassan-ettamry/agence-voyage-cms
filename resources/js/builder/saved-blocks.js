window.BuilderSavedBlocks = {
    blocks: [],
    loaded: false,

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
        const name = window.prompt('Saved block name');
        if (!name?.trim()) return;
        const category = window.prompt('Category (optional)', '') || '';
        try {
            const response = await fetch(window.builderSavedBlocksUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                body: JSON.stringify({ name: name.trim(), category: category.trim() || null, structure: [structuredClone(node)] })
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || 'The block could not be saved.');
            this.blocks.unshift(data);
            BuilderTemplateLibrary.open('my-templates');
        } catch (error) {
            window.alert(error.message);
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
        const name = window.prompt('Saved block name', block.name);
        if (!name?.trim()) return;
        const category = window.prompt('Category (optional)', block.category || '') || '';
        await this.update(id, { name: name.trim(), category: category.trim() || null });
    },

    async update(id, payload) {
        try {
            const response = await fetch(`${window.builderSavedBlocksUrl}/${encodeURIComponent(id)}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                body: JSON.stringify(payload)
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || 'The block could not be updated.');
            this.blocks = this.blocks.map(block => block.id === id ? data : block);
            BuilderTemplateLibrary.render();
        } catch (error) {
            window.alert(error.message);
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
            window.alert(error.message);
        }
    }
};
