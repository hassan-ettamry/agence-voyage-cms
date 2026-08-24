window.BuilderStorage = {
    tracker: null,
    savePromise: null,
    initialized: false,

    init() {
        if (this.initialized) return;
        this.initialized = true;
        this.tracker = new BuilderDirtyTracker(this.snapshot());

        [BuilderEvents.STRUCTURE_UPDATED, BuilderEvents.NODE_UPDATED].forEach(event => {
            BuilderEventBus.on(event, () => this.refreshDirtyState());
        });

        window.addEventListener('beforeunload', event => {
            if (!this.isDirty()) return;
            event.preventDefault();
            event.returnValue = '';
        });

        this.setStatus('All changes saved', 'success');
    },

    snapshot() {
        return JSON.stringify({
            structure: window.BuilderStore ? BuilderStore.getStructure() : (window.initialStructure || []),
            title: document.getElementById('builder-page-title')?.value || '',
            slug: document.getElementById('builder-page-slug')?.value || ''
        });
    },

    isDirty() {
        return this.tracker ? this.tracker.isDirty(this.snapshot()) : false;
    },

    refreshDirtyState() {
        if (this.isDirty()) this.setStatus('Unsaved changes', 'neutral');
    },

    pageChanged(field, value) {
        this.clearFieldError(field);
        if (field === 'title') {
            BuilderSidebar.setTitle(value);
        }
        this.refreshDirtyState();
    },

    setStatus(message, tone = 'neutral') {
        const status = document.getElementById('builder-save-status');
        if (!status) return;
        status.textContent = message;
        status.classList.remove('text-slate-500', 'text-blue-600', 'text-emerald-700', 'text-red-600');
        status.classList.add({ saving: 'text-blue-600', success: 'text-emerald-700', error: 'text-red-600' }[tone] || 'text-slate-500');
    },

    clearErrors() {
        document.querySelectorAll('[data-page-error]').forEach(element => {
            element.textContent = '';
            element.classList.add('hidden');
        });
    },

    clearFieldError(field) {
        const element = document.querySelector(`[data-page-error="${CSS.escape(field)}"]`);
        element?.classList.add('hidden');
    },

    showErrors(errors = {}) {
        Object.entries(errors).forEach(([field, messages]) => {
            const element = document.querySelector(`[data-page-error="${CSS.escape(field)}"]`);
            if (!element) return;
            element.textContent = Array.isArray(messages) ? messages[0] : String(messages);
            element.classList.remove('hidden');
        });
    },

    async save() {
        if (this.savePromise) return this.savePromise;

        this.savePromise = this.performSave();
        try {
            return await this.savePromise;
        } finally {
            this.savePromise = null;
        }
    },

    async performSave() {
        const buttons = document.querySelectorAll('[data-action="save-page"], [data-action="preview-page"], [data-action="publish-page"]');
        try {
            BuilderHistory.flushPending();
            this.clearErrors();
            this.setStatus('Saving…', 'saving');
            buttons.forEach(button => button.setAttribute('disabled', 'disabled'));
            BuilderStructureRules.normalizeStore();

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('title', document.getElementById('builder-page-title')?.value || '');
            formData.append('slug', document.getElementById('builder-page-slug')?.value || '');
            formData.append('structure', JSON.stringify(BuilderStore.getStructure()));

            const response = await fetch(`/pages/${window.pageId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    Accept: 'application/json'
                },
                body: formData
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                this.showErrors(data.errors || {});
                const errors = data.errors ? Object.values(data.errors).flat().join(' ') : data.message;
                throw new Error(errors || 'The page could not be saved. Check the settings and try again.');
            }

            if (data.page?.slug) document.getElementById('builder-page-slug').value = data.page.slug;
            if (data.page?.title) document.getElementById('builder-page-title').value = data.page.title;
            this.tracker.markSaved(this.snapshot());
            this.setStatus('All changes saved', 'success');
            return true;
        } catch (error) {
            console.error(error);
            this.setStatus('Save failed', 'error');
            return false;
        } finally {
            buttons.forEach(button => button.removeAttribute('disabled'));
        }
    },

    async preview() {
        const previewWindow = window.open('about:blank', '_blank');
        if (previewWindow) previewWindow.opener = null;
        if (await this.save()) {
            if (previewWindow) previewWindow.location = window.builderPreviewUrl;
            else window.open(window.builderPreviewUrl, '_blank', 'noopener');
        } else {
            previewWindow?.close();
        }
    },

    async publish() {
        if (!await this.save()) return false;
        this.setStatus('Publishing…', 'saving');
        try {
            const response = await fetch(window.builderPublishUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    Accept: 'application/json'
                }
            });
            if (!response.ok) throw new Error('The page could not be published.');
            this.setStatus('Published', 'success');
            window.location.reload();
            return true;
        } catch (error) {
            console.error(error);
            this.setStatus('Publish failed', 'error');
            return false;
        }
    }
};
