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

            case 'richtext':

                return this.richtext(
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

            case 'number':

                return this.number(
                    key,
                    field,
                    value,
                    nodeId
                );

            case 'select':

                return this.select(
                    key,
                    field,
                    value,
                    nodeId
                );

            case 'toggle':

                return this.toggle(
                    key,
                    field,
                    value,
                    nodeId
                );

            case 'media':

                return this.media(
                    key,
                    field,
                    value,
                    nodeId
                );

            case 'entity-select':

                return this.entitySelect(key, field, value, nodeId, false);

            case 'entity-multiselect':

                return this.entitySelect(key, field, value, nodeId, true);

            case 'repeater':

                return this.repeater(key, field, value, nodeId);

            case 'notice':

                return this.notice(
                    field
                );

            default:

                return '';

        }

    },

    help(field) {

        if (!field?.help) {
            return '';
        }

        return `
            <p class="mt-1 text-[11px] leading-4 text-slate-400">
                ${BuilderHtmlEscape.html(field.help)}
            </p>
        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Notice
    |--------------------------------------------------------------------------
    */

    notice(field) {

        return `
            <div class="rounded border border-blue-100 bg-blue-50 px-3 py-2 text-xs leading-5 text-blue-800">
                ${BuilderHtmlEscape.html(field.text || field.label || '')}
            </div>
        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Toggle
    |--------------------------------------------------------------------------
    */

    toggle(
        key,
        field,
        value,
        nodeId
    ) {

        return this.select(
            key,
            {
                ...field,
                type: 'select',
                options: field.options || [
                    { value: 'yes', label: 'Yes' },
                    { value: 'no', label: 'No' }
                ]
            },
            value,
            nodeId
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    media(
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
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

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

                ${value ? `<img src="${fieldValue}" alt="" class="mb-2 aspect-video w-full rounded border border-slate-200 bg-slate-100 object-cover">` : ''}

                <div class="flex gap-2">
                    <input
                        type="text"
                        value="${fieldValue}"
                        data-target-node-id="${safeNodeId}"
                        data-setting-field="${safeKey}"
                        class="
                            w-full
                            rounded
                            border
                            border-gray-200
                            px-3
                            py-2
                            text-sm
                            outline-none
                            focus:border-blue-400
                        "
                    />
                    <button type="button" data-action="open-builder-media" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" class="shrink-0 rounded bg-slate-900 px-3 text-xs font-semibold text-white hover:bg-slate-700">Choose</button>
                </div>

                ${value ? `<button type="button" data-action="clear-builder-media" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" class="mt-2 text-xs font-medium text-red-600 hover:text-red-700">Remove image</button>` : ''}

                ${this.help(field)}

            </div>

        `;

    },

    entitySelect(key, field, value, nodeId, multiple = false) {

        const items = window.builderDataOptions?.[field.entity] || [];
        const selected = new Set(Array.isArray(value) ? value.map(String) : [String(value || '')]);
        const options = items.map(item => `
            <option value="${BuilderHtmlEscape.attribute(item.id)}" ${selected.has(String(item.id)) ? 'selected' : ''}>
                ${BuilderHtmlEscape.html(item.label)}
            </option>
        `).join('');

        return `
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">${BuilderHtmlEscape.html(field.label)}</label>
                <select ${multiple ? 'multiple size="6"' : ''} data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}" data-setting-field="${BuilderHtmlEscape.attribute(key)}" class="w-full rounded border border-gray-200 bg-white px-3 py-2 text-sm">
                    ${multiple ? '' : '<option value="">None</option>'}
                    ${options}
                </select>
                ${this.help(field)}
            </div>
        `;

    },

    repeater(key, field, value, nodeId) {

        const items = BuilderRepeater.items(value, field);
        const safeNodeId = BuilderHtmlEscape.attribute(nodeId);
        const safeKey = BuilderHtmlEscape.attribute(key);
        const cards = items.map((item, index) => `
            <div class="rounded-lg border border-slate-200 bg-white p-3">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-700">Item ${index + 1}</span>
                    <div class="flex gap-1">
                        <button type="button" data-action="repeater-up" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" data-repeater-index="${index}" class="rounded px-2 py-1 text-xs text-slate-500 hover:bg-slate-100" ${index === 0 ? 'disabled' : ''}>↑</button>
                        <button type="button" data-action="repeater-down" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" data-repeater-index="${index}" class="rounded px-2 py-1 text-xs text-slate-500 hover:bg-slate-100" ${index === items.length - 1 ? 'disabled' : ''}>↓</button>
                        <button type="button" data-action="repeater-remove" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" data-repeater-index="${index}" class="rounded px-2 py-1 text-xs text-red-600 hover:bg-red-50">Remove</button>
                    </div>
                </div>
                <div class="space-y-2">
                    ${(field.itemFields || []).map(itemField => {
                        const itemKey = BuilderHtmlEscape.attribute(itemField.key);
                        const itemValue = BuilderHtmlEscape.attribute(item[itemField.key] ?? '');
                        if (itemField.type === 'media') {
                            return `
                                <div class="text-[11px] font-medium text-slate-600">
                                    ${BuilderHtmlEscape.html(itemField.label)}
                                    ${itemValue ? `<img src="${itemValue}" alt="" class="mt-1 aspect-video w-full rounded border border-slate-200 object-cover">` : ''}
                                    <div class="mt-1 flex gap-2">
                                        <button type="button" data-action="open-repeater-media" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" data-repeater-index="${index}" data-repeater-field="${itemKey}" class="rounded border border-slate-200 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">${itemValue ? 'Replace' : 'Choose image'}</button>
                                        ${itemValue ? `<button type="button" data-action="clear-repeater-media" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" data-repeater-index="${index}" data-repeater-field="${itemKey}" class="px-2 py-1 text-xs text-red-600">Remove</button>` : ''}
                                    </div>
                                </div>
                            `;
                        }
                        return `
                            <label class="block text-[11px] font-medium text-slate-600">
                                ${BuilderHtmlEscape.html(itemField.label)}
                                <input type="${itemField.type === 'number' ? 'number' : 'text'}" value="${itemValue}" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" data-repeater-index="${index}" data-repeater-field="${itemKey}" class="mt-1 w-full rounded border border-slate-200 px-2.5 py-2 text-xs outline-none focus:border-blue-400">
                            </label>
                        `;
                    }).join('')}
                </div>
            </div>
        `).join('');

        return `
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-600">${BuilderHtmlEscape.html(field.label)}</span>
                    <button type="button" data-action="repeater-add" data-target-node-id="${safeNodeId}" data-setting-field="${safeKey}" class="rounded bg-blue-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Add item</button>
                </div>
                ${cards || '<div class="rounded border border-dashed border-slate-300 p-4 text-center text-xs text-slate-400">No items yet.</div>'}
                ${this.help(field)}
            </div>
        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Rich Text
    |--------------------------------------------------------------------------
    */

    richtext(
        key,
        field,
        value,
        nodeId
    ) {

        const label =
            BuilderHtmlEscape.html(field.label || 'RichText');

        const fieldValue =
            value || field.default || '';

        const safeNodeId =
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

        return `

            <div class="space-y-3">

                <label class="
                    block
                    text-[13px]
                    font-normal
                    text-slate-700
                ">
                    ${label}
                </label>

                <div
                    class="
                        w-[236px]
                        max-w-full
                        ml-5
                        bg-[#eff6ff]
                        p-3.5
                    "
                    data-richtext-toolbar="${safeNodeId}"
                >
                    <div class="grid grid-cols-5 gap-1.5">
                        ${this.richtextButton('table', 'Insert table')}
                        ${this.richtextButton('undo', 'Undo')}
                        ${this.richtextButton('redo', 'Redo')}
                        ${this.richtextButton('formatBlock', 'Paragraph', 'P')}
                        ${this.richtextButton('bold', 'Bold', 'B')}
                        ${this.richtextButton('italic', 'Italic', 'I')}
                        ${this.richtextButton('formatBlock:h2', 'Heading', 'H')}
                        ${this.richtextButton('strikeThrough', 'Strike', 'T')}
                        ${this.richtextButton('underline', 'Underline', 'U')}
                        ${this.richtextButton('subscript', 'Subscript', 'X2')}
                        ${this.richtextButton('superscript', 'Superscript', 'X2')}
                        ${this.richtextColor('foreColor', '#111111')}
                        ${this.richtextButton('foreColor:#111111', 'Text color', 'A')}
                        ${this.richtextColor('hiliteColor', '#fde047')}
                        ${this.richtextButton('hiliteColor:#111111', 'Background color', 'A')}
                        ${this.richtextButton('justifyLeft', 'Align left')}
                        ${this.richtextButton('justifyCenter', 'Align center')}
                        ${this.richtextButton('justifyRight', 'Align right')}
                        ${this.richtextButton('justifyFull', 'Justify')}
                        ${this.richtextButton('insertUnorderedList', 'Bullet list')}
                        ${this.richtextButton('insertOrderedList', 'Number list')}
                        ${this.richtextButton('insertChecklist', 'Checklist')}
                        ${this.richtextButton('createLink', 'Link')}
                        ${this.richtextButton('unlink', 'Unlink')}
                        ${this.richtextButton('removeFormat', 'Clear')}
                        ${this.richtextButton('blockquote', 'Quote')}
                        ${this.richtextButton('insertHorizontalRule', 'Divider')}
                        ${this.richtextButton('insertParagraph', 'Paragraph break')}
                        ${this.richtextButton('outdent', 'Outdent')}
                        ${this.richtextButton('indent', 'Indent')}
                        ${this.richtextButton('insertImage', 'Image')}
                        ${this.richtextButton('insertVideo', 'Video')}
                        ${this.richtextButton('code', 'Code')}
                        ${this.richtextButton('audio', 'Audio')}
                        ${this.richtextButton('insertParagraph', 'New line')}
                    </div>
                </div>

                <div
                    contenteditable="true"
                    data-target-node-id="${safeNodeId}"
                    data-setting-field="${safeKey}"
                    data-richtext-editor="true"
                    class="
                        min-h-[348px]
                        mx-3
                        w-[calc(100%-1.5rem)]
                        rounded-sm
                        border
                        border-slate-100
                        bg-white
                        p-4
                        text-[18px]
                        leading-[1.55]
                        text-slate-800
                        outline-none
                        focus:border-blue-200
                    "
                >${fieldValue}</div>

            </div>

        `;

    },

    richtextButton(command, title, label = null) {

        const safeCommand =
            BuilderHtmlEscape.attribute(command);

        const safeTitle =
            BuilderHtmlEscape.attribute(title);

        const safeLabel =
            label
                ? BuilderHtmlEscape.html(label)
                : this.richtextIcon(command);

        return `

            <button
                type="button"
                tabindex="-1"
                data-action="richtext-command"
                data-richtext-command="${safeCommand}"
                title="${safeTitle}"
                class="
                    flex
                    h-8
                    w-8
                    items-center
                    justify-center
                    rounded
                    text-[17px]
                    font-semibold
                    leading-none
                    text-slate-800
                    hover:bg-blue-100
                    focus:bg-blue-100
                    focus:outline-none
                "
            >${safeLabel}</button>

        `;

    },

    richtextColor(command, color) {

        const safeCommand =
            BuilderHtmlEscape.attribute(command);

        const safeColor =
            BuilderHtmlEscape.attribute(color);

        return `

            <button
                type="button"
                tabindex="-1"
                data-action="richtext-command"
                data-richtext-command="${safeCommand}"
                data-richtext-value="${safeColor}"
                class="
                    flex
                    h-8
                    w-8
                    items-center
                    justify-center
                    rounded
                    hover:bg-blue-100
                    focus:bg-blue-100
                    focus:outline-none
                "
            >
                <span
                    class="block h-4 w-4 border border-slate-300"
                    style="background:${safeColor}"
                ></span>
            </button>

        `;

    },

    richtextIcon(command) {

        const svg = {
            table: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="1"/><path d="M4 10h16M10 4v16"/></svg>',
            undo: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14 4 9l5-5"/><path d="M4 9h10a6 6 0 1 1 0 12h-2"/></svg>',
            redo: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 14 5-5-5-5"/><path d="M20 9H10a6 6 0 1 0 0 12h2"/></svg>',
            bold: 'B',
            italic: '<span class="italic">I</span>',
            strikeThrough: '<span class="line-through">T</span>',
            underline: '<span class="underline">U</span>',
            subscript: '<span>X<sub>2</sub></span>',
            superscript: '<span>X<sup>2</sup></span>',
            justifyLeft: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 11h10M4 16h16M4 21h10"/></svg>',
            justifyCenter: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M7 11h10M4 16h16M7 21h10"/></svg>',
            justifyRight: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M10 11h10M4 16h16M10 21h10"/></svg>',
            justifyFull: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 11h16M4 16h16M4 21h16"/></svg>',
            insertUnorderedList: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6h11M9 12h11M9 18h11"/><path d="M4 6h.01M4 12h.01M4 18h.01"/></svg>',
            insertOrderedList: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 6h10M10 12h10M10 18h10"/><path d="M4 6h1v4M4 10h2M4 14h2l-2 3h2"/></svg>',
            insertChecklist: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="m8 12 2.5 2.5L16 9"/></svg>',
            createLink: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"/></svg>',
            unlink: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 7h1a5 5 0 0 1 0 10h-1M9 17H8A5 5 0 0 1 8 7h1"/><path d="M8 12h8M3 3l18 18"/></svg>',
            removeFormat: '<span class="text-[12px]">&lt;/&gt;</span>',
            blockquote: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 12H5a4 4 0 1 1 4-4v8M19 12h-3a4 4 0 1 1 4-4v8"/></svg>',
            insertHorizontalRule: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/></svg>',
            insertParagraph: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 7v10H8"/><path d="m11 14-3 3 3 3"/></svg>',
            outdent: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 7h9M11 12h9M11 17h9"/><path d="m7 8-4 4 4 4"/></svg>',
            indent: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h9M4 12h9M4 17h9"/><path d="m17 8 4 4-4 4"/></svg>',
            insertImage: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 16 5-5 4 4 2-2 7 7"/><circle cx="8" cy="9" r="1.5"/></svg>',
            insertVideo: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3z"/></svg>',
            code: '<span class="text-[12px]">&lt;/&gt;</span>',
            audio: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
            selectAll: '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M8 8h8v8H8z"/></svg>'
        };

        return svg[command]
            || '<span class="text-[12px]">.</span>';

    },

    /*
    |--------------------------------------------------------------------------
    | Number
    |--------------------------------------------------------------------------
    */

    number(
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
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

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

                    type="number"

                    min="${BuilderHtmlEscape.attribute(field.min ?? '')}"

                    max="${BuilderHtmlEscape.attribute(field.max ?? '')}"

                    value="${fieldValue}"

                    data-target-node-id="${safeNodeId}"

                    data-setting-field="${safeKey}"

                    class="
                        w-full
                        border
                        rounded
                        px-3
                        py-2
                        text-sm
                    "
                />

                ${this.help(field)}

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Select
    |--------------------------------------------------------------------------
    */

    select(
        key,
        field,
        value,
        nodeId
    ) {

        const label =
            BuilderHtmlEscape.html(field.label);

        const fieldValue =
            String(value ?? field.default ?? '');

        const safeNodeId =
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

        const options =
            Array.isArray(field.options)
                ? field.options
                : [];

        const optionsHtml =
            options.map(option => {

                const optionValue =
                    typeof option === 'object'
                        ? String(option.value ?? '')
                        : String(option);

                const optionLabel =
                    typeof option === 'object'
                        ? String(option.label ?? optionValue)
                        : optionValue;

                const selected =
                    optionValue === fieldValue
                        ? 'selected'
                        : '';

                return `
                    <option
                        value="${BuilderHtmlEscape.attribute(optionValue)}"
                        ${selected}
                    >
                        ${BuilderHtmlEscape.html(optionLabel)}
                    </option>
                `;

            }).join('');

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

                <select

                    data-target-node-id="${safeNodeId}"

                    data-setting-field="${safeKey}"

                    class="
                        w-full
                        border
                        rounded
                        px-3
                        py-2
                        text-sm
                        bg-white
                    "
                >
                    ${optionsHtml}
                </select>

                ${this.help(field)}

            </div>

        `;

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
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

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

                    data-target-node-id="${safeNodeId}"

                    data-setting-field="${safeKey}"

                    class="
                        w-full
                        border
                        rounded
                        px-3
                        py-2
                        text-sm
                    "
                />

                ${this.help(field)}

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
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

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

                    data-target-node-id="${safeNodeId}"

                    data-setting-field="${safeKey}"

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

                ${this.help(field)}

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
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

        const node =
            Builder.findNodeById(
                nodeId
            );

        const hasLocalValue = BuilderObjectPath.has(
            BuilderStructureRules.ensurePlainProps(node?.props),
            key
        );

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

                <div class="flex items-center gap-2">

                    <input

                        type="color"

                        value="${fieldValue}"

                        data-target-node-id="${safeNodeId}"

                        data-setting-field="${safeKey}"

                    />

                    ${hasLocalValue ? `
                        <button
                            type="button"
                            data-action="use-theme-default"
                            data-target-node-id="${safeNodeId}"
                            data-setting-field="${safeKey}"
                            class="text-[11px] font-medium text-blue-600 hover:text-blue-700"
                        >
                            Use theme default
                        </button>
                    ` : ''}

                </div>

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
            BuilderHtmlEscape.attribute(field.min ?? 0);

        const max =
            BuilderHtmlEscape.attribute(field.max ?? 100);

        const step =
            BuilderHtmlEscape.attribute(field.step ?? 1);

        const fieldValue =
            BuilderHtmlEscape.attribute(value);

        const safeNodeId =
            BuilderHtmlEscape.attribute(nodeId);

        const safeKey =
            BuilderHtmlEscape.attribute(key);

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

                    step="${step}"

                    value="${fieldValue}"

                    data-target-node-id="${safeNodeId}"

                    data-setting-field="${safeKey}"

                    class="w-full"
                />

                ${this.help(field)}

            </div>

        `;

    }

};
