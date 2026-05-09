window.BuilderSettingsPanel = {

    render(node, schema, index) {

        const panel =
            document.getElementById('settings-panel');

        if (!panel) return;

        if (!schema?.tabs) {

            panel.innerHTML = `
                <div class="p-4 text-sm text-gray-400">
                    No settings available
                </div>
            `;

            return;
        }

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
                            index
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

    renderField(key, field, value, index) {

        switch (field.type) {

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
                                    ${index},
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
                                    ${index},
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
                                    ${index},
                                    '${key}',
                                    this.value
                                )
                            "
                        />

                    </div>
                `;

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
                                    ${index},
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

    updateField(index, key, value) {

        if (!Builder.structure[index]) return;

        Builder.structure[index].props[key] = value;

        BuilderCanvas.render();
    }
};