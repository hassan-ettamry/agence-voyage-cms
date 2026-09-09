window.BuilderComponents = {

    /*
    |--------------------------------------------------------------------------
    | Add Component
    |--------------------------------------------------------------------------
    */

    add(type) {

        const component =

            BuilderComponentFactory.create(
                type
            );

        if (!component) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Add To State
        |--------------------------------------------------------------------------
        */

        BuilderStore.addRootComponent(
            component
        );

        BuilderEventBus.emit(
            BuilderEvents.COMPONENT_ADDED,
            component
        );

        /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        */

        BuilderHistory.push();

        /*
        |--------------------------------------------------------------------------
        | Re-render
        |--------------------------------------------------------------------------
        */

        BuilderRenderManager.requestRender(
            'component.add'
        );

    }

};
