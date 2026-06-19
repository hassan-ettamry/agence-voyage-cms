window.BuilderCanvasHover = {

    /*
    |--------------------------------------------------------------------------
    | Bind Hover Events
    |--------------------------------------------------------------------------
    */

    bind(canvas) {

        if (!canvas) return;

        const hoverStyle = (mode, outlineOptions = {}) => ({
            outline: BuilderOverlayTheme.outline(
                mode,
                outlineOptions
            ),
            outlineOffset: BuilderOverlayTheme.outlineOffset(
                outlineOptions.offset
                ??
                -2
            ),
            cursor: 'pointer'
        });

        let hoveredNodeId = null;

        const clearHoverStyles = () => {

            canvas
                .querySelectorAll('[data-node-id]')
                .forEach(node => {

                    if (node.dataset.nodeId === BuilderStore.selectedNodeId) {
                        return;
                    }

                    delete node.dataset.builderHoverState;

                    node.style.outline = '';
                    node.style.outlineOffset = '';
                    node.style.cursor = '';

                });

        };

        const applyHoverStyle = (element, style, state, options = {}) => {

            if (!element) return;

            if (
                !options.force
                &&
                element.dataset.nodeId === BuilderStore.selectedNodeId
            ) {
                return;
            }

            element.dataset.builderHoverState = state;
            element.style.outline = style.outline;
            element.style.outlineOffset = style.outlineOffset;
            element.style.cursor = style.cursor;

        };

        const elementForNodeId = (nodeId) => {

            if (!nodeId) {
                return null;
            }

            return canvas.querySelector(
                `[data-node-id="${CSS.escape(nodeId)}"]`
            );

        };

        const syncHover = (event) => {

            const element =

                BuilderCanvasUtils
                    .getNodeElement(
                        event.target
                    );

            if (!element) return;

            if (element.dataset.nodeId === hoveredNodeId) {
                return;
            }

            hoveredNodeId =
                element.dataset.nodeId;

            clearHoverStyles();

            const ancestors =
                BuilderNodeTraversal.findAncestors(
                    element.dataset.nodeId
                )
                ||
                [];

            ancestors.forEach((ancestor, index) => {

                const ancestorElement =
                    elementForNodeId(
                        ancestor.id
                    );

                const isImmediateParent =
                    index === ancestors.length - 1;

                const distanceFromSelected =
                    ancestors.length - index;

                applyHoverStyle(
                    ancestorElement,
                    isImmediateParent
                        ? hoverStyle(
                            'parent',
                            BuilderOverlayTheme.pathOutlineOptions(
                                distanceFromSelected
                            )
                        )
                        : hoverStyle(
                            'ancestor',
                            BuilderOverlayTheme.pathOutlineOptions(
                                distanceFromSelected
                            )
                        ),
                    isImmediateParent
                        ? 'parent'
                        : 'ancestor',
                    {
                        force: true
                    }
                );

            });

            BuilderNodeTraversal
                .directChildrenByType(
                    element.dataset.nodeId,
                    'container'
                )
                .forEach(child => {

                    applyHoverStyle(
                        elementForNodeId(child.id),
                        hoverStyle(
                            'child',
                            BuilderOverlayTheme.childOutlineOptions()
                        ),
                        'child',
                        {
                            force: true
                        }
                    );

                });

            applyHoverStyle(
                element,
                hoverStyle(
                    'active',
                    BuilderOverlayTheme.pathOutlineOptions(0)
                ),
                'active'
            );

            const overlayItems = [];

            const parentElement =
                ancestors.length
                    ? elementForNodeId(
                        ancestors[ancestors.length - 1].id
                    )
                    : null;

            if (
                parentElement
                &&
                parentElement.dataset.nodeId !== element.dataset.nodeId
            ) {

                overlayItems.push({
                    element: parentElement,
                    nodeId: parentElement.dataset.nodeId,
                    mode: 'parent-hover',
                    align: 'left',
                    height: 24
                });

            }

            overlayItems.push({
                element,
                nodeId: element.dataset.nodeId,
                mode:
                    element.dataset.nodeId === BuilderStore.selectedNodeId
                        ? 'selected'
                        : 'hover',
                align: 'center',
                height: 24
            });

            BuilderOverlay.showGroup(
                overlayItems
            );

        };

        /*
        |--------------------------------------------------------------------------
        | Mouse Over
        |--------------------------------------------------------------------------
        */

        canvas.addEventListener(

            'mouseover',

            (event) => {

                syncHover(event);

            }

        );

        canvas.addEventListener(

            'mousemove',

            (event) => {

                syncHover(event);

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Mouse Out
        |--------------------------------------------------------------------------
        */

        canvas.addEventListener(

            'mouseout',

            (event) => {

                const element =

                    BuilderCanvasUtils
                        .getNodeElement(
                            event.target
                        );

                if (!element) return;

                if (
                    element.contains(event.relatedTarget)
                    ||
                    event.relatedTarget?.closest?.(
                        '[data-builder-overlay-toolbar]'
                    )
                ) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Ignore Selected
                |--------------------------------------------------------------------------
                */

                if (

                    element.dataset.nodeId
                    ===
                    BuilderStore.selectedNodeId

                ) {

                    return;

                }

                /*
                |--------------------------------------------------------------------------
                | Remove Hover
                |--------------------------------------------------------------------------
                */

                hoveredNodeId = null;

                clearHoverStyles();

                if (BuilderStore.selectedNodeId) {

                    const selected =
                        BuilderCanvasUtils.getCanvas()
                            ?.querySelector(
                                `[data-node-id="${CSS.escape(BuilderStore.selectedNodeId)}"]`
                            );

                    if (selected) {

                        BuilderSelectionManager.restoreVisuals();

                        return;

                    }

                }

                BuilderOverlay.hide({
                    hoverOnly: true
                });

            }

        );

    }

};
