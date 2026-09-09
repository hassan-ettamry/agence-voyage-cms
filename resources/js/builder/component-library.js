import { componentMatches } from './component-library-filter';

window.BuilderComponentLibrary = {
    init() {
        this.search = document.querySelector('[data-component-search]');
        this.category = document.querySelector('[data-component-category-filter]');
        this.cards = [...document.querySelectorAll('[data-action="add-component"]')];
        this.empty = document.querySelector('[data-component-empty]');
        this.count = document.querySelector('[data-component-result-count]');

        if (!this.search || !this.category) return;

        this.search.addEventListener('input', () => this.filter());
        this.category.addEventListener('change', () => this.filter());
        document.addEventListener('keydown', (event) => {
            const editable = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)
                || document.activeElement?.isContentEditable;
            if (event.key === '/' && !editable) {
                event.preventDefault();
                this.search.focus();
            }
        });
        this.filter();
    },

    filter() {
        let visible = 0;
        this.cards.forEach((card) => {
            const match = componentMatches({
                label: card.dataset.componentLabel,
                type: card.dataset.component,
                category: card.dataset.componentCategory,
            }, this.search.value, this.category.value);
            card.classList.toggle('hidden', !match);
            if (match) visible += 1;
        });
        this.empty?.classList.toggle('hidden', visible !== 0);
        if (this.count) this.count.textContent = `${visible} component${visible === 1 ? '' : 's'}`;
    },
};
