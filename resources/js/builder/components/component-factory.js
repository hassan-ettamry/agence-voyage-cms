window.BuilderComponentFactory = {

    /*
    |--------------------------------------------------------------------------
    | Create Component
    |--------------------------------------------------------------------------
    */

    create(type) {

        const factory =
            BuilderComponentRegistry[type];

        const schema = BuilderSchema.get(type);

        if (!factory && !schema) return null;

        const component = factory
            ? factory()
            : {
                type,
                accepts: BuilderStructureRules.acceptsForType(type),
                props: BuilderSchemaDefaults.fromSchema(schema),
                children: []
            };

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
