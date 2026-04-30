export function initSidebar() {
    const toggles = document.querySelectorAll('.menu-toggle');

    toggles.forEach(btn => {
        const submenu = btn.nextElementSibling;
        const chevron = btn.querySelector('.chevron');

        // ✅ INIT STATE (important for Laravel active menu)
        if (submenu && !submenu.classList.contains('hidden')) {
            chevron?.classList.add('rotate-180');
        }

        btn.addEventListener('click', () => {

            const isOpen = !submenu.classList.contains('hidden');

            // ✅ CLOSE ALL
            document.querySelectorAll('.submenu').forEach(s => s.classList.add('hidden'));
            document.querySelectorAll('.chevron').forEach(c => c.classList.remove('rotate-180'));

            // ✅ OPEN CURRENT ONLY IF IT WAS CLOSED
            if (!isOpen) {
                submenu.classList.remove('hidden');
                chevron?.classList.add('rotate-180');
            }
        });
    });
}