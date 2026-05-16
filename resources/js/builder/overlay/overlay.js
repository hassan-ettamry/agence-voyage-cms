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

        document.addEventListener(

            'click',

            (event) => {

                const canvas =

                    document.getElementById(
                        'canvas'
                    );

                const root =

                    BuilderOverlayElements
                        .getRoot();

                if (

                    canvas
                    &&
                    !canvas.contains(event.target)

                    &&

                    root
                    &&
                    !root.contains(event.target)

                ) {

                    this.hide();

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

        if (!root) return;

        root.innerHTML = '';

    }

};