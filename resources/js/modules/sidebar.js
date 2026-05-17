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
        const closeUserMenu = () => {
            userMenu.classList.add('hidden');
            userMenuToggle.setAttribute('aria-expanded', 'false');
        };

        userMenuToggle.addEventListener('click', (event) => {
            event.stopPropagation();

            const isOpen = !userMenu.classList.contains('hidden');

            userMenu.classList.toggle('hidden', isOpen);
            userMenuToggle.setAttribute('aria-expanded', String(!isOpen));
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
