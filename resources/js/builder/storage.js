window.BuilderStorage = {
    setStatus(message, tone = 'neutral') {
        const status = document.getElementById('builder-save-status');
        if (!status) return;
        status.textContent = message;
        status.classList.remove('text-slate-500', 'text-blue-600', 'text-emerald-700', 'text-red-600');
        status.classList.add({saving: 'text-blue-600', success: 'text-emerald-700', error: 'text-red-600'}[tone] || 'text-slate-500');
    },

    async save() {
        const button = document.querySelector('[data-action="save-page"]');
        try {
            this.setStatus('Saving…', 'saving');
            button?.setAttribute('disabled', 'disabled');
            BuilderStructureRules.normalizeStore();
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('structure', JSON.stringify(BuilderStore.getStructure()));
            const response = await fetch(`/pages/${window.pageId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: formData,
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const errors = data.errors ? Object.values(data.errors).flat().join(' ') : data.message;
                throw new Error(errors || 'The page could not be saved. Check the component settings and try again.');
            }
            this.setStatus(data.message || 'Page saved', 'success');
            setTimeout(() => this.setStatus('All changes saved'), 2500);
            return true;
        } catch (error) {
            console.error(error);
            this.setStatus(error?.message || 'Save failed', 'error');
            return false;
        } finally {
            button?.removeAttribute('disabled');
        }
    },
};
