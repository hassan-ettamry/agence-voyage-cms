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

        /*
        |--------------------------------------------------------------
        | Element Selection
        |--------------------------------------------------------------
        */

        document
            .querySelectorAll('[data-index]')
            .forEach(element => {

                element.addEventListener(
                    'click',
                    (event) => {

                        event.stopPropagation();

                        const index =
                            parseInt(
                                element.dataset.index
                            );

                        Builder.selectedIndex =
                            index;

                        Builder.selectedElement =
                            element;

                        /*
                        |--------------------------------------------------
                        | Highlight
                        |--------------------------------------------------
                        */

                        document
                            .querySelectorAll(
                                '[data-index]'
                            )
                            .forEach(el => {

                                el.classList.remove(
                                    'ring-2',
                                    'ring-blue-500'
                                );

                            });

                        element.classList.add(
                            'ring-2',
                            'ring-blue-500'
                        );

                        /*
                        |--------------------------------------------------
                        | Open Controls Tab
                        |--------------------------------------------------
                        */

                        BuilderSidebar.switchTab(
                            'controls'
                        );

                        /*
                        |--------------------------------------------------
                        | Settings Panel
                        |--------------------------------------------------
                        */

                        BuilderSettings.select(
                            element
                        );
                        const node =
                            Builder.structure[index];
                    
                        const schema =
                            BuilderSchema.get(
                                node.type
                            );
                        
                        BuilderSettingsPanel.render(
                            node,
                            schema,
                            index
                        );                       

                        /*
                        |--------------------------------------------------
                        | Sync Layers Panel
                        |--------------------------------------------------
                        */

                        if (
                            typeof BuilderRightSidebar
                            !== 'undefined'
                        ) {

                            BuilderRightSidebar
                                .highlightElement(
                                    element
                                );

                        }

                    }
                );

            });

    }

};

/*
|----------------------------------------------------------------------
| Initial Render
|----------------------------------------------------------------------
*/

window.addEventListener(

    'DOMContentLoaded',

    () => {

        BuilderCanvas.render();

    }

);