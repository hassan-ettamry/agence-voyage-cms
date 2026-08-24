export function normalizeRepeater(value, fields = []) {
    if (Array.isArray(value)) {
        return value.filter(item => item && typeof item === 'object').map(item => ({ ...item }));
    }
    if (typeof value !== 'string' || value.trim() === '') return [];

    return value.split(/\r?\n/).filter(Boolean).map(line => {
        const parts = line.split('|').map(part => part.trim());
        return fields.reduce((item, field, index) => {
            item[field.key] = parts[index] ?? '';
            return item;
        }, {});
    });
}

export function moveRepeaterItem(items, from, to) {
    const copy = items.map(item => ({ ...item }));
    if (from < 0 || from >= copy.length || to < 0 || to >= copy.length) return copy;
    const [item] = copy.splice(from, 1);
    copy.splice(to, 0, item);
    return copy;
}

if (typeof window !== 'undefined') {
    window.BuilderRepeater = {
        items(value, field) {
            return normalizeRepeater(value, field.itemFields || []);
        },
        update(nodeId, key, index, itemKey, value, deferHistory = false) {
            const node = Builder.findNodeById(nodeId);
            const schema = BuilderSchema.get(node?.type);
            const field = Object.values(schema?.tabs || {}).flatMap(tab => Object.entries(tab.fields || []))
                .find(([fieldKey]) => fieldKey === key)?.[1];
            if (!node || !field) return;
            const items = normalizeRepeater(node.props?.[key], field.itemFields || []);
            if (!items[index]) return;
            items[index][itemKey] = value;
            BuilderSettingsUpdater.updateField(nodeId, key, items, { deferHistory });
        },
        add(nodeId, key) {
            const node = Builder.findNodeById(nodeId);
            const schema = BuilderSchema.get(node?.type);
            const field = Object.values(schema?.tabs || {}).flatMap(tab => Object.entries(tab.fields || []))
                .find(([fieldKey]) => fieldKey === key)?.[1];
            if (!node || !field) return;
            const items = normalizeRepeater(node.props?.[key], field.itemFields || []);
            if (items.length >= (field.maxItems || 24)) return;
            items.push(Object.fromEntries((field.itemFields || []).map(item => [item.key, item.default ?? ''])));
            BuilderSettingsUpdater.updateField(nodeId, key, items);
            BuilderSettingsUpdater.refreshSettingsPanel(nodeId);
        },
        remove(nodeId, key, index) {
            const node = Builder.findNodeById(nodeId);
            if (!node) return;
            const schema = BuilderSchema.get(node.type);
            const field = Object.values(schema?.tabs || {}).flatMap(tab => Object.entries(tab.fields || []))
                .find(([fieldKey]) => fieldKey === key)?.[1];
            const items = normalizeRepeater(node.props?.[key], field?.itemFields || []);
            items.splice(index, 1);
            BuilderSettingsUpdater.updateField(nodeId, key, items);
            BuilderSettingsUpdater.refreshSettingsPanel(nodeId);
        },
        move(nodeId, key, index, direction) {
            const node = Builder.findNodeById(nodeId);
            if (!node) return;
            const schema = BuilderSchema.get(node.type);
            const field = Object.values(schema?.tabs || {}).flatMap(tab => Object.entries(tab.fields || []))
                .find(([fieldKey]) => fieldKey === key)?.[1];
            const items = normalizeRepeater(node.props?.[key], field?.itemFields || []);
            const moved = moveRepeaterItem(items, index, index + direction);
            BuilderSettingsUpdater.updateField(nodeId, key, moved);
            BuilderSettingsUpdater.refreshSettingsPanel(nodeId);
        }
    };
}
