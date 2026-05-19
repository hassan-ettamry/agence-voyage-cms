window.BuilderSettingsPanel = {

    /*
    |--------------------------------------------------------------------------
    | Render Panel
    |--------------------------------------------------------------------------
    */

    render(node, schema, nodeId) {

        const panel =
            document.getElementById(
                'settings-panel'
            );

        if (!panel) return;

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Build HTML
        |--------------------------------------------------------------------------
        */

        let html = '';

        Object.entries(schema.tabs).forEach(

            ([tabKey, tab]) => {

                const title =
                    BuilderHtmlEscape.html(tab.title);

                html += `

                    <div class="border-b border-gray-200">

                        <div class="
                            px-4
                            py-3
                            font-semibold
                            text-xs
                            uppercase
                            tracking-wider
                            text-gray-500
                            bg-gray-50
                        ">

                            ${title}

                        </div>

                        <div class="p-4 space-y-4">

                `;

                /*
                |--------------------------------------------------------------------------
                | Fields
                |--------------------------------------------------------------------------
                */

                Object.entries(tab.fields).forEach(

                    ([fieldKey, field]) => {

                        const value =

                            node.props?.[fieldKey]

                            ??

                            field.default

                            ??

                            '';

                        html +=
                            BuilderSettingsFields.render(

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

    }

};
