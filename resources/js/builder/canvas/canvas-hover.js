window.BuilderCanvasHover = {

    /*
    |--------------------------------------------------------------------------
    | Bind Hover Events
    |--------------------------------------------------------------------------
    */

    bind(canvas) {

        if (!canvas) return;

        /*
        |--------------------------------------------------------------------------
        | Mouse Over
        |--------------------------------------------------------------------------
        */

        canvas.addEventListener(

            'mouseover',

            (event) => {

                const element =

                    BuilderCanvasUtils
                        .getNodeElement(
                            event.target
                        );

                if (!element) return;

                /*
                |--------------------------------------------------------------------------
                | Ignore Selected
                |--------------------------------------------------------------------------
                */

                if (

                    element.dataset.nodeId
                    ===
                    Builder.selectedNodeId

                ) {

                    return;

                }

                /*
                |--------------------------------------------------------------------------
                | Add Hover
                |--------------------------------------------------------------------------
                */

                element.classList.add(

                    'outline',
                    'outline-1',
                    'outline-dashed',
                    'outline-blue-400',
                    'outline-offset-[-1px]',
                    'cursor-pointer'

                );

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Mouse Out
        |--------------------------------------------------------------------------
        */

        canvas.addEventListener(

            'mouseout',

            (event) => {

                const element =

                    BuilderCanvasUtils
                        .getNodeElement(
                            event.target
                        );

                if (!element) return;

                /*
                |--------------------------------------------------------------------------
                | Ignore Selected
                |--------------------------------------------------------------------------
                */

                if (

                    element.dataset.nodeId
                    ===
                    Builder.selectedNodeId

                ) {

                    return;

                }

                /*
                |--------------------------------------------------------------------------
                | Remove Hover
                |--------------------------------------------------------------------------
                */

                element.classList.remove(

                    'outline',
                    'outline-1',
                    'outline-dashed',
                    'outline-blue-400',
                    'outline-offset-[-1px]',
                    'cursor-pointer'

                );

            }

        );

    }

};