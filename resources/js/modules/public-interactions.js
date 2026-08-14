export function initPublicInteractions(root = document) {
    root.querySelectorAll('[data-site-tabs]').forEach((tabs) => {
        const buttons = [...tabs.querySelectorAll('[data-site-tab]')];
        const panels = [...tabs.querySelectorAll('[data-site-tab-panel]')];
        buttons.forEach((button) => button.addEventListener('click', () => {
            const selected = button.dataset.siteTab;
            buttons.forEach((candidate) => {
                const active = candidate.dataset.siteTab === selected;
                candidate.setAttribute('aria-selected', active ? 'true' : 'false');
                candidate.classList.toggle('border-[var(--site-primary)]', active);
                candidate.classList.toggle('text-[var(--site-primary)]', active);
                candidate.classList.toggle('border-transparent', !active);
                candidate.classList.toggle('text-slate-500', !active);
            });
            panels.forEach((panel) => { panel.hidden = panel.dataset.siteTabPanel !== selected; });
        }));
    });
}
