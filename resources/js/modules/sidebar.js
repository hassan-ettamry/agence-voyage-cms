export function initSidebar() {

    // =========================
    // 1. DROPDOWN MENUS (your existing logic)
    // =========================
    const toggles = document.querySelectorAll('.menu-toggle');

    toggles.forEach(btn => {
        const submenu = btn.nextElementSibling;
        const chevron = btn.querySelector('.chevron');

        // INIT STATE
        if (submenu && !submenu.classList.contains('hidden')) {
            chevron?.classList.add('rotate-180');
        }

        btn.addEventListener('click', () => {

            const isOpen = !submenu.classList.contains('hidden');

            document.querySelectorAll('.submenu').forEach(s => s.classList.add('hidden'));
            document.querySelectorAll('.chevron').forEach(c => c.classList.remove('rotate-180'));

            if (!isOpen) {
                submenu.classList.remove('hidden');
                chevron?.classList.add('rotate-180');
            }
        });
    });

    // =========================
    // 2. USER MENU
    // =========================
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const userMenu = document.querySelector('.user-menu');

    if (userMenuToggle && userMenu) {
        const viewportPadding = 12;
        const triggerGap = 8;
        const userMenuAnchor = userMenu.closest('.user-menu-anchor');

        const updateUserMenuSize = () => {
            if (userMenu.classList.contains('hidden')) {
                return;
            }

            const anchorRect = userMenuAnchor?.getBoundingClientRect();
            const availableHeight = anchorRect
                ? Math.max(0, anchorRect.top - viewportPadding - triggerGap)
                : Math.max(0, window.innerHeight - (viewportPadding * 2));

            userMenu.style.maxHeight = `${Math.max(0, Math.floor(availableHeight))}px`;
        };

        const closeUserMenu = () => {
            userMenu.classList.add('hidden');
            userMenuToggle.setAttribute('aria-expanded', 'false');
            userMenu.style.removeProperty('max-height');
        };

        const openUserMenu = () => {
            userMenu.classList.remove('hidden');
            userMenuToggle.setAttribute('aria-expanded', 'true');
            updateUserMenuSize();
        };

        userMenuToggle.addEventListener('click', (event) => {
            event.stopPropagation();

            const isOpen = !userMenu.classList.contains('hidden');

            if (isOpen) {
                closeUserMenu();
                return;
            }

            openUserMenu();
        });

        userMenu.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('click', closeUserMenu);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeUserMenu();
            }
        });

        window.addEventListener('resize', updateUserMenuSize);
        window.addEventListener('scroll', updateUserMenuSize, true);
    }

    // =========================
    // 3. MOBILE SIDEBAR TOGGLE (NEW)
    // =========================
    window.toggleSidebar = function () {

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (!sidebar) return;

        const isOpen = !sidebar.classList.contains('-translate-x-full');

        if (isOpen) {
            sidebar.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
        } else {
            sidebar.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
        }
    };

}
