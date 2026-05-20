window.BuilderInteractionBoundaries = {

    /*
    |--------------------------------------------------------------------------
    | Builder Interaction Surfaces
    |--------------------------------------------------------------------------
    |
    | These selectors define the current builder UI boundaries used by
    | outside-click handling and interaction diagnostics.
    |
    | Accepted surfaces:
    | - canvas: rendered builder document (#canvas)
    | - overlay: canvas overlay toolbar/root (#builder-overlay-root)
    | - left sidebar: widgets/settings/page controls (#left-panel)
    | - right sidebar: layers tree (#right-panel, #layers-panel)
    | - topbar: viewport/history/save controls (#builder-topbar)
    | - floating UI: future popovers/toolbars marked with builder floating
    |   selectors below
    |
    */

    selectors: {

        node:
            '[data-node-id]',

        canvas:
            '#canvas',

        overlay:
            '#builder-overlay-root',

        leftSidebar:
            '#left-panel',

        rightSidebar:
            '#right-panel',

        topbar:
            '#builder-topbar',

        settings:
            '#settings-panel',

        layers:
            '#layers-panel',

        floating:
            [
                '[data-builder-floating-ui]',
                '[data-floating-ui]',
                '.builder-floating-ui'
            ].join(',')

    },

    controlSelector: [
        'input',
        'textarea',
        'select',
        'button',
        'a',
        'label',
        '[contenteditable="true"]'
    ].join(','),

    /*
    |--------------------------------------------------------------------------
    | Element Normalization
    |--------------------------------------------------------------------------
    */

    element(target) {

        if (!target) {
            return null;
        }

        if (target.nodeType === Node.ELEMENT_NODE) {
            return target;
        }

        return target.parentElement || null;

    },

    closest(target, selector) {

        const element =
            this.element(target);

        if (!element) {
            return null;
        }

        return element.closest(selector);

    },

    /*
    |--------------------------------------------------------------------------
    | Surface Checks
    |--------------------------------------------------------------------------
    */

    isNode(target) {

        return !!this.closest(
            target,
            this.selectors.node
        );

    },

    isCanvas(target) {

        return !!this.closest(
            target,
            this.selectors.canvas
        );

    },

    isOverlay(target) {

        return !!this.closest(
            target,
            this.selectors.overlay
        );

    },

    isBuilderUi(target) {

        return !!this.closest(
            target,
            [
                this.selectors.leftSidebar,
                this.selectors.rightSidebar,
                this.selectors.topbar,
                this.selectors.settings,
                this.selectors.layers,
                this.selectors.floating
            ].join(',')
        );

    },

    isControl(target) {

        return !!this.closest(
            target,
            this.controlSelector
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Outside-Click Policies
    |--------------------------------------------------------------------------
    */

    preservesSelection(target) {

        return (
            this.isNode(target)
            ||
            this.isOverlay(target)
            ||
            this.isBuilderUi(target)
            ||
            this.isControl(target)
        );

    },

    preservesOverlay(target) {

        return (
            this.isCanvas(target)
            ||
            this.isOverlay(target)
            ||
            this.isBuilderUi(target)
            ||
            this.isControl(target)
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Diagnostics
    |--------------------------------------------------------------------------
    */

    surface(target) {

        if (this.isNode(target)) {
            return 'canvas-node';
        }

        if (this.isCanvas(target)) {
            return 'canvas';
        }

        if (this.isOverlay(target)) {
            return 'overlay';
        }

        if (
            this.closest(
                target,
                this.selectors.leftSidebar
            )
        ) {
            return 'left-sidebar';
        }

        if (
            this.closest(
                target,
                this.selectors.rightSidebar
            )
        ) {
            return 'right-sidebar';
        }

        if (
            this.closest(
                target,
                this.selectors.topbar
            )
        ) {
            return 'topbar';
        }

        if (
            this.closest(
                target,
                this.selectors.floating
            )
        ) {
            return 'floating-ui';
        }

        if (this.isControl(target)) {
            return 'control';
        }

        return 'outside';

    }

};
