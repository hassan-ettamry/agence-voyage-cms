window.BuilderInlineEditing = {

    /*
    |------------------------------------------------------------------
    | Init
    |------------------------------------------------------------------
    */

    init() {

        const canvas =
            document.getElementById(
                'canvas'
            );

        if (!canvas) return;

        /*
        |--------------------------------------------------------------
        | Prevent Duplicate Binding
        |--------------------------------------------------------------
        */

        if (canvas.dataset.inlineEditingBound) {
            return;
        }

        canvas.dataset.inlineEditingBound = 'true';

        /*
        |--------------------------------------------------------------
        | Input Delegation
        |--------------------------------------------------------------
        */

        canvas.addEventListener(

            'input',

            (event) => {

                const editable =
                    event.target.closest(
                        '[contenteditable="true"]'
                    );

                if (!editable) return;

                /*
                |------------------------------------------------------
                | Parent Node
                |------------------------------------------------------
                */

                const nodeElement =
                    editable.closest(
                        '[data-node-id]'
                    );

                if (!nodeElement) return;

                /*
                |------------------------------------------------------
                | Node ID
                |------------------------------------------------------
                */

                const nodeId =
                    nodeElement.dataset.nodeId;

                if (!nodeId) return;

                /*
                |------------------------------------------------------
                | Field
                |------------------------------------------------------
                */

                const field =
                    editable.dataset.field;

                if (!field) return;

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
                | Ensure Props
                |------------------------------------------------------
                */

                if (!node.props) {
                    node.props = {};
                }

                /*
                |------------------------------------------------------
                | Update State
                |------------------------------------------------------
                */

                BuilderNodes.updateProps(
                    nodeId,
                    field,
                    editable.innerText
                );

                /*
                |------------------------------------------------------
                | Push History
                |------------------------------------------------------
                */

                BuilderHistory.push();

                /*
                |------------------------------------------------------
                | Sync Settings Panel
                |------------------------------------------------------
                */

                if (
                    Builder.selectedNodeId
                    === nodeId
                ) {

                    const schema =
                        BuilderSchema.get(
                            node.type
                        );

                    BuilderSettingsPanel.render(
                        node,
                        schema,
                        nodeId
                    );

                }

                /*
                |------------------------------------------------------
                | Refresh Layers
                |------------------------------------------------------
                */

                BuilderRightSidebar.render();

                /*
                |------------------------------------------------------
                | Keep Selection Highlight
                |------------------------------------------------------
                */

                BuilderRightSidebar.highlightNode(
                    nodeId
                );

                /*
                |------------------------------------------------------
                | Debug
                |------------------------------------------------------
                */

                console.log(
                    'INLINE STATE UPDATED:',
                    Builder.structure
                );

            }

        );

    }

};

/*
|----------------------------------------------------------------------
| Auto Init
|----------------------------------------------------------------------
*/

document.addEventListener(

    'DOMContentLoaded',

    () => {

        BuilderInlineEditing.init();

    }

);