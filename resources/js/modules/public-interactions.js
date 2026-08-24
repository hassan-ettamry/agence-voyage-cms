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

    root.querySelectorAll('[data-site-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-site-carousel-track]');
        const previous = carousel.querySelector('[data-site-carousel-prev]');
        const next = carousel.querySelector('[data-site-carousel-next]');

        if (!track || !previous || !next || carousel.dataset.carouselReady === 'true') return;

        carousel.dataset.carouselReady = 'true';
        const scroll = (direction) => {
            const card = track.querySelector('a, article');
            const distance = card ? card.getBoundingClientRect().width + 20 : track.clientWidth * 0.8;
            track.scrollBy({ left: direction * distance, behavior: 'smooth' });
        };

        previous.addEventListener('click', () => scroll(-1));
        next.addEventListener('click', () => scroll(1));
        track.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;

            event.preventDefault();
            scroll(event.key === 'ArrowRight' ? 1 : -1);
        });
    });
}
