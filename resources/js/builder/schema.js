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

        return component.schema_json || null;
    }

};