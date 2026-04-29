export function initSidebar() {
    const toggles = document.querySelectorAll('.menu-toggle');

    toggles.forEach(btn => {
        btn.addEventListener('click', () => {

            document.querySelectorAll('.submenu').forEach(s => s.classList.add('hidden'));

            const submenu = btn.nextElementSibling;
            if (submenu) submenu.classList.remove('hidden');
        });
    });
}