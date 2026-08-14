export function normalizeComponentFilter(value) {
    return String(value || '').trim().toLocaleLowerCase();
}

export function componentMatches(component, query = '', category = 'all') {
    const normalizedQuery = normalizeComponentFilter(query);
    const normalizedCategory = normalizeComponentFilter(category) || 'all';
    const label = normalizeComponentFilter(component?.label);
    const type = normalizeComponentFilter(component?.type);
    const itemCategory = normalizeComponentFilter(component?.category || 'other');

    const matchesCategory = normalizedCategory === 'all' || itemCategory === normalizedCategory;
    const matchesQuery = !normalizedQuery || `${label} ${type} ${itemCategory}`.includes(normalizedQuery);

    return matchesCategory && matchesQuery;
}
