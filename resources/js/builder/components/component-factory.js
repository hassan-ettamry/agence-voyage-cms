window.BuilderComponentFactory = {

    /*
    |--------------------------------------------------------------------------
    | Create Component
    |--------------------------------------------------------------------------
    */

    create(type) {

        const factory =
            BuilderComponentRegistry[type];

        if (!factory) {
            return null;
        }

        const component =
            factory();

        component.id =
            BuilderComponentUtils.generateId();

        return component;

    }

};