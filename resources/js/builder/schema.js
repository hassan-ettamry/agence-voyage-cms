window.BuilderSchema = {

    get(type) {

        if (!window.builderComponents)
            return null;

        const component =
            window.builderComponents.find(
                c => c.type === type
            );

        if (!component)
            return null;

        const databaseSchema = component.schema_json || null;
        const legacySchema = window.BuilderControlSchemas
            ? BuilderControlSchemas.get(type, databaseSchema)
            : databaseSchema;
        const schema = databaseSchema?.tabs ? databaseSchema : legacySchema;

        return window.BuilderSharedControls
            ? BuilderSharedControls.merge(type, schema || {}, BuilderStore?.viewport || 'desktop')
            : schema;
    }

};
