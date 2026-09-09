window.BuilderCanvas = {

    /*
    |--------------------------------------------------------------------------
    | Render Version
    |--------------------------------------------------------------------------
    */

    renderVersion: 0,

    lastRenderError: null,

    /*
    |--------------------------------------------------------------------------
    | Invalidate Current Render
    |--------------------------------------------------------------------------
    */

    invalidateCurrentRender() {

        this.renderVersion++;

    },

    /*
    |--------------------------------------------------------------------------
    | Render Feedback
    |--------------------------------------------------------------------------
    */

    showRenderFeedback(error = {}) {

        const feedback =
            document.getElementById(
                'builder-render-feedback'
            );

        if (!feedback) {
            return;
        }

        const status =
            Number(error?.status) || null;

        const sessionExpired =
            status === 401
            || status === 419;

        const message =
            feedback.querySelector(
                '[data-builder-render-message]'
            );

        const retryButton =
            feedback.querySelector(
                '[data-action="retry-canvas-render"]'
            );

        const reloadButton =
            feedback.querySelector(
                '[data-action="reload-builder"]'
            );

        if (message) {

            message.textContent = sessionExpired
                ? 'Your session has expired. Reload the page to continue.'
                : 'The canvas could not be updated. Your changes are still preserved.';

        }

        retryButton?.classList.toggle(
            'hidden',
            sessionExpired
        );

        reloadButton?.classList.toggle(
            'hidden',
            !sessionExpired
        );

        feedback.dataset.renderStatus =
            status ? String(status) : 'network';

        feedback.classList.remove('hidden');
        feedback.classList.add('flex');

    },

    clearRenderFeedback() {

        this.lastRenderError = null;

        const feedback =
            document.getElementById(
                'builder-render-feedback'
            );

        if (!feedback) {
            return;
        }

        feedback.classList.add('hidden');
        feedback.classList.remove('flex');
        delete feedback.dataset.renderStatus;

    },

    async retryRender() {

        const rendered =
            await BuilderRenderManager.requestRender(
                'canvas.retry',
                this.lastRenderError?.status || null
            );

        if (!rendered) {
            return false;
        }

        return this.revealNode(
            BuilderStore.selectedNodeId,
            {
                source: 'canvas.retry',
                scroll: true
            }
        );

    },

    revealNode(nodeId, options = {}) {

        if (!nodeId) {
            return false;
        }

        const element =
            document.querySelector(
                `#canvas [data-node-id="${CSS.escape(nodeId)}"]`
            );

        if (!element) {
            return false;
        }

        if (window.BuilderSelectionManager) {

            return BuilderSelectionManager.select(
                nodeId,
                element,
                options
            );

        }

        BuilderStore.setSelection(
            nodeId,
            element
        );

        BuilderCanvasUtils.applySelectionStyles(
            element
        );

        BuilderOverlay.show(
            element,
            nodeId
        );

        if (options.scroll) {

            element.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        }

        return true;

    },

    /*
    |--------------------------------------------------------------------------
    | Render Canvas
    |--------------------------------------------------------------------------
    */

    async render(source = 'direct', reason = null) {

        const version =
            ++this.renderVersion;

        const startedAt =
            performance.now();

        const context = {
            version,
            source,
            reason,
            startedAt
        };

        BuilderEventBus.emitSafe(
            BuilderEvents.CANVAS_RENDER_STARTED,
            context
        );

        if (BuilderLogger.shouldLog('render')) {

            BuilderLogger.group(
                `RENDER ${version}`,
                BuilderLogger.colors.render
            );

            BuilderLogger.log(
                'SOURCE',
                source
            );

            if (reason) {

                BuilderLogger.log(
                    'REASON',
                    reason
                );

            }
        
        }

        const canvas =
            BuilderCanvasUtils.getCanvas();

        if (!canvas) {

            BuilderLogger.warn(
                'Canvas not found'
            );

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDER_FAILED,
                {
                    ...context,
                    error: 'canvas_not_found',
                    duration: performance.now() - startedAt
                }
            );

            return false;

        }

        const structure =
            BuilderStore.getStructure();

        if (BuilderLogger.shouldLog('render')) {

            BuilderLogger.log(
                'STRUCTURE BEFORE RENDER',
                structuredClone(
                    structure
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        if (!structure.length) {

            canvas.innerHTML = `

                ${this.menuShellHtml()}

                <div
                    id="canvas-empty-state"
                    class="
                        flex
                        min-h-[300px]
                        items-center
                        justify-center
                        py-20
                    "
                >

                    <div class="
                        flex
                        items-center
                        gap-3
                    ">

                        <button
                            type="button"
                            data-action="open-template-library"
                            class="
                                inline-flex
                                items-center
                                gap-2
                                border
                                border-slate-700
                                bg-white
                                px-5
                                py-2.5
                                text-sm
                                font-semibold
                                text-slate-900
                                hover:bg-slate-50
                            "
                        >
                            <span class="
                                flex
                                h-4
                                w-4
                                items-center
                                justify-center
                                rounded-full
                                bg-slate-950
                                text-[11px]
                                leading-none
                                text-white
                            ">+</span>
                            Add Section
                        </button>

                        <button
                            type="button"
                            data-action="open-template-library"
                            class="
                                hidden
                                items-center
                                gap-2
                                border
                                border-slate-700
                                bg-white
                                px-5
                                py-2.5
                                text-sm
                                font-semibold
                                text-slate-900
                                hover:bg-slate-50
                            "
                        >
                            <span class="text-base leading-none">▣</span>
                            Choose Block
                        </button>

                    </div>

                </div>

            `;

            this.clearStaleSelection();
            this.clearRenderFeedback();

            BuilderLogger.success(
                `RENDER ${version} COMPLETE`
            );

            if (BuilderLogger.shouldLog('render')) {

                BuilderLogger.log(
                    'DURATION',
                    `${(performance.now() - startedAt).toFixed(2)}ms`
                );

            }

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDERED,
                {
                    ...context,
                    empty: true,
                    duration: performance.now() - startedAt
                }
            );

            return true;

        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Request HTML
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(

                    '/builder/render',

                    {

                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':

                                document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .content

                        },

                        body: JSON.stringify({

                            mode: 'editor',

                            structure:
                                structuredClone(
                                    structure
                                )

                        })

                    }

                );

            if (!response.ok) {

                const renderError = new Error(
                    `Builder render failed with status ${response.status}`
                );

                renderError.status = response.status;

                throw renderError;

            }

            /*
            |--------------------------------------------------------------------------
            | HTML
            |--------------------------------------------------------------------------
            */

            const html =
                await response.text();

            BuilderLogger.success(
                'HTML RECEIVED'
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent Old Render
            |--------------------------------------------------------------------------
            */

            if (
                version !== this.renderVersion
            ) {

                BuilderLogger.warn(
                    'OLD RENDER CANCELLED',
                    version
                );

                BuilderLogger.end();

                BuilderEventBus.emitSafe(
                    BuilderEvents.CANVAS_RENDER_CANCELLED,
                    {
                        ...context,
                        duration: performance.now() - startedAt
                    }
                );

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | Inject
            |--------------------------------------------------------------------------
            */

            canvas.innerHTML =
                this.menuShellHtml()
                +
                html
                +
                this.canvasActionButtonsHtml(true);

            /*
            |--------------------------------------------------------------------------
            | Bind Events
            |--------------------------------------------------------------------------
            */

            BuilderCanvasEvents.bind();
            this.disablePreviewLinks(canvas);

            /*
            |--------------------------------------------------------------------------
            | Restore Selection
            |--------------------------------------------------------------------------
            */

            this.restoreSelection();
            this.clearRenderFeedback();

            BuilderLogger.success(
                `RENDER ${version} COMPLETE`
            );

            if (BuilderLogger.shouldLog('render')) {

                BuilderLogger.log(
                    'DURATION',
                    `${(performance.now() - startedAt).toFixed(2)}ms`
                );

            }

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDERED,
                {
                    ...context,
                    empty: false,
                    duration: performance.now() - startedAt
                }
            );

            return true;

        } catch (error) {

            this.lastRenderError = {
                status: Number(error?.status) || null,
                message: error?.message || String(error)
            };

            this.showRenderFeedback(
                this.lastRenderError
            );

            console.error(
                'Canvas render error:',
                error
            );

            BuilderLogger.error(
                error
            );

            if (BuilderLogger.shouldLog('render')) {

                BuilderLogger.log(
                    'FAILED AFTER',
                    `${(performance.now() - startedAt).toFixed(2)}ms`
                );

            }

            BuilderLogger.end();

            BuilderEventBus.emitSafe(
                BuilderEvents.CANVAS_RENDER_FAILED,
                {
                    ...context,
                    error: error?.message || String(error),
                    status: Number(error?.status) || null,
                    duration: performance.now() - startedAt
                }
            );

            return false;

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Restore Selection
    |--------------------------------------------------------------------------
    */

    restoreSelection() {

        if (!BuilderStore.selectedNodeId) {
            return;
        }

        const selected =
            document.querySelector(

                `[data-node-id="${CSS.escape(BuilderStore.selectedNodeId)}"]`

            );

        if (!selected) {

            this.clearStaleSelection();

            return;

        }

        if (!document.body.contains(selected)) {

            BuilderLogger.warn(
                'INVALID SELECTION RESTORE'
            );

            this.clearStaleSelection();

            return;
        }

        BuilderStore.selectedElement =
            selected;

        if (window.BuilderSelectionManager) {

            BuilderSelectionManager.restoreVisuals();

            return;

        }

        BuilderCanvasUtils
            .applySelectionStyles(
                selected
            );

        BuilderOverlay.show(

            selected,
            BuilderStore.selectedNodeId

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Canvas Action Buttons
    |--------------------------------------------------------------------------
    */

    canvasActionButtonsHtml(compact = false) {

        return `

            <div
                data-builder-canvas-actions="true"
                class="
                    flex
                    items-center
                    justify-center
                    ${compact ? 'py-10' : ''}
                "
            >

                <button
                    type="button"
                    data-action="open-template-library"
                    class="
                        inline-flex
                        items-center
                        gap-2
                        border
                        border-slate-700
                        bg-white
                        px-5
                        py-2.5
                        text-sm
                        font-semibold
                        text-slate-900
                        hover:bg-slate-50
                    "
                >
                    <span class="text-base leading-none">+</span>
                    ${compact ? 'Add new section' : 'Add Section'}
                </button>

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Disable Preview Links In Editor
    |--------------------------------------------------------------------------
    */

    disablePreviewLinks(canvas) {

        canvas
            .querySelectorAll('a[href]')
            .forEach(link => {

                link.dataset.builderDisabledLink = 'true';
                link.setAttribute('tabindex', '-1');

                link.addEventListener(
                    'click',
                    event => {

                        event.preventDefault();

                    },
                    true
                );

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Menu Shell Preview
    |--------------------------------------------------------------------------
    */

    menuShellHtml() {

        const items =
            Array.isArray(window.builderMenuItems)
                ? window.builderMenuItems
                : [];

        if (!items.length) {
            return '';
        }

        const links =
            items.map(item => {

                const title =
                    BuilderHtmlEscape.html(item?.title || 'Menu item');

                const url =
                    BuilderHtmlEscape.attribute(item?.url || '#');

                return `

                    <a
                        href="${url}"
                        onclick="return false"
                        class="
                            rounded-full
                            px-3
                            py-1.5
                            text-sm
                            font-medium
                            text-slate-600
                            hover:bg-slate-100
                            hover:text-slate-950
                        "
                    >
                        ${title}
                    </a>

                `;

            }).join('');

        return `

            <header
                data-builder-shell="menu"
                class="
                    relative
                    z-0
                    border-b
                    border-slate-200
                    bg-white
                    px-6
                    py-3
                "
            >

                <div class="
                    mx-auto
                    flex
                    max-w-6xl
                    items-center
                    justify-between
                    gap-4
                ">

                    <div class="
                        text-sm
                        font-semibold
                        text-slate-950
                    ">
                        Navigation
                    </div>

                    <nav class="
                        flex
                        flex-wrap
                        items-center
                        justify-end
                        gap-1
                    ">
                        ${links}
                    </nav>

                </div>

            </header>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Stale Selection
    |--------------------------------------------------------------------------
    */

    clearStaleSelection() {

        if (window.BuilderSelectionManager) {

            BuilderSelectionManager.clear({
                settings: true
            });

            return;

        }

        BuilderStore.clearSelection();
        BuilderOverlay.hide();

        const panel =
            document.getElementById(
                'settings-panel'
            );

        if (panel) {

            panel.innerHTML = '';

        }

    }

};
