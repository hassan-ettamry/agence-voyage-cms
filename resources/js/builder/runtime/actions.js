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

        'add-blank-section'() {

            BuilderTemplateLibrary.addBlankSection();

        },

        'open-template-library'() {

            BuilderTemplateLibrary.open('blocks');

        },

        'close-template-library'() {

            BuilderTemplateLibrary.close();

        },

        'close-section-layout-picker'() {

            BuilderTemplateLibrary.closeSectionLayoutPicker();

        },

        'choose-section-layout'(target) {

            BuilderTemplateLibrary.insertSectionLayout(
                target.dataset.sectionLayout
            );

        },

        'retry-canvas-render'() {

            BuilderCanvas.retryRender();

        },

        'reload-builder'() {

            window.location.reload();

        },

        'template-library-tab'(target) {

            BuilderTemplateLibrary.setTab(
                target.dataset.templateTab
            );

        },

        'template-library-category'(target) {

            BuilderTemplateLibrary.setCategory(
                target.value
            );

        },

        'template-library-category-button'(target) {

            BuilderTemplateLibrary.setCategory(
                target.dataset.templateCategoryValue
            );

        },

        'template-library-search'(target) {

            BuilderTemplateLibrary.setSearch(
                target.value
            );

        },

        'insert-builder-template'(target) {

            BuilderTemplateLibrary.insert(
                target.dataset.templateId
            );

        },

        'save-section-block'(target) {
            BuilderSavedBlocks.saveSection(target.dataset.targetNodeId);
        },

        'submit-saved-block-form'() {
            BuilderSavedBlocks.submitForm();
        },

        'close-saved-block-form'() {
            BuilderSavedBlocks.closeForm();
        },

        'rename-saved-block'(target) {
            BuilderSavedBlocks.rename(target.dataset.savedBlockId);
        },

        'delete-saved-block'(target) {
            BuilderSavedBlocks.remove(target.dataset.savedBlockId);
        },

        'set-viewport'(target) {

            BuilderViewport.set(
                target.dataset.viewport
            );

        },

        'richtext-command'(target) {

            BuilderRichTextControls.run(
                target
            );

        },

        'section-control-tab'(target) {

            BuilderSettingsPanel.activateSectionTab(
                target.dataset.sectionTab
            );

        },

        'section-accordion-toggle'(target) {

            BuilderSettingsPanel.toggleAccordion(
                target.dataset.sectionAccordion
            );

        },

        'settings-tab-toggle'(target) {

            BuilderSettingsPanel.toggleSettingTab(
                target.dataset.targetNodeId,
                target.dataset.settingsTab
            );

        },

        'section-background-mode'(target) {

            BuilderSettingsPanel.setSectionBackgroundMode(
                target
            );

        },

        'apply-container-role-preset'(target) {

            BuilderSettingsUpdater.applyContainerRolePreset(
                target.dataset.targetNodeId
            );

        },

        'use-theme-default'(target) {

            BuilderSettingsUpdater.useThemeDefault(
                target.dataset.targetNodeId,
                target.dataset.settingField
            );

        },

        'open-builder-media'(target) {
            BuilderMediaPicker.open(target.dataset.targetNodeId, target.dataset.settingField);
        },

        'open-repeater-media'(target) {
            BuilderMediaPicker.open(target.dataset.targetNodeId, target.dataset.settingField, {
                index: Number(target.dataset.repeaterIndex),
                field: target.dataset.repeaterField
            });
        },

        'clear-builder-media'(target) {
            BuilderMediaPicker.clear(target.dataset.targetNodeId, target.dataset.settingField);
        },

        'clear-repeater-media'(target) {
            BuilderMediaPicker.clearRepeater(
                target.dataset.targetNodeId,
                target.dataset.settingField,
                Number(target.dataset.repeaterIndex),
                target.dataset.repeaterField
            );
        },

        'choose-builder-media'(target) {
            BuilderMediaPicker.choose(target.dataset.mediaUrl);
        },

        'close-builder-media'() {
            BuilderMediaPicker.close();
        },

        'repeater-add'(target) {
            BuilderRepeater.add(target.dataset.targetNodeId, target.dataset.settingField);
        },

        'repeater-remove'(target) {
            BuilderRepeater.remove(target.dataset.targetNodeId, target.dataset.settingField, Number(target.dataset.repeaterIndex));
        },

        'repeater-up'(target) {
            BuilderRepeater.move(target.dataset.targetNodeId, target.dataset.settingField, Number(target.dataset.repeaterIndex), -1);
        },

        'repeater-down'(target) {
            BuilderRepeater.move(target.dataset.targetNodeId, target.dataset.settingField, Number(target.dataset.repeaterIndex), 1);
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

        'preview-page'() {
            BuilderStorage.preview();
        },

        'publish-page'() {
            BuilderStorage.publish();
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

        'overlay-delete'(target) {

            BuilderOverlayActions.delete(
                target.dataset.targetNodeId
            );

        },

        'overlay-select-parent'(target) {
            BuilderSelectionManager.selectParent(target.dataset.targetNodeId);
        },

        'select-layer'(target) {

            BuilderRightSidebar.select(
                target.dataset.layerNode
            );

        },

        'toggle-layer-node'(target) {

            BuilderRightSidebar.toggleNode(
                target.dataset.layerKey
                    || target.dataset.layerNode
            );

        }

    },

    init() {

        if (this.bound) {
            return;
        }

        this.bound = true;

        document.addEventListener('pointerdown', event => {
            this.dragOrigin = event.target;
        }, true);

        document.addEventListener(
            'click',
            this.handleClick.bind(this)
        );

        document.addEventListener(
            'input',
            this.handleInput.bind(this)
        );

        document.addEventListener(
            'change',
            this.handleChange.bind(this)
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

        if (!target || target.disabled || target.getAttribute('aria-disabled') === 'true') {
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

        const pageField = event.target.closest('[data-page-field]');
        if (pageField) {
            BuilderStorage.pageChanged(pageField.dataset.pageField, pageField.value);
            return;
        }

        const repeaterTarget = event.target.closest('[data-repeater-field]');
        if (repeaterTarget) {
            BuilderRepeater.update(
                repeaterTarget.dataset.targetNodeId,
                repeaterTarget.dataset.settingField,
                Number(repeaterTarget.dataset.repeaterIndex),
                repeaterTarget.dataset.repeaterField,
                repeaterTarget.value,
                true
            );
            return;
        }

        const templateTarget =
            event.target.closest(
                '[data-template-search]'
            );

        if (templateTarget) {

            BuilderTemplateLibrary.setSearch(
                templateTarget.value
            );

            return;

        }

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
            target.dataset.richtextEditor === 'true'
                ? target.innerHTML
                : target.value,
            { deferHistory: true }
        );

    },

    handleChange(event) {

        const settingTarget =
            event.target.closest(
                '[data-setting-field]'
            );

        if (settingTarget) {

            const value = settingTarget.multiple
                ? Array.from(settingTarget.selectedOptions).map(option => option.value)
                : settingTarget.value;

            BuilderSettingsUpdater.updateField(
                settingTarget.dataset.targetNodeId,
                settingTarget.dataset.settingField,
                value
            );

            return;

        }

        const categoryTarget =
            event.target.closest(
                '[data-template-category]'
            );

        if (categoryTarget) {

            BuilderTemplateLibrary.setCategory(
                categoryTarget.value
            );

        }

    },

    handleDragStart(event) {

        if (BuilderHistory.isRestoring) {
            event.preventDefault();
            return;
        }

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

            const origin = this.dragOrigin || event.target;
            const isHandle = !!origin.closest('[data-builder-drag-handle]');
            if (!isHandle && origin.closest('input, textarea, select, button, a, [contenteditable="true"]')) {
                event.preventDefault();
                return;
            }

            BuilderDragReorder.start(
                event,
                target.dataset.targetNodeId || target.dataset.nodeId
            );

        }

    },

    handleDragEnd(event) {
        this.dragOrigin = null;
        if (BuilderDragState.getDraggedNode() || BuilderDragState.componentType || BuilderDragReorder.activeElement) {
            BuilderDragReorder.end(event);
        }

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
