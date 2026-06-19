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

        if (window.BuilderStructureRules) {

            BuilderStructureRules.normalizeNode(
                component
            );

        }

        component.id =
            BuilderComponentUtils.generateId();

        return component;

    }

};
