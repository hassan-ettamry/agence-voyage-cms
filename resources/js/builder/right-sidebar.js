window.BuilderRightSidebar = {

    /*
    |--------------------------------------------------------------
    | Init
    |--------------------------------------------------------------
    */

    init() {

        /*
        |----------------------------------------------------------
        | Structure Updated
        |----------------------------------------------------------
        */

        BuilderEventBus.on(

            'structure.updated',

            () => {

                BuilderRightSidebar.render();

            }

        );

        /*
        |----------------------------------------------------------
        | Selection Changed
        |----------------------------------------------------------
        */

        BuilderEventBus.on(

            'selection.changed',

            (nodeId) => {

                BuilderRightSidebar.highlightNode(
                    nodeId
                );

            }

        );

    },

    /*
    |--------------------------------------------------------------
    | Render
    |--------------------------------------------------------------
    */

    render() {

        const tree =
            document.getElementById(
                'layers-tree'
            );

        const empty =
            document.getElementById(
                'layers-empty'
            );

        if (!tree) return;

        /*
        |----------------------------------------------------------
        | Empty State
        |----------------------------------------------------------
        */

        if (!BuilderStore.structure.length) {

            tree.innerHTML = '';

            empty?.classList.remove(
                'hidden'
            );

            return;
        }

        empty?.classList.add(
            'hidden'
        );

        /*
        |----------------------------------------------------------
        | Recursive Render
        |----------------------------------------------------------
        */

        tree.innerHTML =
            this.renderTree(
                BuilderStore.structure
            );

    },

    /*
    |--------------------------------------------------------------
    | Recursive Tree Renderer
    |--------------------------------------------------------------
    */

    renderTree(nodes, depth = 0) {

        let html = '';

        nodes.forEach(node => {

            const selected =
                BuilderStore.selectedNodeId === node.id;

            const safeNodeId =
                BuilderHtmlEscape.attribute(node.id);

            const safeNodeIdJs =
                BuilderHtmlEscape.jsString(node.id);

            const type =
                BuilderHtmlEscape.html(node.type);

            const label =
                BuilderHtmlEscape.html(
                    this.getLabel(node)
                );

            html += `

                <div>

                    <div

                        class="
                            layer-item
                            flex
                            items-center
                            gap-2
                            px-3
                            py-2
                            text-xs
                            cursor-pointer
                            border-b
                            border-gray-100
                            hover:bg-gray-50
                            transition-all

                            ${selected
                                ? 'bg-blue-50 text-blue-600'
                                : 'text-gray-700'
                            }
                        "

                        data-layer-node="${safeNodeId}"

                        style="
                            padding-left:
                            ${(depth * 20) + 12}px
                        "

                        onclick="
                            BuilderRightSidebar.select(
                                ${safeNodeIdJs}
                            )
                        "
                    >

                        <!-- ICON -->

                        <span class="
                            uppercase
                            text-[10px]
                            text-gray-400
                            font-bold
                            shrink-0
                        ">
                            ${type}
                        </span>

                        <!-- LABEL -->

                        <span class="truncate">

                            ${label}

                        </span>

                    </div>

            `;

            /*
            |------------------------------------------------------
            | Children
            |------------------------------------------------------
            */

            if (
                node.children &&
                node.children.length
            ) {

                html += this.renderTree(
                    node.children,
                    depth + 1
                );

            }

            html += `</div>`;

        });

        return html;

    },

    /*
    |--------------------------------------------------------------
    | Highlight Node
    |--------------------------------------------------------------
    */

    highlightNode(nodeId) {

        document
            .querySelectorAll('.layer-item')
            .forEach(item => {

                item.classList.remove(
                    'bg-blue-50',
                    'text-blue-600'
                );

            });

        const layer =
            document.querySelector(
                `[data-layer-node="${CSS.escape(nodeId)}"]`
            );

        if (!layer) return;

        layer.classList.add(
            'bg-blue-50',
            'text-blue-600'
        );

    },

    /*
    |--------------------------------------------------------------
    | Select Node
    |--------------------------------------------------------------
    */

    select(nodeId) {

        /*
        |----------------------------------------------------------
        | Find Canvas Element
        |----------------------------------------------------------
        */

        const element =
            document.querySelector(
                `[data-node-id="${CSS.escape(nodeId)}"]`
            );

        if (!element) return;

        /*
        |----------------------------------------------------------
        | Trigger Selection
        |----------------------------------------------------------
        */

        element.click();

        /*
        |----------------------------------------------------------
        | Scroll Into View
        |----------------------------------------------------------
        */

        element.scrollIntoView({

            behavior: 'smooth',

            block: 'center'

        });

    },

    /*
    |--------------------------------------------------------------
    | Get Label
    |--------------------------------------------------------------
    */

    getLabel(node) {

        switch (node.type) {

            case 'heading':
                return node.props?.text
                    || 'Heading';

            case 'text':
                return node.props?.text
                    || 'Text';

            case 'button':
                return node.props?.text
                    || 'Button';

            case 'hero':
                return node.props?.title
                    || 'Hero';

            default:

                return (
                    node.type.charAt(0)
                        .toUpperCase()
                    +
                    node.type.slice(1)
                );
        }

    },

    /*
    |--------------------------------------------------------------
    | Toggle Sidebar
    |--------------------------------------------------------------
    */

    toggle() {

        document
            .getElementById(
                'right-panel'
            )
            ?.classList.toggle(
                'hidden'
            );

    }

};
