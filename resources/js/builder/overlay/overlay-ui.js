window.BuilderOverlayUI = {

    icons: {

        up: `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 15l7-7 7 7"/>
            </svg>
        `,

        down: `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7"/>
            </svg>
        `,

        duplicate: `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="9" y="9" width="13" height="13" rx="2" stroke-width="2"/>
                <rect x="2" y="2" width="13" height="13" rx="2" stroke-width="2"/>
            </svg>
        `,

        edit: `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.232 5.232l3.536 3.536M9 13l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 16.536a4 4 0 01-1.414.95L7 19l1.514-4.122A4 4 0 019 13z"/>
            </svg>
        `,

        delete: `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    render(nodeId, label, position) {

        return `

            <div

                class="
                    absolute
                    flex
                    items-center
                    bg-pink-500
                    rounded-sm
                    shadow-lg
                    pointer-events-auto
                    z-[99999]
                    overflow-hidden
                "

                style="
                    top:${position.top}px;
                    left:${position.left}px;
                    height:28px;
                "
            >

                <!-- LABEL -->

                <div
                    class="
                        px-2
                        text-[10px]
                        uppercase
                        font-bold
                        text-white
                        tracking-wide
                        flex
                        items-center
                        h-full
                    "
                >

                    ${label}

                </div>

                <!-- BUTTONS -->

                <div class="flex items-center h-full">

                    ${this.button(
                        this.icons.up,
                        'overlay-move-up',
                        nodeId
                    )}

                    ${this.button(
                        this.icons.down,
                        'overlay-move-down',
                        nodeId
                    )}

                    ${this.divider()}

                    ${this.button(
                        this.icons.duplicate,
                        'overlay-duplicate',
                        nodeId
                    )}

                    ${this.button(
                        this.icons.edit,
                        'overlay-edit',
                        nodeId
                    )}

                    ${this.divider()}

                    ${this.button(
                        this.icons.delete,
                        'overlay-delete',
                        nodeId
                    )}

                </div>

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Button
    |--------------------------------------------------------------------------
    */

    button(icon, action, nodeId) {

        const safeAction =
            BuilderHtmlEscape.attribute(action);

        const safeNodeId =
            BuilderHtmlEscape.attribute(nodeId);

        return `

            <button

                data-action="${safeAction}"

                data-target-node-id="${safeNodeId}"

                class="
                    w-7
                    h-7
                    flex
                    items-center
                    justify-center
                    text-white
                    hover:bg-white/20
                    transition
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
