window.BuilderOverlayUI = {
    icons: {
        move: '<path d="M12 3v18M3 12h18M9 6l3-3 3 3M9 18l3 3 3-3M6 9l-3 3 3 3M18 9l3 3-3 3"/>',
        parent: '<path d="M18 18H9a3 3 0 0 1-3-3V4m-4 4 4-4 4 4"/>',
        up: '<path d="m6 15 6-6 6 6"/>',
        down: '<path d="m6 9 6 6 6-6"/>',
        duplicate: '<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V4H4v12h4"/>',
        edit: '<path d="m15 5 4 4M4 20l5-1L20 8a2.8 2.8 0 0 0-4-4L5 15z"/>',
        delete: '<path d="M4 7h16M9 7V4h6v3M10 11v6M14 11v6M6 7l1 13h10l1-13"/>'
    },

    render(nodeId, label, position = {}, mode = 'selected', capabilities = {}) {
        const escape = value => BuilderHtmlEscape.attribute(value);
        return `
            <div data-builder-overlay-toolbar="true" data-builder-overlay-mode="selected"
                class="builder-component-toolbar" role="toolbar"
                aria-label="${escape(`${label} actions`)}" style="visibility:hidden">
                <span class="builder-component-toolbar__label" title="${escape(label)}">
                    ${BuilderHtmlEscape.html(label)}
                </span>
                <div class="builder-component-toolbar__actions">
                    ${capabilities.parent ? this.button('parent', 'overlay-select-parent', nodeId, 'Select parent') : ''}
                    ${this.button('move', null, nodeId, 'Drag to move component', !capabilities.canDrag, { drag: true })}
                    ${this.button('up', 'overlay-move-up', nodeId, 'Move component up', !capabilities.canMoveUp)}
                    ${this.button('down', 'overlay-move-down', nodeId, 'Move component down', !capabilities.canMoveDown)}
                    ${this.button('edit', 'overlay-edit', nodeId, 'Edit component')}
                    ${this.button('duplicate', 'overlay-duplicate', nodeId, 'Duplicate component', !capabilities.canDuplicate)}
                    ${this.canSpan(capabilities) ? this.gridSpanSelect(nodeId, capabilities.node.props?.gridSpan ?? 12) : ''}
                    ${this.button('delete', 'overlay-delete', nodeId, 'Delete component', !capabilities.canDelete, { destructive: true })}
                </div>
            </div>`;
    },

    canSpan({ node, parent } = {}) {
        return node?.type === 'container' && parent?.type === 'container'
            && (parent.props?.display || 'block') === 'grid';
    },

    gridSpanSelect(nodeId, value = 12) {
        const selectedValue = String(value || 12);
        return `<select data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
            data-setting-field="gridSpan" title="Grid span" aria-label="Grid span"
            class="builder-component-toolbar__span">
            ${Array.from({ length: 12 }, (_, index) => {
                const option = String(index + 1);
                return `<option value="${option}" ${selectedValue === option ? 'selected' : ''}>${option}</option>`;
            }).join('')}
        </select>`;
    },

    button(icon, action, nodeId, label, disabled = false, options = {}) {
        const escape = value => BuilderHtmlEscape.attribute(value);
        return `<button type="button" data-target-node-id="${escape(nodeId)}"
            ${action ? `data-action="${escape(action)}"` : ''}
            ${options.drag ? `data-builder-drag-handle data-drag-action="reorder" draggable="${!disabled}"` : ''}
            ${disabled ? 'disabled aria-disabled="true"' : ''}
            title="${escape(label)}" aria-label="${escape(label)}"
            class="builder-component-toolbar__button${options.destructive ? ' builder-component-toolbar__button--delete' : ''}">
            <svg width="16" height="16" aria-hidden="true" fill="none" stroke="currentColor"
                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                ${this.icons[icon]}
            </svg>
        </button>`;
    }
};
