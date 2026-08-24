export function getPath(object, path, fallback = undefined) {
    if (!path) return fallback;
    const value = String(path).split('.').reduce(
        (current, key) => current && typeof current === 'object' ? current[key] : undefined,
        object
    );
    return value === undefined ? fallback : value;
}

export function hasPath(object, path) {
    if (!object || typeof object !== 'object' || !path) return false;
    let current = object;
    for (const key of String(path).split('.')) {
        if (!current || typeof current !== 'object' || !Object.prototype.hasOwnProperty.call(current, key)) return false;
        current = current[key];
    }
    return true;
}

export function setPath(object, path, value) {
    const keys = String(path || '').split('.').filter(Boolean);
    if (!keys.length) return object;
    let current = object;
    keys.slice(0, -1).forEach(key => {
        if (!current[key] || typeof current[key] !== 'object' || Array.isArray(current[key])) current[key] = {};
        current = current[key];
    });
    current[keys.at(-1)] = value;
    return object;
}

export function deletePath(object, path) {
    const keys = String(path || '').split('.').filter(Boolean);
    if (!keys.length || !object || typeof object !== 'object') return false;
    const parents = [];
    let current = object;
    for (const key of keys.slice(0, -1)) {
        if (!current[key] || typeof current[key] !== 'object') return false;
        parents.push([current, key]);
        current = current[key];
    }
    const leaf = keys.at(-1);
    if (!Object.prototype.hasOwnProperty.call(current, leaf)) return false;
    delete current[leaf];
    for (let index = parents.length - 1; index >= 0; index -= 1) {
        const [parent, key] = parents[index];
        if (Object.keys(parent[key]).length === 0) delete parent[key];
    }
    return true;
}

if (typeof window !== 'undefined') {
    window.BuilderObjectPath = { get: getPath, has: hasPath, set: setPath, delete: deletePath };
}
