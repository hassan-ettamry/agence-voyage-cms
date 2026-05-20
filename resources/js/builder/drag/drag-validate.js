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

            return false;

        }

        return true;

    }

};
