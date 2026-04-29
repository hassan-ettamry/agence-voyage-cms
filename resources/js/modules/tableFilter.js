export function initTableFilter() {

    const buttons = document.querySelectorAll('.filter-btn');
    if (!buttons.length) return;

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

        const rows = document.querySelectorAll('.page-row');

        rows.forEach(row => {
            const type = row.dataset.type;

            if (filter === 'all' || type === filter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update record count
        const recordCount = document.getElementById('record-count');
        if (recordCount) {
            recordCount.textContent =
                `(${visibleCount} record${visibleCount !== 1 ? 's' : ''} filtered)`;
        }

        // Update pagination text
        const paginationInfo = document.getElementById('pagination-info');
        if (paginationInfo) {
            paginationInfo.innerHTML =
                `Showing <span class="font-semibold text-gray-700">1–${visibleCount}</span> 
                 of <span class="font-semibold text-gray-700">${visibleCount}</span> filtered pages`;
        }

        // Hide pagination controls when filtering
        const controls = document.querySelector('.flex.items-center.justify-between.w-full');
        if (controls) {
            const right = controls.querySelector('.flex.items-center.gap-2');
            if (right) {
                right.style.display = filter === 'all' ? '' : 'none';
            }
        }

        // Empty state
        if (visibleCount === 0) {
            const tbody = document.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-5 py-16 text-center text-gray-400">
                            No results found
                        </td>
                    </tr>
                `;
            }
        }
    }
}