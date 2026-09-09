export function initPublicNavigation() {
    document.querySelectorAll('[data-site-navigation]').forEach((navigation) => {
        const toggle = navigation.querySelector('[data-site-nav-toggle]');
        const mobileMenu = navigation.querySelector('[data-site-mobile-nav]');
        const openIcon = navigation.querySelector('[data-site-nav-open-icon]');
        const closeIcon = navigation.querySelector('[data-site-nav-close-icon]');

        if (!toggle || !mobileMenu) return;

        const setOpen = (open) => {
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
            mobileMenu.classList.toggle('hidden', !open);
            openIcon?.classList.toggle('hidden', open);
            closeIcon?.classList.toggle('hidden', !open);
        };

        toggle.addEventListener('click', () => {
            setOpen(toggle.getAttribute('aria-expanded') !== 'true');
        });

        mobileMenu.addEventListener('click', (event) => {
            if (event.target.closest('a')) setOpen(false);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setOpen(false);
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) setOpen(false);
        });
    });
}
