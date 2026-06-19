window.BuilderOverlayTheme = {

    colors: {
        active: 'var(--builder-overlay-active, #e92f93)',
        parent: 'var(--builder-overlay-parent, #111827)',
        ancestor: 'var(--builder-overlay-ancestor, #64748b)',
        child: 'var(--builder-overlay-child, #22c55e)',
        drag: 'var(--builder-overlay-drag, #2563eb)',
        dragSurface: 'var(--builder-overlay-drag-surface, #eff6ff)'
    },

    outlineRules: {
        selected: {
            offset: 0,
            width: 2
        },
        parent: {
            offset: 2,
            width: 2
        },
        ancestor: {
            offsetStep: 2,
            maxOffset: 8,
            width: 1
        },
        child: {
            offset: -2,
            width: 1
        }
    },

    outline(mode = 'active', options = {}) {

        const color =
            this.colors[mode]
            ||
            this.colors.active;

        const width =
            options.width
            ??
            2;

        return `${width}px dashed ${color}`;

    },

    outlineOffset(value = -2) {

        return `${value}px`;

    },

    pathOutlineOptions(distanceFromSelected = 0) {

        const distance =
            Math.max(
                0,
                Number(distanceFromSelected) || 0
            );

        if (distance === 0) {
            return {
                ...this.outlineRules.selected
            };
        }

        if (distance === 1) {
            return {
                ...this.outlineRules.parent
            };
        }

        return {
            offset: Math.min(
                distance * this.outlineRules.ancestor.offsetStep,
                this.outlineRules.ancestor.maxOffset
            ),
            width: this.outlineRules.ancestor.width
        };

    },

    childOutlineOptions() {

        return {
            ...this.outlineRules.child
        };

    },

    toolbarBackground(mode = 'selected') {

        return mode === 'parent-hover'
            ? this.colors.parent
            : this.colors.active;

    },

    applyOutline(element, mode = 'active', options = {}) {

        if (!element) return;

        element.style.outline =
            this.outline(mode, options);

        element.style.outlineOffset =
            this.outlineOffset(
                options.offset
                ??
                -2
            );

        if (options.cursor) {
            element.style.cursor = options.cursor;
        }

    },

    clearOutline(element) {

        if (!element) return;

        element.style.outline = '';
        element.style.outlineOffset = '';
        element.style.cursor = '';

    },

    applyDragPreview(element, position) {

        if (!element) return;

        if (position === 'before') {
            element.style.borderTop =
                `4px solid ${this.colors.drag}`;
        }

        if (position === 'after') {
            element.style.borderBottom =
                `4px solid ${this.colors.drag}`;
        }

        if (position === 'inside') {
            element.style.outline =
                `2px solid ${this.colors.drag}`;

            element.style.backgroundColor =
                this.colors.dragSurface;
        }

    },

    clearDragPreview(element) {

        if (!element) return;

        element.style.borderTop = '';
        element.style.borderBottom = '';
        element.style.backgroundColor = '';

        this.clearOutline(element);

    }

};
