export function initTableFilter() {

    const buttons = document.querySelectorAll('.filter-btn');
    if (!buttons.length) return;

    const rows = document.querySelectorAll('.page-row');
    const emptyRow = document.getElementById('empty-state-row');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;
            applyFilter(btn, filter);
        });
    });

    function applyFilter(btn, filter) {

        // Active state
        buttons.forEach(b => {
            b.classList.remove('bg-white', 'shadow-sm', 'text-slate-800');
            b.classList.add('text-slate-500');
        });

        btn.classList.add('bg-white', 'shadow-sm', 'text-slate-800');
        btn.classList.remove('text-slate-500');

        let visibleCount = 0;

        rows.forEach(row => {
            const type = row.dataset.type;

            if (filter === 'all' || type === filter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Record count
        const recordCount = document.getElementById('record-count');
        if (recordCount) {
            recordCount.textContent =
                `(${visibleCount} record${visibleCount !== 1 ? 's' : ''} filtered)`;
        }

        // Pagination hide
        const controls = document.querySelector('.flex.items-center.justify-between.w-full');
        if (controls) {
            const right = controls.querySelector('.flex.items-center.gap-2');
            if (right) {
                right.style.display = filter === 'all' ? '' : 'none';
            }
        }

        // Empty state (NO DOM DELETE)
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }
}