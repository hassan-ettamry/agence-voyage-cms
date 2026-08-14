import test from 'node:test';
import assert from 'node:assert/strict';
import { componentMatches, normalizeComponentFilter } from '../../resources/js/builder/component-library-filter.js';

test('normalizes component searches for non-technical labels', () => {
    assert.equal(normalizeComponentFilter('  Search HERO '), 'search hero');
});

test('filters components by label, type, and category', () => {
    const component = { label: 'Search Hero', type: 'search-hero', category: 'marketing' };
    assert.equal(componentMatches(component, 'hero', 'all'), true);
    assert.equal(componentMatches(component, 'search-hero', 'marketing'), true);
    assert.equal(componentMatches(component, '', 'travel'), false);
    assert.equal(componentMatches(component, 'newsletter', 'all'), false);
});
