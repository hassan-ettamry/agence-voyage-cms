window.BuilderSettingsFields = {

    /*
    |--------------------------------------------------------------------------
    | Render Field
    |--------------------------------------------------------------------------
    */

    render(
        key,
        field,
        value,
        nodeId
    ) {

        switch (field.type) {

            case 'text':

                return this.text(
                    key,
                    field,
                    value,
                    nodeId
                );

            case 'textarea':

                return this.textarea(
                    key,
                    field,
                    value,
                    nodeId
                );

            case 'color':

                return this.color(
                    key,
                    field,
                    value,
                    nodeId
                );

            case 'range':

                return this.range(
                    key,
                    field,
                    value,
                    nodeId
                );

            default:

                return '';

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Text
    |--------------------------------------------------------------------------
    */

    text(
        key,
        field,
        value,
        nodeId
    ) {

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${field.label}

                </label>

                <input

                    type="text"

                    value="${value}"

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            '${nodeId}',
                            '${key}',
                            this.value
                        )
                    "

                    class="
                        w-full
                        border
                        rounded
                        px-3
                        py-2
                        text-sm
                    "
                />

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Textarea
    |--------------------------------------------------------------------------
    */

    textarea(
        key,
        field,
        value,
        nodeId
    ) {

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${field.label}

                </label>

                <textarea

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            '${nodeId}',
                            '${key}',
                            this.value
                        )
                    "

                    class="
                        w-full
                        border
                        rounded
                        px-3
                        py-2
                        text-sm
                        min-h-[120px]
                    "
                >${value}</textarea>

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Color
    |--------------------------------------------------------------------------
    */

    color(
        key,
        field,
        value,
        nodeId
    ) {

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${field.label}

                </label>

                <input

                    type="color"

                    value="${value}"

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            '${nodeId}',
                            '${key}',
                            this.value
                        )
                    "

                />

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Range
    |--------------------------------------------------------------------------
    */

    range(
        key,
        field,
        value,
        nodeId
    ) {

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${field.label}

                </label>

                <input

                    type="range"

                    min="${field.min || 0}"

                    max="${field.max || 100}"

                    value="${value}"

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            '${nodeId}',
                            '${key}',
                            this.value
                        )
                    "

                    class="w-full"
                />

            </div>

        `;

    }

};