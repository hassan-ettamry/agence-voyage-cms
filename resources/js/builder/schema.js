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

        const schema =
            component.schema_json || null;

        return window.BuilderControlSchemas
            ? BuilderControlSchemas.get(type, schema)
            : schema;
    }

};
