const BuilderWidgetTypes = [
    'text',
    'richtext',
    'heading',
    'button',
    'image',
    'icon',
    'icon-text',
    'link',
    'video',
    'iframe',
    'gallery',
    'map',
    'contact-form',
    'faq',
    'countdown',
    'hero',
    'destination-grid',
    'featured-destinations',
    'offer-grid',
    'special-offers',
    'offer-card'
];

const BuilderContainerChildren = [
    'container',
    ...BuilderWidgetTypes
];

window.BuilderStructureRules = {

    /*
    |--------------------------------------------------------------------------
    | Accepted Children
    |--------------------------------------------------------------------------
    */

    acceptedChildren: {

        hero: [],

        text: [],

        richtext: [],

        heading: [],

        button: [],

        image: [],

        icon: [],

        'icon-text': [],

        link: [],

        video: [],

        iframe: [],

        gallery: [],

        map: [],

        'contact-form': [],

        faq: [],

        countdown: [],

        'destination-grid': [],

        'featured-destinations': [],

        'offer-grid': [],

        'special-offers': [],

        'offer-card': [],

        section: [
            ...BuilderContainerChildren
        ],

        container: [
            ...BuilderContainerChildren
        ]

    },

    /*
    |--------------------------------------------------------------------------
    | Accepts For Type
    |--------------------------------------------------------------------------
    */

    acceptsForType(type) {

        return [
            ...(this.acceptedChildren[type] || [])
        ];

    },

    /*
    |--------------------------------------------------------------------------
    | Props Normalization
    |--------------------------------------------------------------------------
    */

    isPlainObject(value) {

        return (
            value !== null
            &&
            typeof value === 'object'
            &&
            !Array.isArray(value)
            &&
            Object.prototype.toString.call(value) === '[object Object]'
        );

    },

    ensurePlainProps(props) {

        return this.isPlainObject(props)
            ? props
            : {};

    },

    normalizeNode(node) {

        if (!node || typeof node !== 'object' || Array.isArray(node)) {
            return null;
        }

        node.props =
            this.ensurePlainProps(
                node.props
            );

        if (!Array.isArray(node.children)) {
            node.children = [];
        }

        return node;

    },

    normalizeContainerRole(node, parent = null, siblingIndex = 0) {

        if (
            node?.type !== 'container'
            ||
            !window.BuilderContainerRoles
        ) {
            return;
        }

        node.props =
            this.ensurePlainProps(
                node.props
            );

        node.props.containerRole =
            BuilderContainerRoles.infer(
                node,
                parent,
                siblingIndex
            );

    },

    /*
    |--------------------------------------------------------------------------
    | Can Accept
    |--------------------------------------------------------------------------
    */

    canAccept(parent, child) {

        if (!child) {
            return false;
        }

        if (!parent) {
            return true;
        }

        const childType =
            typeof child === 'string'
                ? child
                : child.type;

        if (!childType) {
            return false;
        }

        if (
            Object.prototype.hasOwnProperty.call(
                this.acceptedChildren,
                parent.type
            )
        ) {

            return this.acceptedChildren[parent.type]
                .includes(childType);

        }

        if (Array.isArray(parent.accepts)) {

            return parent.accepts
                .includes(childType);

        }

        return false;

    },

    /*
    |--------------------------------------------------------------------------
    | Insert Node
    |--------------------------------------------------------------------------
    */

    insertNode(node, targetId = null, position = 'after') {

        node =
            this.normalizeNode(
                node
            );

        if (!node) {
            return null;
        }

        const placement =
            this.findPlacement(
                node,
                targetId,
                position
            );

        if (!placement) {
            return null;
        }

        placement.collection.splice(
            placement.index,
            0,
            node
        );

        return placement;

    },

    /*
    |--------------------------------------------------------------------------
    | Find Placement
    |--------------------------------------------------------------------------
    */

    findPlacement(node, targetId = null, position = 'after') {

        if (!targetId) {

            return {
                parent: null,
                target: null,
                collection: BuilderStore.structure,
                index: BuilderStore.structure.length,
                position: 'root',
                normalized: false
            };

        }

        const target =
            Builder.findNodeById(
                targetId
            );

        if (!target) {
            return null;
        }

        if (
            position === 'inside' &&
            this.canAccept(target, node)
        ) {

            if (!Array.isArray(target.children)) {
                target.children = [];
            }

            return {
                parent: target,
                target,
                collection: target.children,
                index: target.children.length,
                position: 'inside',
                normalized: false
            };

        }

        const placement =
            this.findSiblingPlacement(
                node,
                target,
                position === 'before'
                    ? 'before'
                    : 'after'
            );

        if (
            placement
            &&
            position === 'inside'
        ) {

            placement.normalized = true;

        }

        return placement;

    },

    /*
    |--------------------------------------------------------------------------
    | Find Sibling Placement
    |--------------------------------------------------------------------------
    */

    findSiblingPlacement(node, target, position = 'after') {

        let current =
            target;

        let siblingPosition =
            position;

        while (current) {

            const parent =
                BuilderNodeTraversal.findParent(
                    current.id
                );

            if (!parent) {

                const index =
                    BuilderStore.structure.findIndex(
                        child => child.id === current.id
                    );

                if (index === -1) {
                    return null;
                }

                return {
                    parent: null,
                    target: current,
                    collection: BuilderStore.structure,
                    index:
                        siblingPosition === 'before'
                            ? index
                            : index + 1,
                    position: siblingPosition,
                    normalized: current.id !== target.id
                };

            }

            if (this.canAccept(parent, node)) {

                const index =
                    parent.children.findIndex(
                        child => child.id === current.id
                    );

                if (index === -1) {
                    return null;
                }

                return {
                    parent,
                    target: current,
                    collection: parent.children,
                    index:
                        siblingPosition === 'before'
                            ? index
                            : index + 1,
                    position: siblingPosition,
                    normalized: current.id !== target.id
                };

            }

            current = parent;
            siblingPosition = 'after';

        }

        return null;

    },

    /*
    |--------------------------------------------------------------------------
    | Normalize Tree
    |--------------------------------------------------------------------------
    */

    normalizeTree(nodes = []) {

        const result =
            this.normalizeCollection(
                Array.isArray(nodes)
                    ? nodes
                    : [],
                null
            );

        return [
            ...result.nodes,
            ...result.overflow
        ];

    },

    /*
    |--------------------------------------------------------------------------
    | Normalize Store
    |--------------------------------------------------------------------------
    */

    normalizeStore() {

        BuilderStore.structure =
            this.normalizeTree(
                BuilderStore.structure
            );

    },

    /*
    |--------------------------------------------------------------------------
    | Normalize Collection
    |--------------------------------------------------------------------------
    */

    normalizeCollection(nodes, parent) {

        const normalized = [];
        const overflow = [];

        nodes.forEach((node, index) => {

            node =
                this.normalizeNode(
                    node
                );

            if (!node) {
                return;
            }

            this.normalizeContainerRole(
                node,
                parent,
                index
            );

            const childResult =
                this.normalizeCollection(
                    Array.isArray(node.children)
                        ? node.children
                        : [],
                    node
                );

            node.children =
                childResult.nodes;

            if (this.canAccept(parent, node)) {

                normalized.push(node);

                childResult.overflow.forEach(child => {

                    if (this.canAccept(parent, child)) {
                        normalized.push(child);
                        return;
                    }

                    overflow.push(child);

                });

                return;

            }

            overflow.push(node);

            childResult.overflow.forEach(child => {

                if (this.canAccept(parent, child)) {
                    normalized.push(child);
                    return;
                }

                overflow.push(child);

            });

        });

        return {
            nodes: normalized,
            overflow
        };

    }

};
