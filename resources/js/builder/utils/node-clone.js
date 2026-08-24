function fallbackId() {
    if (globalThis.crypto?.randomUUID) return globalThis.crypto.randomUUID();
    return `component_${Date.now()}_${Math.random().toString(16).slice(2)}`;
}

export function cloneBuilderNodes(nodes, generateId = fallbackId, normalizeProps = props => props || {}) {
    if (!Array.isArray(nodes)) return [];

    return nodes
        .filter(node => node && typeof node === 'object' && !Array.isArray(node))
        .map(node => ({
            id: generateId(),
            type: node.type,
            accepts: typeof window !== 'undefined' && window.BuilderStructureRules
                ? BuilderStructureRules.acceptsForType(node.type)
                : node.accepts,
            props: structuredClone(normalizeProps(node.props, node.type)),
            children: cloneBuilderNodes(node.children || [], generateId, normalizeProps)
        }));
}

if (typeof window !== 'undefined') window.BuilderNodeClone = { cloneNodes: cloneBuilderNodes };
