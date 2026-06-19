window.BuilderRightSidebar = {

    collapsedNodes: new Set(),
    knownNodeIds: new Set(),

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

            BuilderEvents.STRUCTURE_UPDATED,

            () => {

                BuilderRightSidebar.render();

            }

        );

        /*
        |----------------------------------------------------------
        | Canvas Rendered
        |----------------------------------------------------------
        */

        BuilderEventBus.on(

            BuilderEvents.CANVAS_RENDERED,

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

            BuilderEvents.SELECTION_CHANGED,

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

        this.applyDefaultCollapsedSections(
            BuilderStore.structure
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

        return nodes
            .map(node => this.renderNode(node, depth))
            .join('');

    },

    /*
    |--------------------------------------------------------------
    | Default Collapsed Sections
    |--------------------------------------------------------------
    */

    applyDefaultCollapsedSections(nodes) {

        const selectedPath =
            BuilderStore.selectedNodeId
                ? this.findPath(
                    BuilderStore.selectedNodeId,
                    BuilderStore.structure
                )
                : null;

        const selectedPathIds =
            new Set(
                (selectedPath || []).map(node => node.id)
            );

        const visit =
            (items = []) => {

                items.forEach(node => {

                    const hasChildren =
                        Array.isArray(node.children)
                        &&
                        node.children.length > 0;

                    const isNew =
                        !this.knownNodeIds.has(node.id);

                    this.knownNodeIds.add(node.id);

                    if (
                        isNew
                        &&
                        node.type === 'section'
                        &&
                        hasChildren
                        &&
                        !selectedPathIds.has(node.id)
                    ) {

                        this.collapsedNodes.add(node.id);

                    }

                    visit(node.children || []);

                });

            };

        visit(nodes);

    },

    /*
    |--------------------------------------------------------------
    | Render Node
    |--------------------------------------------------------------
    */

    renderNode(node, depth = 0) {

        const hasChildren =
            Array.isArray(node.children)
            &&
            node.children.length > 0;

        const collapsed =
            this.collapsedNodes.has(node.id);

        const selected =
            BuilderStore.selectedNodeId === node.id;

        const safeNodeId =
            BuilderHtmlEscape.attribute(node.id);

        const label =
            BuilderHtmlEscape.html(
                this.getLabel(node)
            );

        const title =
            BuilderHtmlEscape.attribute(
                this.getLabel(node)
            );

        return `

            <div class="layer-node">

                <div
                    class="${this.layerItemClasses(selected)}"
                    data-action="select-layer"
                    data-layer-node="${safeNodeId}"
                    title="${title}"
                >

                    ${this.renderToggle(hasChildren, collapsed, selected, safeNodeId)}

                    <span class="${this.layerIconClasses(selected)}" aria-hidden="true">
                        ${this.getIcon(node)}
                    </span>

                    <span class="min-w-0 flex-1 truncate">
                        ${label}
                    </span>

                </div>

                ${hasChildren && !collapsed
                    ? `
                        <div class="
                            layer-children
                            ml-4
                            border-l
                            border-slate-200/80
                            pl-1
                        ">
                            ${this.renderTree(node.children, depth + 1)}
                        </div>
                    `
                    : ''
                }

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------
    | Row Classes
    |--------------------------------------------------------------
    */

    layerItemClasses(selected) {

        return `
            layer-item
            group
            flex
            h-7
            items-center
            gap-1.5
            border-b
            border-slate-100
            px-2
            text-[13px]
            leading-none
            cursor-pointer
            transition-colors
            ${selected
                ? 'bg-indigo-500 text-white shadow-sm'
                : 'text-slate-700 hover:bg-slate-50'
            }
        `;

    },

    layerIconClasses(selected) {

        return `
            inline-flex
            h-4
            w-4
            shrink-0
            items-center
            justify-center
            ${selected ? 'text-white' : 'text-slate-500'}
        `;

    },

    /*
    |--------------------------------------------------------------
    | Toggle Marker
    |--------------------------------------------------------------
    */

    renderToggle(hasChildren, collapsed, selected, safeNodeId) {

        if (!hasChildren) {

            return `
                <span class="h-4 w-4 shrink-0"></span>
            `;

        }

        return `
            <button
                type="button"
                class="
                    inline-flex
                    h-4
                    w-4
                    shrink-0
                    items-center
                    justify-center
                    rounded-[2px]
                    border
                    text-[10px]
                    font-semibold
                    leading-none
                    ${selected
                        ? 'border-white/40 bg-white/15 text-white'
                        : 'border-slate-300 bg-white text-slate-500 group-hover:border-slate-400'
                    }
                "
                data-action="toggle-layer-node"
                data-layer-key="${safeNodeId}"
                aria-label="${collapsed ? 'Expand layer' : 'Collapse layer'}"
            >
                ${collapsed ? '+' : '-'}
            </button>
        `;

    },

    /*
    |--------------------------------------------------------------
    | Highlight Node
    |--------------------------------------------------------------
    */

    highlightNode(nodeId) {

        if (nodeId) {

            this.expandPath(nodeId);

        }

        const panel =
            document.getElementById('layers-panel');

        const scrollTop =
            panel?.scrollTop ?? 0;

        window.requestAnimationFrame(() => {

            this.render();

            if (panel) {

                panel.scrollTop = scrollTop;

            }

        });

    },

    /*
    |--------------------------------------------------------------
    | Toggle Node
    |--------------------------------------------------------------
    */

    toggleNode(nodeId) {

        if (!nodeId) return;

        if (this.collapsedNodes.has(nodeId)) {

            this.collapsedNodes.delete(nodeId);

        } else {

            this.collapsedNodes.add(nodeId);

        }

        this.render();

    },

    /*
    |--------------------------------------------------------------
    | Expand Selected Path
    |--------------------------------------------------------------
    */

    expandPath(nodeId) {

        const path =
            this.findPath(
                nodeId,
                BuilderStore.structure
            );

        if (!path) return;

        path
            .slice(0, -1)
            .forEach(node => {

                this.collapsedNodes.delete(node.id);

            });

    },

    findPath(nodeId, nodes, path = []) {

        for (const node of nodes) {

            const nextPath =
                [...path, node];

            if (node.id === nodeId) {

                return nextPath;

            }

            const childPath =
                this.findPath(
                    nodeId,
                    node.children || [],
                    nextPath
                );

            if (childPath) {

                return childPath;

            }

        }

        return null;

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

        if (
            BuilderLogger.shouldLog('selection')
            ||
            BuilderLogger.shouldLog('interaction')
        ) {

            BuilderLogger.log(
                'SELECTION REQUEST SOURCE',
                {
                    source: 'layers',
                    nodeId
                }
            );

        }

        BuilderSelectionManager.select(
            nodeId,
            element,
            {
                source: 'layers',
                scroll: true
            }
        );

    },

    /*
    |--------------------------------------------------------------
    | Get Label
    |--------------------------------------------------------------
    */

    getLabel(node) {

        switch (node.type) {

            case 'section':
                return 'Section';

            case 'heading':
                return 'Heading';

            case 'text':
                return 'Text';

            case 'richtext':
                return 'RichText';

            case 'button':
                return 'Button';

            case 'icon':
                return 'Icon';

            case 'icon-text':
                return 'Icon With Text';

            case 'link':
                return 'Link';

            case 'video':
                return 'Video';

            case 'iframe':
                return 'iFrame';

            case 'gallery':
                return 'Gallery';

            case 'map':
                return 'Map';

            case 'contact-form':
                return 'Contact Form';

            case 'faq':
                return 'FAQ';

            case 'countdown':
                return 'Countdown';

            case 'hero':
                return 'Hero';

            case 'image':
                return 'Image';

            case 'container':
                return window.BuilderContainerRoles
                    ? BuilderContainerRoles.label(node.props?.containerRole)
                    : 'Container';

            default:

                return String(node.type || 'Node')
                    .split('-')
                    .map((part) => (
                        part.charAt(0)
                            .toUpperCase()
                        +
                        part.slice(1)
                    ))
                    .join(' ');
        }

    },

    /*
    |--------------------------------------------------------------
    | Icons
    |--------------------------------------------------------------
    */

    getIcon(node) {

        if (node.type === 'container') {

            return this.containerIcon(
                node.props?.containerRole
            );

        }

        switch (node.type) {

            case 'section':
                return this.icon(`
                    <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                    <path d="M4 10h16"></path>
                    <path d="M9 10v9"></path>
                `);

            case 'heading':
                return this.icon(`
                    <path d="M5 6h14"></path>
                    <path d="M12 6v12"></path>
                    <path d="M9 18h6"></path>
                `);

            case 'text':
            case 'richtext':
                return this.icon(`
                    <path d="M6 7h12"></path>
                    <path d="M6 12h12"></path>
                    <path d="M6 17h8"></path>
                `);

            case 'button':
                return this.icon(`
                    <rect x="5" y="8" width="14" height="8" rx="2"></rect>
                    <path d="M9 12h6"></path>
                `);

            case 'image':
                return this.icon(`
                    <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                    <path d="M8 13l2.2-2.2 3.3 3.3 1.5-1.5L20 17"></path>
                    <circle cx="9" cy="9" r="1"></circle>
                `);

            case 'icon':
                return this.icon(`
                    <path d="M12 3l2.6 5.3 5.9.9-4.3 4.2 1 5.9L12 16.5 6.8 19.3l1-5.9-4.3-4.2 5.9-.9L12 3z"></path>
                `);

            case 'icon-text':
                return this.icon(`
                    <circle cx="7" cy="8" r="2.5"></circle>
                    <path d="M12 7h7"></path>
                    <path d="M12 12h7"></path>
                    <path d="M5 17h14"></path>
                `);

            case 'link':
                return this.icon(`
                    <path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1"></path>
                    <path d="M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"></path>
                `);

            case 'video':
                return this.icon(`
                    <rect x="4" y="6" width="12" height="12" rx="2"></rect>
                    <path d="M16 10l4-2.5v9L16 14"></path>
                `);

            case 'iframe':
                return this.icon(`
                    <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                    <path d="M8 9l-2 3 2 3"></path>
                    <path d="M16 9l2 3-2 3"></path>
                    <path d="M13 8l-2 8"></path>
                `);

            case 'gallery':
                return this.icon(`
                    <rect x="4" y="5" width="7" height="6" rx="1"></rect>
                    <rect x="13" y="5" width="7" height="6" rx="1"></rect>
                    <rect x="4" y="13" width="7" height="6" rx="1"></rect>
                    <rect x="13" y="13" width="7" height="6" rx="1"></rect>
                `);

            case 'map':
                return this.icon(`
                    <path d="M9 18l-5 2V6l5-2 6 2 5-2v14l-5 2-6-2z"></path>
                    <path d="M9 4v14"></path>
                    <path d="M15 6v14"></path>
                `);

            case 'contact-form':
                return this.icon(`
                    <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                    <path d="M7 9h10"></path>
                    <path d="M7 13h10"></path>
                    <path d="M7 17h6"></path>
                `);

            case 'faq':
                return this.icon(`
                    <circle cx="12" cy="12" r="8"></circle>
                    <path d="M9.8 9a2.5 2.5 0 0 1 4.4 1.6c0 1.8-2.2 2-2.2 3.4"></path>
                    <path d="M12 17h.01"></path>
                `);

            case 'countdown':
                return this.icon(`
                    <circle cx="12" cy="13" r="7"></circle>
                    <path d="M12 9v4l3 2"></path>
                    <path d="M9 3h6"></path>
                `);

            case 'hero':
                return this.icon(`
                    <rect x="4" y="6" width="16" height="12" rx="2"></rect>
                    <path d="M8 10h5"></path>
                    <path d="M8 13h8"></path>
                `);

            default:
                return this.icon(`
                    <rect x="6" y="6" width="12" height="12" rx="2"></rect>
                `);

        }

    },

    containerIcon(role) {

        switch (role) {

            case 'grid':
                return this.icon(`
                    <rect x="5" y="5" width="5" height="5" rx="1"></rect>
                    <rect x="14" y="5" width="5" height="5" rx="1"></rect>
                    <rect x="5" y="14" width="5" height="5" rx="1"></rect>
                    <rect x="14" y="14" width="5" height="5" rx="1"></rect>
                `);

            case 'card':
                return this.icon(`
                    <rect x="4" y="6" width="16" height="12" rx="2"></rect>
                    <path d="M7 10h10"></path>
                    <path d="M7 14h6"></path>
                `);

            case 'content':
                return this.icon(`
                    <path d="M6 7h12"></path>
                    <path d="M8 12h8"></path>
                    <path d="M9 17h6"></path>
                `);

            case 'layout':
                return this.icon(`
                    <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                    <path d="M8 5v14"></path>
                    <path d="M16 5v14"></path>
                `);

            default:
                return this.icon(`
                    <rect x="5" y="5" width="14" height="14" rx="2"></rect>
                    <path d="M8 9h8"></path>
                    <path d="M8 15h8"></path>
                `);

        }

    },

    icon(paths) {

        return `
            <svg
                class="h-3.5 w-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
            >
                ${paths}
            </svg>
        `;

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
