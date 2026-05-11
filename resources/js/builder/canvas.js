window.BuilderCanvas = {

    /*
    |------------------------------------------------------------------
    | Render Canvas
    |------------------------------------------------------------------
    */

    async render() {

        const canvas =
            document.getElementById('canvas');

        if (!canvas) return;

        /*
        |--------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------
        */

        if (!Builder.getStructure().length) {

            canvas.innerHTML = `

                <div
                    id="canvas-empty-state"
                    class="
                        h-[500px]
                        flex
                        items-center
                        justify-center
                        text-gray-400
                    "
                >

                    Drag components here

                </div>

            `;

            return;
        }

        try {

            /*
            |----------------------------------------------------------
            | Request Rendered HTML
            |----------------------------------------------------------
            */

            const response = await fetch(

                '/builder/render',

                {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .content

                    },

                    body: JSON.stringify({

                        structure:
                            Builder.getStructure()

                    })

                }

            );

            /*
            |----------------------------------------------------------
            | Get HTML
            |----------------------------------------------------------
            */

            const html =
                await response.text();

            /*
            |----------------------------------------------------------
            | Inject HTML
            |----------------------------------------------------------
            */

            canvas.innerHTML = html;

            /*
            |----------------------------------------------------------
            | Bind Events
            |----------------------------------------------------------
            */

            this.bindEvents();

            BuilderRightSidebar.render();

        } catch (error) {

            console.error(
                'Canvas render error:',
                error
            );

        }

    },

    /*
    |------------------------------------------------------------------
    | Bind Canvas Events
    |------------------------------------------------------------------
    */

    bindEvents() {

        const canvas =
            document.getElementById('canvas');

        if (!canvas) return;

        /*
        |--------------------------------------------------------------
        | Prevent Multiple Bindings
        |--------------------------------------------------------------
        */

        if (canvas.dataset.eventsBound) {
            return;
        }

        canvas.dataset.eventsBound = 'true';

        /*
        |--------------------------------------------------------------
        | Canvas Click Delegation
        |--------------------------------------------------------------
        */

        canvas.addEventListener(

            'click',

            (event) => {

                /*
                |------------------------------------------------------
                | Find Node Element
                |------------------------------------------------------
                */

                const element =
                    event.target.closest(
                        '[data-node-id]'
                    );

                if (!element) return;

                event.stopPropagation();

                /*
                |------------------------------------------------------
                | Node ID
                |------------------------------------------------------
                */

                const nodeId =
                    element.dataset.nodeId;

                if (!nodeId) return;

                /*
                |------------------------------------------------------
                | Find Node
                |------------------------------------------------------
                */

                const node =
                    Builder.findNodeById(
                        nodeId
                    );

                if (!node) return;

                /*
                |------------------------------------------------------
                | Save Selection
                |------------------------------------------------------
                */

                Builder.selectedNodeId =
                    nodeId;

                Builder.selectedElement =
                    element;

                /*
                |------------------------------------------------------
                | Remove Old Highlights
                |------------------------------------------------------
                */

                document
                    .querySelectorAll(
                        '[data-node-id]'
                    )
                    .forEach(el => {

                        el.classList.remove(
                            'ring-2',
                            'ring-blue-500'
                        );

                    });

                /*
                |------------------------------------------------------
                | Highlight Selected
                |------------------------------------------------------
                */

                element.classList.add(
                    'ring-2',
                    'ring-blue-500'
                );

                /*
                |------------------------------------------------------
                | Open Controls
                |------------------------------------------------------
                */

                BuilderSidebar.switchTab(
                    'controls'
                );

                /*
                |------------------------------------------------------
                | Render Settings Panel
                |------------------------------------------------------
                */

                const schema =
                    BuilderSchema.get(
                        node.type
                    );

                BuilderSettingsPanel.render(
                    node,
                    schema,
                    nodeId
                );

                /*
                |------------------------------------------------------
                | Layers Highlight
                |------------------------------------------------------
                */

                if (
                    typeof BuilderRightSidebar
                    !== 'undefined'
                ) {

                    BuilderRightSidebar
                        .highlightNode(
                            nodeId
                        );

                }

            }

        );

    }

};

/*
|--------------------------------------------------------------------------
| Initial Render
|--------------------------------------------------------------------------
*/

window.addEventListener(

    'DOMContentLoaded',

    () => {

        BuilderCanvas.render();

    }

);