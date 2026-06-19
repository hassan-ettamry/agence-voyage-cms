window.BuilderSettingsPanel = {

    openSettingTabs: {},

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

        BuilderStructureRules.normalizeNode(
            node
        );

        if (['section', 'container'].includes(node.type)) {

            panel.innerHTML =
                this.sectionPanel(
                    node,
                    nodeId,
                    node.type
                );

            return;

        }

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

        let html =
            this.usesControlTabs(
                node,
                schema
            )
                ? this.controlTabs(
                    schema.controlTabs
                )
                : '';

        Object.entries(schema.tabs || {}).forEach(

            ([tabKey, tab]) => {

                const title =
                    BuilderHtmlEscape.html(tab.title);

                const open =
                    this.isSettingTabOpen(
                        nodeId,
                        tabKey
                    );

                html += `

                    <div class="border-b border-gray-100 bg-white">

                        <button
                            type="button"
                            data-action="settings-tab-toggle"
                            data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                            data-settings-tab="${BuilderHtmlEscape.attribute(tabKey)}"
                            class="
                            flex
                            h-10
                            w-full
                            items-center
                            justify-between
                            px-4
                            text-left
                            text-[13px]
                            font-medium
                            uppercase
                            tracking-wide
                            text-slate-600
                            ${open ? 'bg-[#eff6ff]' : 'bg-white'}
                            hover:bg-[#eff6ff]
                        "
                        >

                            <span>${title}</span>
                            <span class="text-slate-700">
                                <svg
                                    class="h-3.5 w-3.5 transition-transform ${open ? '' : 'rotate-180'}"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M5 12l5-5 5 5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>

                        </button>

                        <div
                            data-settings-tab-body="${BuilderHtmlEscape.attribute(tabKey)}"
                            class="${open ? '' : 'hidden'} space-y-4 bg-white p-4"
                        >

                `;

                /*
                |--------------------------------------------------------------------------
                | Fields
                |--------------------------------------------------------------------------
                */

                Object.entries(tab.fields || {}).forEach(

                    ([fieldKey, field]) => {

                        if (!this.shouldRenderField(field, node)) {

                            return;

                        }

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

    },

    settingTabStateKey(nodeId, tabKey) {

        return `${nodeId || 'global'}:${tabKey}`;

    },

    isSettingTabOpen(nodeId, tabKey) {

        const key =
            this.settingTabStateKey(
                nodeId,
                tabKey
            );

        if (
            Object.prototype.hasOwnProperty.call(
                this.openSettingTabs,
                key
            )
        ) {
            return this.openSettingTabs[key];
        }

        return false;

    },

    toggleSettingTab(nodeId, tabKey) {

        if (!tabKey) {
            return;
        }

        const key =
            this.settingTabStateKey(
                nodeId,
                tabKey
            );

        const body =
            document.querySelector(
                `[data-settings-tab-body="${CSS.escape(tabKey)}"]`
            );

        if (!body) {
            return;
        }

        const open =
            body.classList.contains('hidden');

        this.openSettingTabs[key] = open;

        body.classList.toggle('hidden', !open);

        const button =
            document.querySelector(
                `[data-action="settings-tab-toggle"][data-settings-tab="${CSS.escape(tabKey)}"]`
            );

        const icon =
            button?.querySelector('svg');

        button?.classList.toggle('bg-[#eff6ff]', open);
        button?.classList.toggle('bg-white', !open);
        icon?.classList.toggle('rotate-180', !open);

    },

    shouldRenderField(field, node) {

        const condition =
            field?.when;

        if (!condition) {
            return true;
        }

        const props =
            BuilderStructureRules.ensurePlainProps(
                node?.props
            );

        const actual =
            props[condition.key];

        if (
            Object.prototype.hasOwnProperty.call(
                condition,
                'is'
            )
        ) {
            return String(actual ?? '') === String(condition.is);
        }

        if (
            Object.prototype.hasOwnProperty.call(
                condition,
                'isNot'
            )
        ) {
            return String(actual ?? '') !== String(condition.isNot);
        }

        if (Array.isArray(condition.in)) {
            return condition.in
                .map(String)
                .includes(String(actual ?? ''));
        }

        return true;

    },

    usesControlTabs(node, schema) {

        return Boolean(
            schema.controlTabs
            || schema.ui?.controlTabs
        );

    },

    controlTabs(tabs = null) {

        const labels =
            Array.isArray(tabs)
                ? tabs
                : ['Default', 'Advanced'];

        return `

            <div class="grid h-10 grid-cols-2 border-b border-gray-200 bg-white text-[13px]">
                <button
                    type="button"
                    class="
                        flex
                        items-center
                        justify-center
                        gap-2
                        border-b-2
                        border-slate-900
                        bg-[#eff6ff]
                        font-medium
                        text-slate-700
                    "
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h12M4 10h12M4 14h12" stroke-linecap="round"></path>
                    </svg>
                    ${BuilderHtmlEscape.html(labels[0] || 'Default')}
                </button>

                <button
                    type="button"
                    class="
                        flex
                        items-center
                        justify-center
                        gap-2
                        border-b-2
                        border-transparent
                        bg-white
                        font-medium
                        text-slate-600
                    "
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="4" y="5" width="12" height="9" rx="1.5"></rect>
                        <path d="M7 16h6" stroke-linecap="round"></path>
                    </svg>
                    ${BuilderHtmlEscape.html(labels[1] || 'Advanced')}
                </button>
            </div>

        `;

    },

    sectionPanel(node, nodeId, type = 'section') {

        const props =
            BuilderStructureRules.ensurePlainProps(
                node.props
            );

        const activeTab =
            this.normalizeSectionControlTab(
                props.sectionControlTab || 'style'
            );

        return `

            ${this.sectionControlTabs(activeTab)}

            <div
                data-section-panel="content"
                class="${activeTab === 'content' ? '' : 'hidden'} bg-gray-50 p-2"
            >
                ${this.sectionAccordion({
                    id: 'content-summary',
                    title: 'Content',
                    open: false,
                    body: `
                        <div class="text-xs leading-5 text-slate-500">
                            ${type === 'container'
                                ? 'Container content is managed through child containers and widgets.'
                                : 'Section content is managed through its child containers and widgets.'}
                        </div>
                        ${type === 'container' ? this.containerRoleControl(nodeId, props) : ''}
                    `
                })}
            </div>

            <div
                data-section-panel="style"
                class="${activeTab === 'style' ? '' : 'hidden'} bg-gray-50 p-2"
            >
                ${this.sectionStyleAccordions(nodeId, props, type)}
            </div>

            <div
                data-section-panel="advanced"
                class="${activeTab === 'advanced' ? '' : 'hidden'} bg-gray-50 p-2"
            >
                ${this.sectionAccordion({
                    id: 'layout',
                    title: 'Layout',
                    open: false,
                    body: this.sectionLayoutAccordions(nodeId, props, type)
                })}
                ${this.sectionAccordion({
                    id: 'custom-style',
                    title: 'Custom Style',
                    open: false,
                    body: `
                        ${this.sectionTextInput(nodeId, 'customClass', 'Custom Class', props.customClass || '')}
                        ${this.sectionTextarea(nodeId, 'customCss', 'Custom CSS', props.customCss || '')}
                    `
                })}
                ${this.sectionAdvancedAccordions(nodeId, props)}
            </div>

        `;

    },

    normalizeSectionControlTab(tab) {

        const normalized = {
            contents: 'content',
            content: 'content',
            style: 'style',
            layout: 'advanced',
            advance: 'advanced',
            advanced: 'advanced'
        }[tab];

        return normalized || 'style';

    },

    sectionControlTabs(activeTab) {

        const currentTab =
            this.normalizeSectionControlTab(activeTab);

        const tabs = [
            [
                'content',
                'Content',
                '<path stroke-linecap="round" d="M5 7h14M5 12h14M5 17h14"></path>'
            ],
            [
                'style',
                'Style',
                '<path stroke-linecap="round" stroke-linejoin="round" d="m14.7 6.3 3 3M5 19l4.6-1.2L18.4 9a2.1 2.1 0 0 0-3-3L6.6 14.8 5 19Zm2.5-10.5 8 8"></path>'
            ],
            [
                'advanced',
                'Advanced',
                '<rect x="4" y="5" width="16" height="11" rx="1.5"></rect><path stroke-linecap="round" d="M9 20h6M12 16v4"></path>'
            ]
        ];

        return `

            <div class="grid h-[39px] grid-cols-3 border-b border-gray-200 bg-white text-[13px]">
                ${tabs.map(([id, label, icon]) => `
                    <button
                        type="button"
                        data-action="section-control-tab"
                        data-section-tab="${id}"
                        class="
                            flex
                            items-center
                            justify-center
                            gap-1.5
                            border-b-2
                            border-r
                            border-gray-100
                            ${currentTab === id ? 'border-b-slate-900 border-r-gray-100 bg-[#eff6ff] text-slate-800' : 'border-b-transparent border-r-gray-100 bg-white text-slate-700'}
                            font-medium
                            transition-colors
                        "
                    >
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            ${icon}
                        </svg>
                        ${label}
                    </button>
                `).join('')}
            </div>

        `;

    },

    sectionLayoutAccordions(nodeId, props, type = 'section') {

        const isContainer =
            type === 'container';

        const parent =
            window.BuilderNodeTraversal?.findParent
                ? BuilderNodeTraversal.findParent(nodeId)
                : null;

        const parentIsGrid =
            parent?.type === 'container'
            && (parent.props?.display || 'block') === 'grid';

        const display =
            props.display || 'block';

        const sizingDefaults =
            isContainer
                ? {
                    maxWidth: '100%',
                    minHeight: 120
                }
                : {
                    maxWidth: 1200,
                    minHeight: 220
                };

        const spacingDefaults =
            isContainer
                ? {
                    paddingTop: 0,
                    paddingBottom: 0,
                    paddingLeft: 0,
                    paddingRight: 0
                }
                : {
                    paddingTop: 60,
                    paddingBottom: 60,
                    paddingLeft: 20,
                    paddingRight: 20
                };

        return `
            ${this.sectionSubheading('Sizing')}
            ${this.sectionNumber(nodeId, 'width', 'Width', props.width ?? '')}
            ${this.sectionTextInput(nodeId, 'maxWidth', 'Max Width', props.maxWidth ?? sizingDefaults.maxWidth)}
            ${this.sectionNumber(nodeId, 'minHeight', 'Min Height', props.minHeight ?? sizingDefaults.minHeight)}
            ${this.sectionNumber(nodeId, 'height', 'Height', props.height ?? '')}

            ${this.sectionSubheading('Margin & Padding')}
            ${this.sectionNumber(nodeId, 'marginTop', 'Margin Top', props.marginTop ?? 0)}
            ${this.sectionNumber(nodeId, 'marginBottom', 'Margin Bottom', props.marginBottom ?? 0)}
            ${this.sectionNumber(nodeId, 'paddingTop', 'Padding Top', props.paddingTop ?? spacingDefaults.paddingTop)}
            ${this.sectionNumber(nodeId, 'paddingBottom', 'Padding Bottom', props.paddingBottom ?? spacingDefaults.paddingBottom)}
            ${this.sectionNumber(nodeId, 'paddingLeft', 'Padding Left', props.paddingLeft ?? spacingDefaults.paddingLeft)}
            ${this.sectionNumber(nodeId, 'paddingRight', 'Padding Right', props.paddingRight ?? spacingDefaults.paddingRight)}

            ${isContainer ? `
                ${this.sectionSubheading('Display')}
                ${this.sectionSelect(nodeId, 'display', 'Display', props.display || 'block', [
                    ['block', 'Block'],
                    ['flex', 'Flex'],
                    ['grid', 'Grid']
                ])}
                ${parentIsGrid ? this.sectionSelect(nodeId, 'gridSpan', 'Grid Span', props.gridSpan || '12', [
                    ['1', '1'],
                    ['2', '2'],
                    ['3', '3'],
                    ['4', '4'],
                    ['5', '5'],
                    ['6', '6'],
                    ['7', '7'],
                    ['8', '8'],
                    ['9', '9'],
                    ['10', '10'],
                    ['11', '11'],
                    ['12', '12']
                ]) : ''}
                ${['flex', 'grid'].includes(display) ? this.sectionNumber(nodeId, 'gap', 'Gap', props.gap ?? 20) : ''}
                ${display === 'flex' ? this.sectionSelect(nodeId, 'flexDirection', 'Direction', props.flexDirection || 'row', [
                    ['row', 'Horizontal'],
                    ['row-reverse', 'Horizontal Reverse'],
                    ['column', 'Vertical'],
                    ['column-reverse', 'Vertical Reverse']
                ]) : ''}
                ${display === 'grid' ? this.sectionNumber(nodeId, 'gridColumns', 'Grid Columns', props.gridColumns ?? 12) : ''}
                ${['flex', 'grid'].includes(display) ? this.sectionSelect(nodeId, 'justifyContent', 'Justify', props.justifyContent || 'flex-start', [
                    ['flex-start', 'Start'],
                    ['center', 'Center'],
                    ['flex-end', 'End'],
                    ['space-between', 'Space Between'],
                    ['space-around', 'Space Around'],
                    ['space-evenly', 'Space Evenly']
                ]) : ''}
                ${['flex', 'grid'].includes(display) ? this.sectionSelect(nodeId, 'alignItems', 'Align', props.alignItems || 'stretch', [
                    ['stretch', 'Stretch'],
                    ['flex-start', 'Start'],
                    ['center', 'Center'],
                    ['flex-end', 'End'],
                    ['baseline', 'Baseline']
                ]) : ''}
            ` : ''}
        `;

    },

    sectionSubheading(title) {

        return `
            <div class="mb-2 mt-1 text-[10.5px] font-semibold uppercase tracking-wide text-slate-400">
                ${BuilderHtmlEscape.html(title)}
            </div>
        `;

    },

    sectionStyleAccordions(nodeId, props, type = 'section') {

        return `
            ${this.sectionAccordion({
                id: 'border',
                title: 'Border',
                open: false,
                body: `
                    ${this.sectionSelect(nodeId, 'borderStyle', 'Border Style', props.borderStyle || 'solid', [
                        ['solid', 'Solid'],
                        ['dashed', 'Dashed'],
                        ['dotted', 'Dotted'],
                        ['none', 'None']
                    ])}
                    ${this.sectionNumber(nodeId, 'borderWidth', 'Border Width', props.borderWidth ?? 0)}
                    ${this.sectionColor(nodeId, 'borderColor', 'Border Color', props.borderColor || '#000000', Object.prototype.hasOwnProperty.call(props, 'borderColor'))}
                `
            })}
            ${this.sectionAccordion({
                id: 'radius',
                title: 'Radius / Rounded Corners',
                open: false,
                body: `${this.sectionNumber(nodeId, 'borderRadius', 'Radius', props.borderRadius ?? 0)}`
            })}
            ${this.sectionAccordion({
                id: 'box-shadow',
                title: 'Box Shadow',
                open: false,
                body: `${this.sectionTextInput(nodeId, 'boxShadow', 'Box Shadow', props.boxShadow || '')}`
            })}
            ${this.sectionAccordion({
                id: 'transform',
                title: 'Transform',
                open: false,
                body: `${this.sectionTextInput(nodeId, 'transform', 'Transform', props.transform || '')}`
            })}
            ${this.sectionBackgroundAccordion(nodeId, props)}
            ${this.sectionAccordion({
                id: 'typography',
                title: 'Fonts & Typography',
                open: false,
                body: `
                    ${this.sectionColor(nodeId, 'textColor', 'Text Color', props.textColor || '#111827', Object.prototype.hasOwnProperty.call(props, 'textColor'))}
                    ${this.sectionNumber(nodeId, 'fontSize', 'Font Size', props.fontSize ?? '')}
                    ${this.sectionSelect(nodeId, 'fontWeight', 'Font Weight', props.fontWeight || '', [
                        ['', 'Default'],
                        ['400', 'Normal'],
                        ['500', 'Medium'],
                        ['600', 'Semibold'],
                        ['700', 'Bold']
                    ])}
                `
            })}
            ${this.sectionAccordion({
                id: 'hover-transition',
                title: 'Hover Transition',
                open: false,
                body: `${this.sectionNumber(nodeId, 'transitionDuration', 'Duration ms', props.transitionDuration ?? 150)}`
            })}
        `;

    },

    sectionAdvancedAccordions(nodeId, props) {

        return `
            ${this.sectionAccordion({
                id: 'overflow',
                title: 'Overflow',
                open: false,
                body: `${this.sectionSelect(nodeId, 'overflow', 'Overflow', props.overflow || 'hidden', [
                    ['visible', 'Visible'],
                    ['hidden', 'Hidden'],
                    ['auto', 'Auto'],
                    ['scroll', 'Scroll']
                ])}`
            })}
            ${this.sectionAccordion({
                id: 'visibility',
                title: 'Visibility',
                open: false,
                body: `
                    ${this.sectionSelect(nodeId, 'visibility', 'Visibility', props.visibility || 'visible', [
                        ['visible', 'Visible'],
                        ['hidden', 'Hidden']
                    ])}
                    ${this.sectionRange(nodeId, 'opacity', 'Opacity', props.opacity ?? 100, 0, 100)}
                `
            })}
            ${this.sectionAccordion({
                id: 'position',
                title: 'Position',
                open: false,
                body: `
                    ${this.sectionSelect(nodeId, 'position', 'Position', props.position || 'relative', [
                        ['static', 'Static'],
                        ['relative', 'Relative'],
                        ['absolute', 'Absolute'],
                        ['sticky', 'Sticky']
                    ])}
                    ${this.sectionNumber(nodeId, 'top', 'Top', props.top ?? '')}
                    ${this.sectionNumber(nodeId, 'left', 'Left', props.left ?? '')}
                `
            })}
            ${this.sectionAccordion({
                id: 'before-after',
                title: 'Before And After',
                open: false,
                body: `
                    ${this.sectionTextarea(nodeId, 'beforeContent', 'Before Content', props.beforeContent || '')}
                    ${this.sectionTextarea(nodeId, 'afterContent', 'After Content', props.afterContent || '')}
                `
            })}
        `;

    },

    containerRoleControl(nodeId, props) {

        const role =
            BuilderContainerRoles.normalize(
                props.containerRole || 'group'
            );

        const status =
            BuilderContainerRoles.presetStatus(
                props
            );

        const statusTone = {
            applied: 'border-emerald-100 bg-emerald-50 text-emerald-800',
            partial: 'border-amber-100 bg-amber-50 text-amber-800',
            available: 'border-blue-100 bg-blue-50 text-blue-800'
        }[status.state] || 'border-slate-100 bg-slate-50 text-slate-700';

        const buttonClass =
            status.disabled
                ? 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-400'
                : 'border-blue-600 bg-blue-600 text-white hover:bg-blue-700';

        return `
            ${this.sectionSelect(
                nodeId,
                'containerRole',
                'Role',
                role,
                BuilderContainerRoles.options()
            )}

            <div class="mt-1 rounded border ${statusTone} p-2.5 text-xs">
                <div class="font-semibold">
                    ${BuilderHtmlEscape.html(status.label)}
                </div>
                <div class="mt-1 leading-5 opacity-80">
                    ${BuilderHtmlEscape.html(status.description)}
                </div>
                <button
                    type="button"
                    data-action="apply-container-role-preset"
                    data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                    ${status.disabled ? 'disabled' : ''}
                    class="mt-2 w-full border px-2 py-1.5 text-xs font-semibold transition-colors ${buttonClass}"
                >
                    ${BuilderHtmlEscape.html(status.buttonLabel)}
                </button>
            </div>
        `;

    },

    sectionBackgroundAccordion(nodeId, props) {

        const mode =
            props.backgroundMode || 'color';

        return this.sectionAccordion({
            id: 'background',
            title: 'Background',
            open: false,
            body: `
                <div class="mb-4 grid grid-cols-2 border-b border-gray-100 text-xs">
                    <button type="button" class="border-b-2 border-blue-600 py-2 text-blue-700">Normal</button>
                    <button type="button" class="border-b-2 border-transparent py-2 text-slate-600">Hover</button>
                </div>

                <div class="mb-4">
                    <div class="mb-2 text-xs font-medium text-slate-600">Background Style</div>
                    <div class="grid grid-cols-4 overflow-hidden border border-gray-200 bg-white text-xs">
                        ${['color', 'image', 'video', 'none'].map(item => `
                            <button
                                type="button"
                                data-action="section-background-mode"
                                data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                                data-background-mode="${item}"
                                class="
                                    border-r
                                    border-gray-200
                                    px-2
                                    py-2
                                    capitalize
                                    ${mode === item ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-50'}
                                "
                            >
                                ${item}
                            </button>
                        `).join('')}
                    </div>
                </div>

                ${mode === 'image' ? this.sectionTextInput(nodeId, 'backgroundImage', 'Image URL', props.backgroundImage || '') : ''}
                ${mode === 'video' ? this.sectionTextInput(nodeId, 'backgroundVideo', 'Video URL', props.backgroundVideo || '') : ''}
                ${mode === 'none' ? '<div class="text-xs text-slate-400">No background selected.</div>' : ''}
                ${mode === 'color' ? this.sectionColor(nodeId, 'backgroundColor', 'Background Color', props.backgroundColor || '#ffffff', Object.prototype.hasOwnProperty.call(props, 'backgroundColor')) : ''}
            `
        });

    },

    sectionAccordion({ id, title, body, open = false }) {

        return `

            <div class="mb-1 border border-gray-50 bg-white shadow-[0_1px_5px_rgba(15,23,42,0.05)]" data-section-accordion-wrapper="${id}">
                <button
                    type="button"
                    data-action="section-accordion-toggle"
                    data-section-accordion="${id}"
                    class="
                        flex
                        h-[37px]
                        w-full
                        items-center
                        justify-between
                        px-3
                        text-left
                        text-[12.5px]
                        font-semibold
                        uppercase
                        tracking-normal
                        text-slate-800
                        hover:bg-slate-50
                        ${open ? 'bg-[#eff6ff]' : 'bg-white'}
                    "
                >
                    <span>${BuilderHtmlEscape.html(title)}</span>
                    <svg
                        class="h-4 w-4 text-slate-900 transition-transform ${open ? '' : 'rotate-180'}"
                        viewBox="0 0 20 20"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M5 12l5-5 5 5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>

                <div
                    data-section-accordion-body="${id}"
                    class="${open ? '' : 'hidden'} border-t border-gray-100 bg-white p-3.5"
                >
                    ${body}
                </div>
            </div>

        `;

    },

    sectionTextInput(nodeId, key, label, value) {

        return this.sectionField(label, `
            <input
                type="text"
                value="${BuilderHtmlEscape.attribute(value)}"
                data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                data-setting-field="${BuilderHtmlEscape.attribute(key)}"
                class="w-full border border-gray-200 px-2 py-1.5 text-xs outline-none focus:border-blue-400"
            />
        `);

    },

    sectionTextarea(nodeId, key, label, value) {

        return this.sectionField(label, `
            <textarea
                data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                data-setting-field="${BuilderHtmlEscape.attribute(key)}"
                class="min-h-[80px] w-full border border-gray-200 px-2 py-1.5 text-xs outline-none focus:border-blue-400"
            >${BuilderHtmlEscape.html(value)}</textarea>
        `);

    },

    sectionNumber(nodeId, key, label, value) {

        return this.sectionField(label, `
            <input
                type="number"
                value="${BuilderHtmlEscape.attribute(value)}"
                data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                data-setting-field="${BuilderHtmlEscape.attribute(key)}"
                class="w-full border border-gray-200 px-2 py-1.5 text-xs outline-none focus:border-blue-400"
            />
        `);

    },

    sectionRange(nodeId, key, label, value, min, max) {

        return this.sectionField(label, `
            <input
                type="range"
                min="${min}"
                max="${max}"
                value="${BuilderHtmlEscape.attribute(value)}"
                data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                data-setting-field="${BuilderHtmlEscape.attribute(key)}"
                class="w-full"
            />
        `);

    },

    sectionColor(nodeId, key, label, value, hasLocalValue = true) {

        return this.sectionField(label, `
            <div class="flex flex-col items-end gap-1.5">
                <input
                    type="color"
                    value="${BuilderHtmlEscape.attribute(value)}"
                    data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                    data-setting-field="${BuilderHtmlEscape.attribute(key)}"
                    class="h-7 w-20 border border-gray-300 bg-white"
                />
                ${hasLocalValue ? `
                    <button
                        type="button"
                        data-action="use-theme-default"
                        data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                        data-setting-field="${BuilderHtmlEscape.attribute(key)}"
                        class="text-[10px] font-medium text-blue-600 hover:text-blue-700"
                    >
                        Use theme default
                    </button>
                ` : ''}
            </div>
        `);

    },

    sectionSelect(nodeId, key, label, value, options) {

        return this.sectionField(label, `
            <select
                data-target-node-id="${BuilderHtmlEscape.attribute(nodeId)}"
                data-setting-field="${BuilderHtmlEscape.attribute(key)}"
                class="w-full border border-gray-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-blue-400"
            >
                ${options.map(([optionValue, optionLabel]) => `
                    <option
                        value="${BuilderHtmlEscape.attribute(optionValue)}"
                        ${String(value) === String(optionValue) ? 'selected' : ''}
                    >
                        ${BuilderHtmlEscape.html(optionLabel)}
                    </option>
                `).join('')}
            </select>
        `);

    },

    sectionField(label, input) {

        return `
            <label class="mb-3 grid grid-cols-[1fr_92px] items-center gap-3 text-xs text-slate-600">
                <span>${BuilderHtmlEscape.html(label)}</span>
                ${input}
            </label>
        `;

    },

    activateSectionTab(tab) {

        const normalizedTab =
            this.normalizeSectionControlTab(tab);

        const selectedNodeId =
            BuilderStore.selectedNodeId;

        if (selectedNodeId) {

            BuilderNodes.updateProps(
                selectedNodeId,
                'sectionControlTab',
                normalizedTab
            );

        }

        document
            .querySelectorAll('[data-section-panel]')
            .forEach(panel => {
                panel.classList.toggle(
                    'hidden',
                    panel.dataset.sectionPanel !== normalizedTab
                );
            });

        document
            .querySelectorAll('[data-section-tab]')
            .forEach(button => {
                const active =
                    button.dataset.sectionTab === normalizedTab;

                button.classList.toggle('border-b-slate-900', active);
                button.classList.toggle('bg-[#eff6ff]', active);
                button.classList.toggle('text-slate-800', active);
                button.classList.toggle('border-b-transparent', !active);
                button.classList.toggle('bg-white', !active);
                button.classList.toggle('text-slate-600', !active);
                button.classList.toggle('text-slate-700', !active);
            });

    },

    toggleAccordion(id) {

        const body =
            document.querySelector(
                `[data-section-accordion-body="${CSS.escape(id)}"]`
            );

        const wrapper =
            document.querySelector(
                `[data-section-accordion-wrapper="${CSS.escape(id)}"]`
            );

        if (!body || !wrapper) {
            return;
        }

        body.classList.toggle('hidden');

        const button =
            wrapper.querySelector('[data-action="section-accordion-toggle"]');

        const icon =
            button?.querySelector('svg');

        const open =
            !body.classList.contains('hidden');

        button?.classList.toggle('bg-[#eff6ff]', open);
        button?.classList.toggle('bg-white', !open);
        icon?.classList.toggle('rotate-180', !open);

    },

    setSectionBackgroundMode(target) {

        const nodeId =
            target.dataset.targetNodeId;

        const mode =
            target.dataset.backgroundMode;

        if (!nodeId || !mode) {
            return;
        }

        BuilderSettingsUpdater.updateField(
            nodeId,
            'backgroundMode',
            mode
        );

        const node =
            Builder.findNodeById(
                nodeId
            );

        if (node) {

            node.props =
                BuilderStructureRules.ensurePlainProps(
                    node.props
                );

            node.props.sectionControlTab = 'style';

            const schema =
                BuilderSchema.get(node.type) || {};

            this.render(
                node,
                schema,
                nodeId
            );

        }

    }

};
