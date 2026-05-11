window.BuilderSettingsPanel = {

    /*
    |------------------------------------------------------------------
    | Render Panel
    |------------------------------------------------------------------
    */

    render(node, schema, nodeId) {

        const panel =
            document.getElementById(
                'settings-panel'
            );

        if (!panel) return;

        /*
        |--------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------
        */

        if (!schema?.tabs) {

            panel.innerHTML = `
                <div class="p-4 text-sm text-gray-400">
                    No settings available
                </div>
            `;

            return;
        }

        /*
        |--------------------------------------------------------------
        | Build HTML
        |--------------------------------------------------------------
        */

        let html = '';

        Object.entries(schema.tabs).forEach(

            ([tabKey, tab]) => {

                html += `

                    <div class="border-b border-gray-200">

                        <div class="
                            px-4 py-3
                            font-semibold
                            text-xs
                            uppercase
                            tracking-wider
                            text-gray-500
                            bg-gray-50
                        ">
                            ${tab.title}
                        </div>

                        <div class="p-4 space-y-4">

                `;

                /*
                |------------------------------------------------------
                | Fields
                |------------------------------------------------------
                */

                Object.entries(tab.fields).forEach(

                    ([fieldKey, field]) => {

                        const value =
                            node.props?.[fieldKey]
                            ?? field.default
                            ?? '';

                        html += this.renderField(

                            fieldKey,
                            field,
                            value,
                            nodeId

                        );

                    }

                );

                html += `
                        </div>
                    </div>
                `;

            }

        );

        panel.innerHTML = html;

    },

    /*
    |------------------------------------------------------------------
    | Render Field
    |------------------------------------------------------------------
    */

    renderField(
        key,
        field,
        value,
        nodeId
    ) {

        switch (field.type) {

            /*
            |----------------------------------------------------------
            | TEXT
            |----------------------------------------------------------
            */

            case 'text':

                return `
                    <div>

                        <label class="
                            block mb-1 text-xs
                            font-medium text-gray-600
                        ">
                            ${field.label}
                        </label>

                        <input
                            type="text"

                            value="${value}"

                            oninput="
                                BuilderSettingsPanel.updateField(
                                    '${nodeId}',
                                    '${key}',
                                    this.value
                                )
                            "

                            class="
                                w-full border rounded
                                px-3 py-2 text-sm
                            "
                        />

                    </div>
                `;

            /*
            |----------------------------------------------------------
            | TEXTAREA
            |----------------------------------------------------------
            */

            case 'textarea':

                return `
                    <div>

                        <label class="
                            block mb-1 text-xs
                            font-medium text-gray-600
                        ">
                            ${field.label}
                        </label>

                        <textarea

                            oninput="
                                BuilderSettingsPanel.updateField(
                                    '${nodeId}',
                                    '${key}',
                                    this.value
                                )
                            "

                            class="
                                w-full border rounded
                                px-3 py-2 text-sm
                                min-h-[120px]
                            "
                        >${value}</textarea>

                    </div>
                `;

            /*
            |----------------------------------------------------------
            | COLOR
            |----------------------------------------------------------
            */

            case 'color':

                return `
                    <div>

                        <label class="
                            block mb-1 text-xs
                            font-medium text-gray-600
                        ">
                            ${field.label}
                        </label>

                        <input
                            type="color"

                            value="${value}"

                            oninput="
                                BuilderSettingsPanel.updateField(
                                    '${nodeId}',
                                    '${key}',
                                    this.value
                                )
                            "
                        />

                    </div>
                `;

            /*
            |----------------------------------------------------------
            | RANGE
            |----------------------------------------------------------
            */

            case 'range':

                return `
                    <div>

                        <label class="
                            block mb-1 text-xs
                            font-medium text-gray-600
                        ">
                            ${field.label}
                        </label>

                        <input
                            type="range"

                            min="${field.min || 0}"
                            max="${field.max || 100}"

                            value="${value}"

                            oninput="
                                BuilderSettingsPanel.updateField(
                                    '${nodeId}',
                                    '${key}',
                                    this.value
                                )
                            "

                            class="w-full"
                        />

                    </div>
                `;

            default:

                return '';

        }

    },

    /*
    |------------------------------------------------------------------
    | Update Field
    |------------------------------------------------------------------
    */

    updateField(
        nodeId,
        key,
        value
    ) {

        /*
        |--------------------------------------------------------------
        | Find Node
        |--------------------------------------------------------------
        */

        const node =
            Builder.findNodeById(
                nodeId
            );

        if (!node) return;

        /*
        |--------------------------------------------------------------
        | Ensure Props
        |--------------------------------------------------------------
        */

        if (!node.props) {
            node.props = {};
        }

        /*
        |--------------------------------------------------------------
        | Update Prop
        |--------------------------------------------------------------
        */

        BuilderNodes.updateProps(
            nodeId,
            key,
            value
        );

        /*
        |--------------------------------------------------------------
        | Row Columns Sync
        |--------------------------------------------------------------
        */

        if (

            node.type === 'row'
            &&

            key === 'columns'

        ) {

            const columns =
                parseInt(value);

            /*
            |----------------------------------------------------------
            | Ensure Children
            |----------------------------------------------------------
            */

            if (!node.children) {
                node.children = [];
            }

            /*
            |----------------------------------------------------------
            | Add Missing Columns
            |----------------------------------------------------------
            */

            while (
                node.children.length < columns
            ) {

                node.children.push({

                    id:
                        BuilderComponents.generateId(),

                    type: 'column',

                    accepts: [
                        'text',
                        'heading',
                        'button',
                        'image',
                        'container',
                        'section',
                        'row',
                        'hero'
                    ],

                    props: {},

                    children: []

                });

            }

            /*
            |----------------------------------------------------------
            | Remove Extra Columns
            |----------------------------------------------------------
            */

            while (
                node.children.length > columns
            ) {

                node.children.pop();

            }

        }

        /*
        |--------------------------------------------------------------
        | Re-render
        |--------------------------------------------------------------
        */
        BuilderHistory.push();
        BuilderCanvas.render();

    }

};