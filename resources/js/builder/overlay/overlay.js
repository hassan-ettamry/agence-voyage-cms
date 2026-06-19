window.BuilderOverlay = {

    currentElement: null,

    currentNodeId: null,

    mode: null,

    align: null,

    height: null,

    currentItems: [],

    /*
    |--------------------------------------------------------------------------
    | Init
    |--------------------------------------------------------------------------
    */

    init() {

        window.addEventListener(

            'resize',

            () => {

                if (this.currentItems.length) {

                    this.showGroup(
                        this.currentItems
                    );

                    return;

                }

                if (

                    this.currentElement
                    &&
                    this.currentNodeId

                ) {

                    this.show(

                        this.currentElement,
                        this.currentNodeId,
                        {
                            mode: this.mode || 'selected',
                            align: this.align || 'left',
                            offsetY: 0
                        }

                    );

                }

            }

        );

        console.log(
            'Overlay Initialized'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Show Overlay
    |--------------------------------------------------------------------------
    */

    show(element, nodeId, options = {}) {

        if (!element || !nodeId) {
            return;
        }

        const {
            mode = 'selected'
        } = options;

        this.currentElement = element;
        this.currentNodeId = nodeId;
        this.mode = mode;
        this.align = options.align || 'left';
        this.height = options.height || 24;
        this.currentItems = [];

        const root =

            BuilderOverlayElements
                .getRoot();

        if (!root) return;

        const position =

            BuilderOverlayPosition
                .calculate(
                    element,
                    {
                        align: options.align || 'left',
                        offsetY: options.offsetY || 0
                    }
                );

        const node =

            Builder.findNodeById(
                nodeId
            );

        const label =

            node?.type?.toLowerCase()
            || 'element';

        root.innerHTML =

            BuilderOverlayUI.render(

                nodeId,
                label,
                position,
                mode

            );

    },

    /*
    |--------------------------------------------------------------------------
    | Show Multiple Overlays
    |--------------------------------------------------------------------------
    */

    showGroup(items = []) {

        const root =

            BuilderOverlayElements
                .getRoot();

        if (!root) return;

        const validItems =

            items
                .filter(item => item?.element && item?.nodeId);

        const overlays =

            validItems
                .map(item => {

                    const position =

                        BuilderOverlayPosition
                            .calculate(
                                item.element,
                                {
                                    align: item.align || 'left',
                                    offsetY: item.offsetY || 0
                                }
                            );

                    const node =

                        Builder.findNodeById(
                            item.nodeId
                        );

                    const label =

                        node?.type?.toLowerCase()
                        || 'element';

                    return BuilderOverlayUI.render(

                        item.nodeId,
                        label,
                        position,
                        item.mode || 'hover'

                    );

                });

        const active =
            validItems[validItems.length - 1];

        this.currentElement =
            active?.element || null;

        this.currentNodeId =
            active?.nodeId || null;

        this.mode =
            active?.mode || null;

        this.align =
            active?.align || null;

        this.height =
            active?.height || null;

        this.currentItems =
            validItems;

        root.innerHTML =
            overlays.join('');

    },

    /*
    |--------------------------------------------------------------------------
    | Hide
    |--------------------------------------------------------------------------
    */

    hide(options = {}) {

        const {
            hoverOnly = false
        } = options;

        if (hoverOnly && this.mode === 'selected') {
            return;
        }

        const root =

            BuilderOverlayElements
                .getRoot();

        this.currentElement = null;
        this.currentNodeId = null;
        this.mode = null;
        this.align = null;
        this.height = null;
        this.currentItems = [];

        if (!root) return;

        root.innerHTML = '';

    }

};
