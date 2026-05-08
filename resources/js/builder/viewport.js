window.BuilderViewport = {

    set(mode) {

        document
            .querySelectorAll('.viewport-btn')
            .forEach(button => button.classList.remove('active'));

        document
            .getElementById(`viewport-${mode}`)
            ?.classList.add('active');

        const wrapper = document.getElementById('canvas-wrapper');

        if (!wrapper) return;

        switch (mode) {

            case 'mobile':
                wrapper.style.maxWidth = '390px';
                break;

            case 'tablet':
                wrapper.style.maxWidth = '768px';
                break;

            default:
                wrapper.style.maxWidth = '100%';
        }
    }
};