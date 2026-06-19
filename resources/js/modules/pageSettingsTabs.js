const activeClasses = [
    'border-indigo-200',
    'bg-indigo-50',
    'text-indigo-700',
];

const inactiveClasses = [
    'border-transparent',
    'text-gray-500',
    'hover:bg-gray-50',
    'hover:text-gray-700',
];

export function initPageSettingsTabs() {
    const root = document.querySelector('[data-page-settings]');

    if (!root) {
        return;
    }

    const tabs = Array.from(root.querySelectorAll('[data-page-settings-tab]'));
    const panels = Array.from(root.querySelectorAll('[data-page-settings-panel]'));

    if (!tabs.length || !panels.length) {
        return;
    }

    const activate = (name) => {
        tabs.forEach((tab) => {
            const isActive = tab.dataset.pageSettingsTab === name;

            tab.setAttribute('aria-selected', String(isActive));
            tab.classList.remove(...(isActive ? inactiveClasses : activeClasses));
            tab.classList.add(...(isActive ? activeClasses : inactiveClasses));
        });

        panels.forEach((panel) => {
            panel.classList.toggle(
                'hidden',
                panel.dataset.pageSettingsPanel !== name
            );
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            activate(tab.dataset.pageSettingsTab);
        });
    });

    activate(
        tabs.find((tab) => tab.getAttribute('aria-selected') === 'true')
            ?.dataset.pageSettingsTab ?? tabs[0].dataset.pageSettingsTab
    );
}
