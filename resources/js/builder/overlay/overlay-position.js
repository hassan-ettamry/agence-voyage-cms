window.BuilderOverlayPosition = {
    // Pure client-rectangle geometry: separate measurement from placement for testing.
    calculateGeometry(rect, bounds, wrapperRect, toolbarSize, options = {}) {
        const margin = options.margin ?? 8;
        const gap = options.gap ?? 8;
        const maxWidth = Math.max(0, bounds.right - bounds.left - margin * 2);
        const maxHeight = Math.max(0, bounds.bottom - bounds.top - margin * 2);
        const visible = rect.right > rect.left && rect.bottom > rect.top
            && rect.right > bounds.left && rect.left < bounds.right
            && rect.bottom > bounds.top && rect.top < bounds.bottom
            && maxWidth > 0 && maxHeight > 0;

        if (!visible) return { visible: false, maxWidth };

        const width = Math.min(toolbarSize.width, maxWidth);
        const height = Math.min(toolbarSize.height, maxHeight);
        const clamp = (value, minimum, maximum) => Math.min(Math.max(value, minimum), maximum);
        const left = clamp(rect.left, bounds.left + margin, bounds.right - margin - width);
        const above = rect.top - toolbarSize.height - gap;
        const top = clamp(
            above >= bounds.top + margin ? above : rect.top + gap,
            bounds.top + margin,
            bounds.bottom - margin - height
        );

        return {
            visible: true,
            left: left - wrapperRect.left,
            top: top - wrapperRect.top,
            maxWidth,
            maxHeight,
            compact: maxWidth < 400
        };
    },

    visibleBounds(wrapper, workspace) {
        const rect = wrapper.getBoundingClientRect();
        const viewportWidth = document.documentElement.clientWidth;
        const viewportHeight = document.documentElement.clientHeight;
        const workspaceRect = workspace?.getBoundingClientRect() || {
            left: 0, top: 0, right: viewportWidth, bottom: viewportHeight
        };
        const left = workspaceRect.left + (workspace?.clientLeft || 0);
        const top = workspaceRect.top + (workspace?.clientTop || 0);
        const right = workspace ? left + workspace.clientWidth : workspaceRect.right;
        const bottom = workspace ? top + workspace.clientHeight : workspaceRect.bottom;
        return {
            left: Math.max(rect.left, left, 0),
            right: Math.min(rect.right, right, viewportWidth),
            top: Math.max(top, 0),
            bottom: Math.min(bottom, viewportHeight)
        };
    },

    calculate(element, options = {}) {
        const wrapper = BuilderOverlayElements.getWrapper();
        if (!element || !wrapper) return { visible: false };
        const workspace = document.getElementById('builder-workspace');
        const bounds = options.bounds || this.visibleBounds(wrapper, workspace);
        return this.calculateGeometry(
            element.getBoundingClientRect(), bounds, wrapper.getBoundingClientRect(),
            options.toolbarSize || { width: 320, height: 40 }, options
        );
    }
};
