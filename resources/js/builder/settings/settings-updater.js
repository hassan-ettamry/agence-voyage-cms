window.BuilderSettingsUpdater = {

    /*
    |--------------------------------------------------------------------------
    | Update Field
    |--------------------------------------------------------------------------
    */

    updateField(
        nodeId,
        key,
        value
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
        | Sync Row Columns
        |--------------------------------------------------------------------------
        */

        this.syncRowColumns(

            node,
            key,
            value

        );

        /*
        |--------------------------------------------------------------------------
        | Save History
        |--------------------------------------------------------------------------
        */

        BuilderHistory.push();

    },

    /*
    |--------------------------------------------------------------------------
    | Sync Row Columns
    |--------------------------------------------------------------------------
    */

    syncRowColumns(
        node,
        key,
        value
    ) {

        if (

            node.type !== 'row'

            ||

            key !== 'columns'

        ) {

            return;

        }

        const columns =
            parseInt(value);

        /*
        |--------------------------------------------------------------------------
        | Ensure Children
        |--------------------------------------------------------------------------
        */

        if (!node.children) {

            node.children = [];

        }

        let changed = false;

        /*
        |--------------------------------------------------------------------------
        | Add Missing Columns
        |--------------------------------------------------------------------------
        */

        while (
            node.children.length < columns
        ) {

            node.children.push({

                id:
                    BuilderComponentUtils.generateId(),

                type: 'column',

                accepts:
                    BuilderStructureRules.acceptsForType(
                        'column'
                    ),

                props: {},

                children: []

            });

            changed = true;

        }

        /*
        |--------------------------------------------------------------------------
        | Remove Extra Columns
        |--------------------------------------------------------------------------
        */

        while (
            node.children.length > columns
        ) {

            node.children.pop();

            changed = true;

        }

        if (changed) {

            BuilderStructureRules.normalizeStore();

            BuilderEventBus.emit(
                BuilderEvents.STRUCTURE_UPDATED
            );

        }

    }

};
