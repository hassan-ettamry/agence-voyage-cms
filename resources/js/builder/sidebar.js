window.BuilderSidebar = {

    switchTab(tab) {

        const buttons =
            document.querySelectorAll(
                '.left-tab'
            );

        const contents =
            document.querySelectorAll(
                '.left-tab-content'
            );

        const activeButton =
            document.querySelector(
                `[data-tab="${tab}"]`
            );

        const activeContent =
            document.getElementById(
                `tab-${tab}`
            );

        if (
            !buttons.length ||
            !contents.length ||
            !activeButton ||
            !activeContent
        ) {
            return;
        }

        buttons
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

        contents
            .forEach(content => {
                content.classList.add('hidden');
            });

        activeButton.classList.remove(
            'border-transparent',
            'text-gray-500'
        );

        activeButton.classList.add(
            'border-blue-600',
            'text-blue-600',
            'font-semibold'
        );

        activeContent.classList.remove('hidden');
    },

    toggle() {

        document
            .getElementById('left-panel')
            ?.classList.toggle('hidden');
    }
};
