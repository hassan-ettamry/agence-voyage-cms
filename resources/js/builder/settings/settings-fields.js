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

        const label =
            BuilderHtmlEscape.html(field.label);

        const fieldValue =
            BuilderHtmlEscape.attribute(value);

        const safeNodeId =
            BuilderHtmlEscape.jsString(nodeId);

        const safeKey =
            BuilderHtmlEscape.jsString(key);

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${label}

                </label>

                <input

                    type="text"

                    value="${fieldValue}"

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            ${safeNodeId},
                            ${safeKey},
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

        const label =
            BuilderHtmlEscape.html(field.label);

        const fieldValue =
            BuilderHtmlEscape.html(value);

        const safeNodeId =
            BuilderHtmlEscape.jsString(nodeId);

        const safeKey =
            BuilderHtmlEscape.jsString(key);

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${label}

                </label>

                <textarea

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            ${safeNodeId},
                            ${safeKey},
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
                >${fieldValue}</textarea>

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

        const label =
            BuilderHtmlEscape.html(field.label);

        const fieldValue =
            BuilderHtmlEscape.attribute(value);

        const safeNodeId =
            BuilderHtmlEscape.jsString(nodeId);

        const safeKey =
            BuilderHtmlEscape.jsString(key);

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${label}

                </label>

                <input

                    type="color"

                    value="${fieldValue}"

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            ${safeNodeId},
                            ${safeKey},
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

        const label =
            BuilderHtmlEscape.html(field.label);

        const min =
            BuilderHtmlEscape.attribute(field.min || 0);

        const max =
            BuilderHtmlEscape.attribute(field.max || 100);

        const fieldValue =
            BuilderHtmlEscape.attribute(value);

        const safeNodeId =
            BuilderHtmlEscape.jsString(nodeId);

        const safeKey =
            BuilderHtmlEscape.jsString(key);

        return `

            <div>

                <label class="
                    block
                    mb-1
                    text-xs
                    font-medium
                    text-gray-600
                ">

                    ${label}

                </label>

                <input

                    type="range"

                    min="${min}"

                    max="${max}"

                    value="${fieldValue}"

                    oninput="
                        BuilderSettingsUpdater.updateField(
                            ${safeNodeId},
                            ${safeKey},
                            this.value
                        )
                    "

                    class="w-full"
                />

            </div>

        `;

    }

};
