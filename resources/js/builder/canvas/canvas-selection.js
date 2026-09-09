window.BuilderCanvasSelection = {

    /*
    |--------------------------------------------------------------------------
    | Bind Selection
    |--------------------------------------------------------------------------
    */

    bind(canvas) {

        if (!canvas) return;

        canvas.addEventListener(

            'click',

            (event) => {

                const element =

                    BuilderCanvasUtils
                        .getNodeElement(
                            event.target
                        );

                if (!element) return;

                event.stopPropagation();

                /*
                |--------------------------------------------------------------------------
                | Node ID
                |--------------------------------------------------------------------------
                */

                const nodeId =
                    element.dataset.nodeId;

                if (!nodeId) return;

                BuilderSelectionManager.select(
                    nodeId,
                    element,
                    {
                        source: 'canvas'
                    }
                );

            }

        );

    }

};
