window.BuilderOverlay = {
    currentElement: null,
    currentNodeId: null,
    mode: null,
    currentItems: [],
    signature: null,
    frame: null,
    initialized: false,
    observer: null,

    init() {
        if (this.initialized) return;
        this.initialized = true;
        this.schedulePosition = this.schedulePosition.bind(this);
        window.addEventListener('resize', this.schedulePosition);
        // Capture also receives scrolling from nested scrollable components.
        document.addEventListener('scroll', this.schedulePosition, true);
        if (window.ResizeObserver) {
            this.observer = new ResizeObserver(this.schedulePosition);
        }
    },

    labelFor(node) {
        const label = window.BuilderSidebar?.componentLabel(node.type) || node.type || 'Component';
        if (node.type === 'container' && node.props?.containerRole && window.BuilderContainerRoles) {
            return `${label}: ${BuilderContainerRoles.label(node.props.containerRole)}`;
        }
        return label;
    },

    show(element, nodeId, options = {}) {
        this.init();
        const selectedId = window.BuilderStore?.selectedNodeId;
        if (!selectedId) {
            this.hide();
            return;
        }
        // Legacy hover callers cannot change the target of the selected toolbar.
        const target = selectedId === nodeId && element?.isConnected
            ? element : BuilderOverlayElements.getElement(selectedId);
        const root = BuilderOverlayElements.getRoot();
        const capabilities = window.BuilderOverlayActions?.capabilities(selectedId);
        if (!target || !root || !capabilities?.node) {
            this.hide();
            return;
        }

        const label = this.labelFor(capabilities.node);
        const signature = JSON.stringify([
            selectedId, label, capabilities.parent?.id,
            capabilities.canMoveUp, capabilities.canMoveDown, capabilities.canDrag,
            capabilities.canDelete, capabilities.canDuplicate,
            BuilderOverlayUI.canSpan(capabilities), capabilities.node.props?.gridSpan
        ]);
        const targetChanged = this.currentElement !== target;
        this.currentElement = target;
        this.currentNodeId = selectedId;
        this.mode = 'selected';
        this.currentItems = [];

        // Scrolling and resizing never replace focused buttons or a native drag handle.
        if (signature !== this.signature || !root.firstElementChild) {
            root.innerHTML = BuilderOverlayUI.render(selectedId, label, {}, 'selected', capabilities);
            this.signature = signature;
        }
        if (targetChanged) this.observeTargets();
        this.schedulePosition();
    },

    showGroup(items = []) {
        const selectedId = window.BuilderStore?.selectedNodeId;
        const selected = items.find(item => item?.nodeId === selectedId);
        this.show(selected?.element || null, selectedId);
    },

    observeTargets() {
        if (!this.observer) return;
        this.observer.disconnect();
        [document.getElementById('builder-workspace'), BuilderOverlayElements.getWrapper(), this.currentElement]
            .filter(Boolean).forEach(element => this.observer.observe(element));
    },

    schedulePosition() {
        if (!this.currentNodeId || this.frame !== null) return;
        this.frame = requestAnimationFrame(() => {
            this.frame = null;
            this.reposition();
        });
    },

    reposition() {
        if (!this.currentNodeId) return;
        const root = BuilderOverlayElements.getRoot();
        const toolbar = root?.firstElementChild;
        const wrapper = BuilderOverlayElements.getWrapper();
        if (!toolbar || !wrapper) return;
        if (this.currentNodeId !== window.BuilderStore?.selectedNodeId) {
            this.show(null, window.BuilderStore?.selectedNodeId);
            return;
        }
        if (!this.currentElement?.isConnected) {
            this.currentElement = BuilderOverlayElements.getElement(this.currentNodeId);
            this.observeTargets();
        }
        if (!this.currentElement) {
            toolbar.style.visibility = 'hidden';
            toolbar.inert = true;
            return;
        }

        const bounds = BuilderOverlayPosition.visibleBounds(wrapper, document.getElementById('builder-workspace'));
        const availableWidth = Math.max(0, bounds.right - bounds.left - 16);
        toolbar.style.maxWidth = `${availableWidth}px`;
        toolbar.dataset.compact = String(availableWidth < 400);
        const size = toolbar.getBoundingClientRect();
        const position = BuilderOverlayPosition.calculate(this.currentElement, {
            bounds, toolbarSize: { width: size.width, height: size.height }
        });
        toolbar.style.visibility = position.visible ? 'visible' : 'hidden';
        toolbar.inert = !position.visible;
        if (!position.visible) return;
        toolbar.style.left = `${position.left}px`;
        toolbar.style.top = `${position.top}px`;
        toolbar.style.maxHeight = `${position.maxHeight}px`;
    },

    hide(options = {}) {
        if (options.hoverOnly && window.BuilderStore?.selectedNodeId) return;
        if (this.frame !== null) cancelAnimationFrame(this.frame);
        this.frame = null;
        this.observer?.disconnect();
        this.currentElement = null;
        this.currentNodeId = null;
        this.mode = null;
        this.signature = null;
        this.currentItems = [];
        const root = BuilderOverlayElements.getRoot();
        if (root) root.replaceChildren();
    }
};
