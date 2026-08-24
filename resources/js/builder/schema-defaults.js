export function defaultsFromSchema(schema = {}) {
    const defaults = {};

    Object.entries(schema.props || {}).forEach(([key, definition]) => {
        if (definition && Object.prototype.hasOwnProperty.call(definition, 'default')) {
            defaults[key] = structuredClone(definition.default);
        }
    });

    Object.values(schema.tabs || {}).forEach(tab => {
        Object.entries(tab?.fields || {}).forEach(([key, field]) => {
            const emptyNumber = ['number', 'range'].includes(field?.type) && field?.default === '';
            if (!key.includes('.') && field?.type !== 'notice'
                && !emptyNumber
                && Object.prototype.hasOwnProperty.call(field || {}, 'default')
                && !Object.prototype.hasOwnProperty.call(defaults, key)) {
                defaults[key] = structuredClone(field.default);
            }
        });
    });

    return defaults;
}

if (typeof window !== 'undefined') window.BuilderSchemaDefaults = { fromSchema: defaultsFromSchema };
