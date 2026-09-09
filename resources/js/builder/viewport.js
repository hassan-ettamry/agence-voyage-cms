window.BuilderViewport = {

    set(mode) {

        document
            .querySelectorAll('.viewport-btn')
            .forEach(button => {

                const active =
                    button.dataset.viewport === mode;

                button.classList.toggle('active', active);
                button.classList.toggle('bg-blue-50', active);
                button.classList.toggle('text-blue-600', active);
                button.classList.toggle('border-b-blue-600', active);

                button.classList.toggle('bg-white', !active);
                button.classList.toggle('text-gray-500', !active);
                button.classList.toggle('border-b-transparent', !active);

            });

        if (window.BuilderStore) {
            BuilderStore.viewport = mode;
        }

        const wrapper =
            document.getElementById('canvas-wrapper');

        if (!wrapper) return;

        switch (mode) {

            case 'mobile':
                wrapper.style.maxWidth = '390px';
                break;

            case 'tab':
            case 'tablet':
                wrapper.style.maxWidth = '768px';
                break;

            default:
                wrapper.style.maxWidth = '100%';

        }

        if (BuilderStore.selectedNodeId && window.BuilderSettingsUpdater) {
            BuilderSettingsUpdater.refreshSettingsPanel(BuilderStore.selectedNodeId);
        }

    }

};
