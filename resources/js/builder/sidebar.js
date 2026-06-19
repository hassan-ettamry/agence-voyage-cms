window.BuilderSidebar = {

    componentLabels: {
        richtext: 'RichText',
        text: 'Text',
        heading: 'Heading',
        button: 'Button',
        image: 'Image',
        icon: 'Icon',
        'icon-text': 'Icon With Text',
        link: 'Link',
        video: 'Video',
        iframe: 'iFrame',
        gallery: 'Gallery',
        map: 'Map',
        'contact-form': 'Contact Form',
        faq: 'FAQ',
        countdown: 'Countdown',
        section: 'Section',
        container: 'Container',
        hero: 'Hero'
    },

    componentLabel(type) {

        if (!type) {
            return '';
        }

        return this.componentLabels[type]
            || type
                .split('-')
                .map(part => part.charAt(0).toUpperCase() + part.slice(1))
                .join(' ');

    },

    setTitle(title = null) {

        const panelTitle =
            document.getElementById(
                'builder-panel-title'
            );

        if (!panelTitle) {
            return;
        }

        panelTitle.textContent =
            title
            || panelTitle.dataset.pageTitle
            || '';

    },

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
                    'border-b-slate-900',
                    'bg-[#eff6ff]',
                    'font-medium'
                );

                button.classList.add(
                    'border-b-transparent',
                    'bg-white',
                    'text-slate-800'
                );
            });

        contents
            .forEach(content => {
                content.classList.add('hidden');
            });

        activeButton.classList.remove(
            'border-b-transparent',
            'bg-white',
            'text-slate-800'
        );

        activeButton.classList.add(
            'border-b-slate-900',
            'bg-[#eff6ff]',
            'text-slate-800',
            'font-medium'
        );

        activeContent.classList.remove('hidden');
    },

    toggle() {

        document
            .getElementById('left-panel')
            ?.classList.toggle('hidden');
    }
};
