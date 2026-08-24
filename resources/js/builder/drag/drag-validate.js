window.BuilderDragValidate = {

    /*
    |--------------------------------------------------------------------------
    | Validate Child
    |--------------------------------------------------------------------------
    */

    canDrop(parent, type) {

        if (!parent) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Accepts
        |--------------------------------------------------------------------------
        */

        if (
            !BuilderStructureRules.canAccept(
                parent,
                type
            )
        ) {

            console.warn(

                `${type} not allowed inside ${parent.type}`

            );

            if (window.BuilderStorage) {
                BuilderStorage.setStatus(`${type} cannot be placed inside ${parent.type}.`, 'error');
                window.setTimeout(() => BuilderStorage.refreshDirtyState(), 2200);
            }

            return false;

        }

        return true;

    }

};
