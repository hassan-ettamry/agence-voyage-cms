window.BuilderSettingsUpdater = {

    /*
    |--------------------------------------------------------------------------
    | Update Field
    |--------------------------------------------------------------------------
    */

    updateField(
        nodeId,
        key,
        value,
        options = {}
    ) {

        /*
        |--------------------------------------------------------------------------
        | Find Node
        |--------------------------------------------------------------------------
        */

        const node =
            Builder.findNodeById(
                nodeId
            );

        if (!node) return;

        if (
            key === 'containerRole'
            &&
            node.type === 'container'
            &&
            window.BuilderContainerRoles
        ) {
            node.props =
                BuilderContainerRoles.prepareRoleChange(
                    BuilderStructureRules.ensurePlainProps(
                        node.props
                    )
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Props
        |--------------------------------------------------------------------------
        */

        BuilderNodes.updateProps(

            nodeId,
            key,
            value

        );

        /*
        |--------------------------------------------------------------------------
        | Save History
        |--------------------------------------------------------------------------
        */

        if (options.deferHistory) {
            BuilderHistory.schedulePush(`${nodeId}:${key}`);
        } else {
            BuilderHistory.flushPending();
            BuilderHistory.push();
        }

        if ([
            'containerRole',
            'linkType',
            'source',
            'showCta',
            'icon',
            'display',
            'backgroundMode',
            'sourceType'
        ].includes(key)) {
            this.refreshSettingsPanel(nodeId);
        }

    },

    /*
    |--------------------------------------------------------------------------
    | Apply Container Role Preset
    |--------------------------------------------------------------------------
    */

    applyContainerRolePreset(nodeId) {

        const node =
            Builder.findNodeById(
                nodeId
            );

        if (
            !node
            ||
            node.type !== 'container'
            ||
            !window.BuilderContainerRoles
        ) {
            return;
        }

        node.props =
            BuilderStructureRules.ensurePlainProps(
                node.props
            );

        const result =
            BuilderContainerRoles.applyPreset(
                node.props.containerRole || 'group',
                node.props
            );

        node.props = result.props;

        BuilderEventBus.emit(
            BuilderEvents.NODE_UPDATED,
            {
                nodeId,
                key: 'rolePreset',
                value: node.props.rolePreset
            }
        );

        BuilderHistory.push();

        this.refreshSettingsPanel(nodeId);

    },

    /*
    |--------------------------------------------------------------------------
    | Use Theme Default
    |--------------------------------------------------------------------------
    */

    useThemeDefault(nodeId, key) {

        const node =
            Builder.findNodeById(
                nodeId
            );

        if (!node || !key) {
            return;
        }

        node.props =
            BuilderStructureRules.ensurePlainProps(
                node.props
            );

        if (!BuilderObjectPath.has(node.props, key)) {
            return;
        }

        BuilderObjectPath.delete(node.props, key);

        BuilderEventBus.emit(
            BuilderEvents.NODE_UPDATED,
            {
                nodeId,
                key,
                value: undefined
            }
        );

        BuilderHistory.push();

        this.refreshSettingsPanel(nodeId);

    },

    /*
    |--------------------------------------------------------------------------
    | Refresh Settings Panel
    |--------------------------------------------------------------------------
    */

    refreshSettingsPanel(nodeId) {

        if (
            BuilderStore.selectedNodeId !== nodeId
            ||
            !window.BuilderSettingsPanel
        ) {
            return;
        }

        const node =
            Builder.findNodeById(
                nodeId
            );

        if (!node) {
            return;
        }

        BuilderSettingsPanel.render(
            node,
            BuilderSchema.get(node.type),
            nodeId
        );

    }

};
