window.BuilderRuntimeActions = {

    bound: false,

    actions: {

        'switch-tab'(target) {

            BuilderSidebar.switchTab(
                target.dataset.tab
            );

        },

        'add-component'(target) {

            BuilderComponents.add(
                target.dataset.component
            );

        },

        'set-viewport'(target) {

            BuilderViewport.set(
                target.dataset.viewport
            );

        },

        'toggle-left-sidebar'() {

            BuilderSidebar.toggle();

        },

        'toggle-right-sidebar'() {

            BuilderRightSidebar.toggle();

        },

        'history-undo'() {

            BuilderHistory.undo();

        },

        'history-redo'() {

            BuilderHistory.redo();

        },

        'save-page'() {

            BuilderStorage.save();

        },

        'overlay-move-up'(target) {

            BuilderOverlayActions.moveUp(
                target.dataset.targetNodeId
            );

        },

        'overlay-move-down'(target) {

            BuilderOverlayActions.moveDown(
                target.dataset.targetNodeId
            );

        },

        'overlay-duplicate'(target) {

            BuilderOverlayActions.duplicate(
                target.dataset.targetNodeId
            );

        },

        'overlay-edit'(target) {

            BuilderOverlayActions.edit(
                target.dataset.targetNodeId
            );

        },

        'overlay-delete'() {

            BuilderSelection.delete();

        },

        'select-layer'(target) {

            BuilderRightSidebar.select(
                target.dataset.layerNode
            );

        }

    },

    init() {

        if (this.bound) {
            return;
        }

        this.bound = true;

        document.addEventListener(
            'click',
            this.handleClick.bind(this)
        );

        document.addEventListener(
            'input',
            this.handleInput.bind(this)
        );

        document.addEventListener(
            'dragstart',
            this.handleDragStart.bind(this)
        );

        document.addEventListener(
            'dragend',
            this.handleDragEnd.bind(this)
        );

        document.addEventListener(
            'dragover',
            this.handleDragOver.bind(this)
        );

        document.addEventListener(
            'drop',
            this.handleDrop.bind(this)
        );

    },

    handleClick(event) {

        const target =
            event.target.closest(
                '[data-action]'
            );

        if (!target) {
            return;
        }

        const action =
            this.actions[
                target.dataset.action
            ];

        if (!action) {
            return;
        }

        if (BuilderLogger.shouldLog('interaction')) {

            BuilderLogger.log(
                'INTERACTION ACTION',
                {
                    action:
                        target.dataset.action,

                    surface:
                        BuilderInteractionBoundaries
                            .surface(event.target)
                }
            );

        }

        event.preventDefault();
        event.stopPropagation();

        action(
            target,
            event
        );

    },

    handleInput(event) {

        const target =
            event.target.closest(
                '[data-setting-field]'
            );

        if (!target) {
            return;
        }

        BuilderSettingsUpdater.updateField(
            target.dataset.targetNodeId,
            target.dataset.settingField,
            target.value
        );

    },

    handleDragStart(event) {

        const target =
            event.target.closest(
                '[data-drag-action]'
            );

        if (!target) {
            return;
        }

        if (
            target.dataset.dragAction === 'component'
        ) {

            BuilderDragDrop.start(
                event,
                target.dataset.component
            );

            return;

        }

        if (
            target.dataset.dragAction === 'reorder'
        ) {

            BuilderDragReorder.start(
                event,
                target.dataset.nodeId
            );

        }

    },

    handleDragEnd(event) {

        const target =
            event.target.closest(
                '[data-drag-action="reorder"]'
            );

        if (!target) {
            return;
        }

        BuilderDragReorder.end(
            event
        );

    },

    handleDragOver(event) {

        if (
            !event.target.closest('#canvas')
        ) {
            return;
        }

        BuilderDragDrop.allowDrop(
            event
        );

    },

    handleDrop(event) {

        if (
            !event.target.closest('#canvas')
        ) {
            return;
        }

        BuilderDragDrop.drop(
            event
        );

    }

};
