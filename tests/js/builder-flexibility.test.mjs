import test from 'node:test';
import assert from 'node:assert/strict';
import { defaultsFromSchema } from '../../resources/js/builder/schema-defaults.js';
import { resolveResponsiveDesign } from '../../resources/js/builder/settings/shared-controls.js';
import { normalizeRepeater, moveRepeaterItem } from '../../resources/js/builder/settings/repeater.js';
import { DirtyTracker } from '../../resources/js/builder/dirty-state.js';
import { cloneBuilderNodes } from '../../resources/js/builder/utils/node-clone.js';

test('normalizes defaults from versioned and legacy component schemas', () => {
    assert.deepEqual(defaultsFromSchema({ props: { title: { type: 'text', default: 'V2 title' } } }), {
        title: 'V2 title'
    });

    assert.deepEqual(defaultsFromSchema({ tabs: { content: { fields: {
        title: { type: 'text', default: 'Legacy title' },
        limit: { type: 'number', default: '' }
    } } } }), { title: 'Legacy title' });
});

test('responsive values inherit desktop then tablet with sparse mobile overrides', () => {
    const design = {
        desktop: { columns: 4, gap: 32, alignment: 'left' },
        tablet: { columns: 2 },
        mobile: { columns: 1, alignment: 'center' }
    };

    assert.deepEqual(resolveResponsiveDesign(design, 'desktop'), { columns: 4, gap: 32, alignment: 'left' });
    assert.deepEqual(resolveResponsiveDesign(design, 'tablet'), { columns: 2, gap: 32, alignment: 'left' });
    assert.deepEqual(resolveResponsiveDesign(design, 'mobile'), { columns: 1, gap: 32, alignment: 'center' });
});

test('repeaters normalize legacy text and preserve explicit ordering', () => {
    const fields = [{ key: 'title' }, { key: 'text' }];
    const normalized = normalizeRepeater('First|One\nSecond|Two', fields);

    assert.deepEqual(normalized, [
        { title: 'First', text: 'One' },
        { title: 'Second', text: 'Two' }
    ]);
    assert.deepEqual(moveRepeaterItem(normalized, 1, 0).map(item => item.title), ['Second', 'First']);
});

test('dirty state changes only after the saved snapshot changes', () => {
    const tracker = new DirtyTracker('initial');
    assert.equal(tracker.isDirty('initial'), false);
    assert.equal(tracker.isDirty('edited'), true);
    tracker.markSaved('edited');
    assert.equal(tracker.isDirty('edited'), false);
});

test('duplicate and saved block insertion regenerate every nested UUID', () => {
    let nextId = 0;
    const source = [{
        id: 'section-old',
        type: 'section',
        props: { title: 'Independent copy' },
        children: [{ id: 'text-old', type: 'text', props: { text: 'Hello' }, children: [] }]
    }];
    const first = cloneBuilderNodes(source, () => `new-${++nextId}`);
    const second = cloneBuilderNodes(source, () => `new-${++nextId}`);

    assert.deepEqual([first[0].id, first[0].children[0].id], ['new-1', 'new-2']);
    assert.deepEqual([second[0].id, second[0].children[0].id], ['new-3', 'new-4']);
    first[0].props.title = 'Changed';
    assert.equal(source[0].props.title, 'Independent copy');
});

test('history groups successive text edits and ignores identical snapshots', async () => {
    globalThis.window = globalThis;
    let structure = [{ id: 'text', type: 'text', props: { text: 'A' }, children: [] }];
    globalThis.BuilderStructureRules = { normalizeStore() {} };
    globalThis.BuilderStore = {
        getStructure: () => structure,
        setStructure: value => { structure = value; },
        selectedNodeId: null
    };
    globalThis.BuilderLogger = {
        colors: { history: '' }, group() {}, log() {}, warn() {}, end() {}
    };
    globalThis.BuilderCanvas = { async render() {} };
    await import('../../resources/js/builder/history/history-manager.js');

    BuilderHistory.stack = [];
    BuilderHistory.future = [];
    BuilderHistory.pendingTimer = null;
    BuilderHistory.init();
    BuilderHistory.push();
    structure = [{ id: 'text', type: 'text', props: { text: 'AB' }, children: [] }];
    BuilderHistory.schedulePush('inline:text');
    structure = [{ id: 'text', type: 'text', props: { text: 'ABC' }, children: [] }];
    BuilderHistory.schedulePush('inline:text');
    await new Promise(resolve => setTimeout(resolve, 500));

    assert.equal(BuilderHistory.stack.length, 2);
    await BuilderHistory.undo();
    assert.equal(BuilderStore.getStructure()[0].props.text, 'A');
    await BuilderHistory.redo();
    assert.equal(BuilderStore.getStructure()[0].props.text, 'ABC');
});
