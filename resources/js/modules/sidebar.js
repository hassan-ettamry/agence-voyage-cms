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
    // 2. MOBILE SIDEBAR TOGGLE (NEW)
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