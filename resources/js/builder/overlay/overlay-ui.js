window.BuilderOverlayUI = {

    icons: {

        move: `
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v18M3 12h18M7 7l-4 5 4 5M17 7l4 5-4 5"/>
            </svg>
        `,

        up: `
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 15l7-7 7 7"/>
            </svg>
        `,

        down: `
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7"/>
            </svg>
        `,

        duplicate: `
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="9" y="9" width="13" height="13" rx="2" stroke-width="2"/>
                <rect x="2" y="2" width="13" height="13" rx="2" stroke-width="2"/>
            </svg>
        `,

        edit: `
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.232 5.232l3.536 3.536M9 13l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 16.536a4 4 0 01-1.414.95L7 19l1.514-4.122A4 4 0 019 13z"/>
            </svg>
        `,

        delete: `
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 7h12M9 7V4h6v3M10 11v6M14 11v6M5 7l1 13h12l1-13"/>
            </svg>
        `

    },

    /*
    |--------------------------------------------------------------------------
    | Render Toolbar
    |--------------------------------------------------------------------------
    */

    render(nodeId, label, position, mode = 'selected') {

        const isParent =
            mode === 'parent-hover';

        const isCentered =
            position.align === 'center';

        const background =
            BuilderOverlayTheme.toolbarBackground(
                mode
            );

        const transform =
            isCentered
                ? 'transform:translateX(-50%);'
                : '';

        return `

            <div

                data-builder-overlay-toolbar="true"
                data-builder-overlay-mode="${BuilderHtmlEscape.attribute(mode)}"
                class="
                    absolute
                    flex
                    items-center
                    rounded-[2px]
                    shadow-[0_8px_18px_rgba(15,23,42,0.16)]
                    pointer-events-auto
                    z-[99999]
                    overflow-hidden
                    font-sans
                "

                style="
                    top:${position.top}px;
                    left:${position.left}px;
                    height:24px;
                    background:${background};
                    ${transform}
                "
            >
                ${isParent
                    ? this.parentControls(nodeId)
                    : this.activeControls(nodeId)}

            </div>

        `;

    },

    activeControls(nodeId) {

        const node =
            window.Builder?.findNodeById
                ? Builder.findNodeById(nodeId)
                : null;

        const parent =
            window.BuilderNodeTraversal?.findParent
                ? BuilderNodeTraversal.findParent(nodeId)
                : null;

        const parentIsGrid =
            parent?.type === 'container'
            && (parent.props?.display || 'block') === 'grid';

        const canSpan =
            node?.type === 'container'
            && parentIsGrid;

        return `

            <div class="flex items-center h-full">

                ${this.button(this.icons.move, null, nodeId)}

                ${canSpan ? this.gridSpanSelect(nodeId, node?.props?.gridSpan ?? 12) : ''}

                ${this.button(this.icons.down, 'overlay-move-down', nodeId)}
                ${this.button(this.icons.edit, 'overlay-edit', nodeId)}
                ${this.button(this.icons.delete, 'overlay-delete', nodeId)}
                ${this.button(this.icons.duplicate, 'overlay-duplicate', nodeId)}

            </div>

        `;

    },

    gridSpanSelect(nodeId, value = 12) {

        const safeNodeId =
            BuilderHtmlEscape.attribute(nodeId);

        const selectedValue =
            String(value || '12');

        const options = [
            ...Array.from({ length: 12 }, (_, index) => String(index + 1))
        ];

        return `

            <select
                data-target-node-id="${safeNodeId}"
                data-setting-field="gridSpan"
                title="Grid span"
                class="
                    h-6
                    w-12
                    border-0
                    border-l
                    border-white/20
                    bg-transparent
                    px-1
                    text-[11px]
                    font-bold
                    leading-none
                    text-white
                    outline-none
                    cursor-pointer
                    hover:bg-white/20
                    [&>option]:bg-slate-800
                    [&>option]:text-white
                "
            >
                ${options.map(option => `
                    <option
                        value="${BuilderHtmlEscape.attribute(option)}"
                        ${selectedValue === option ? 'selected' : ''}
                    >
                        ${BuilderHtmlEscape.html(option)}
                    </option>
                `).join('')}
            </select>

        `;

    },

    parentControls(nodeId) {

        return `

            <div class="flex items-center h-full">

                ${this.button(this.icons.move, null, nodeId)}
                ${this.button(this.icons.edit, 'overlay-edit', nodeId)}
                ${this.button(this.icons.delete, 'overlay-delete', nodeId)}
                ${this.button(this.icons.up, 'overlay-move-up', nodeId)}
                ${this.button(this.icons.down, 'overlay-move-down', nodeId)}
                ${this.button(this.icons.duplicate, 'overlay-duplicate', nodeId)}

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Button
    |--------------------------------------------------------------------------
    */

    button(icon, action, nodeId) {

        const labels = {
            'overlay-edit': 'Edit component',
            'overlay-delete': 'Delete component',
            'overlay-move-up': 'Move component up',
            'overlay-move-down': 'Move component down',
            'overlay-duplicate': 'Duplicate component'
        };

        const label = labels[action] || 'Move component';

        const safeAction =
            BuilderHtmlEscape.attribute(action);

        const safeNodeId =
            BuilderHtmlEscape.attribute(nodeId);

        return `

            <button

                ${action ? `data-action="${safeAction}"` : ''}

                data-target-node-id="${safeNodeId}"

                title="${BuilderHtmlEscape.attribute(label)}"
                aria-label="${BuilderHtmlEscape.attribute(label)}"

                class="
                    w-6
                    h-6
                    min-w-6
                    flex
                    items-center
                    justify-center
                    text-white
                    hover:bg-white/20
                    transition
                    border-0
                    p-0
                "
            >

                ${icon}

            </button>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Divider
    |--------------------------------------------------------------------------
    */

    divider() {

        return `

            <div
                class="
                    w-px
                    h-4
                    bg-white/20
                "
            ></div>

        `;

    }

};
