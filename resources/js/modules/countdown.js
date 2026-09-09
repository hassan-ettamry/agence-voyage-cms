const pad = (value) => String(value).padStart(2, '0');

const updateCountdown = (element) => {
    const target = element.dataset.countdownTarget;

    if (!target) {
        return;
    }

    const targetTime = new Date(target).getTime();

    if (Number.isNaN(targetTime)) {
        return;
    }

    const remaining = targetTime - Date.now();
    const message = element.querySelector('[data-countdown-message]');

    if (remaining <= 0) {
        ['days', 'hours', 'minutes', 'seconds'].forEach((unit) => {
            const node = element.querySelector(`[data-countdown-unit="${unit}"]`);

            if (node) {
                node.textContent = '00';
            }
        });

        if (message) {
            message.textContent = element.dataset.countdownExpired || 'Offer expired';
            message.classList.remove('hidden');
        }

        return;
    }

    if (message) {
        message.classList.add('hidden');
    }

    const seconds = Math.floor(remaining / 1000);
    const values = {
        days: Math.floor(seconds / 86400),
        hours: Math.floor((seconds % 86400) / 3600),
        minutes: Math.floor((seconds % 3600) / 60),
        seconds: seconds % 60,
    };

    Object.entries(values).forEach(([unit, value]) => {
        const node = element.querySelector(`[data-countdown-unit="${unit}"]`);

        if (node) {
            node.textContent = unit === 'days' ? String(value) : pad(value);
        }
    });
};

export const refreshCountdownWidgets = () => {
    document
        .querySelectorAll('[data-countdown-widget]')
        .forEach(updateCountdown);
};

export const initCountdownWidgets = () => {
    refreshCountdownWidgets();

    if (window.__builderCountdownInterval) {
        return;
    }

    window.__builderCountdownInterval = window.setInterval(
        refreshCountdownWidgets,
        1000
    );
};

if (typeof window !== 'undefined') {
    window.BuilderCountdownWidgets = {
        refresh: refreshCountdownWidgets,
        init: initCountdownWidgets,
    };
}
