window.BuilderOverlay = {

    currentElement: null,

    /*
    |--------------------------------------------------------------------------
    | Init
    |--------------------------------------------------------------------------
    */

    init() {

        window.addEventListener(

            'resize',

            () => {

                if (

                    this.currentElement
                    &&
                    BuilderStore.selectedNodeId

                ) {

                    this.show(

                        this.currentElement,
                        BuilderStore.selectedNodeId

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

    show(element, nodeId) {

        if (!element || !nodeId) {
            return;
        }

        this.currentElement = element;

        const root =

            BuilderOverlayElements
                .getRoot();

        if (!root) return;

        const position =

            BuilderOverlayPosition
                .calculate(element);

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
                position

            );

    },

    /*
    |--------------------------------------------------------------------------
    | Hide
    |--------------------------------------------------------------------------
    */

    hide() {

        const root =

            BuilderOverlayElements
                .getRoot();

        this.currentElement = null;

        if (!root) return;

        root.innerHTML = '';

    }

};
