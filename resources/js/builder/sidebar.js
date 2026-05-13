window.BuilderSidebar = {

    switchTab(tab) {

        document
            .querySelectorAll('.left-tab')
            .forEach(button => {

                button.classList.remove(
                    'border-blue-600',
                    'text-blue-600',
                    'font-semibold'
                );

                button.classList.add(
                    'border-transparent',
                    'text-gray-500'
                );
            });

        document
            .querySelectorAll('.left-tab-content')
            .forEach(content => {
                content.classList.add('hidden');
            });

        const activeButton = document.querySelector(`[data-tab="${tab}"]`);

        activeButton.classList.remove(
            'border-transparent',
            'text-gray-500'
        );

        activeButton.classList.add(
            'border-blue-600',
            'text-blue-600',
            'font-semibold'
        );

        document
            .getElementById(`tab-${tab}`)
            ?.classList.remove('hidden');
    },

    toggle() {

        document
            .getElementById('left-panel')
            ?.classList.toggle('hidden');
    }
};