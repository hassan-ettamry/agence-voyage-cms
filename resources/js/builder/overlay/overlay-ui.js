window.BuilderOverlayUI = {

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
                        '↑',
                        `BuilderOverlayActions.moveUp('${nodeId}')`
                    )}

                    ${this.button(
                        '↓',
                        `BuilderOverlayActions.moveDown('${nodeId}')`
                    )}

                    ${this.divider()}

                    ${this.button(
                        '⧉',
                        `BuilderOverlayActions.duplicate('${nodeId}')`
                    )}

                    ${this.button(
                        '✎',
                        `BuilderOverlayActions.edit('${nodeId}')`
                    )}

                    ${this.divider()}

                    ${this.button(
                        '✕',
                        `BuilderSelection.delete()`
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

    button(icon, onclick) {

        return `

            <button

                onclick="${onclick}"

                class="
                    w-7
                    h-7
                    flex
                    items-center
                    justify-center
                    text-white
                    text-[12px]
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