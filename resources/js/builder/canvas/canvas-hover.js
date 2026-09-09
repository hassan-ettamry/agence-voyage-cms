window.BuilderCanvasHover = {

    bind(canvas) {

        if (!canvas || canvas.dataset.builderHoverBound) return;

        canvas.dataset.builderHoverBound = 'true';

        let hoveredElement = null;

        const clearHover = () => {

            if (hoveredElement) {
                delete hoveredElement.dataset.builderHoverState;
                hoveredElement = null;
            }

        };

        const syncHover = (event) => {

            if (
                window.BuilderDragState?.isDragging?.()
                || window.BuilderDragState?.componentType
            ) {
                clearHover();
                return;
            }

            const element = BuilderCanvasUtils.getNodeElement(event.target);

            if (!element || !canvas.contains(element)) {
                clearHover();
                return;
            }

            if (element !== hoveredElement) {
                clearHover();
                hoveredElement = element;
            }

            // Hover never changes selection or the toolbar's action target.
            if (element.dataset.nodeId === BuilderStore.selectedNodeId) {
                delete element.dataset.builderHoverState;
                return;
            }

            element.dataset.builderHoverState = 'hover';

        };

        canvas.addEventListener('mouseover', syncHover);
        canvas.addEventListener('mousemove', syncHover);
        canvas.addEventListener('mouseleave', clearHover);
        canvas.addEventListener('dragstart', clearHover);

    }

};
