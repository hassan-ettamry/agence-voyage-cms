import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

// Load the browser's real interaction modules in a fresh global context per test.
// Only browser DOM/rendering, settings display and native confirmation are faked.
const modules = [
    'core/events.js', 'core/event-bus.js', 'core/structure-rules.js', 'core/store.js',
    'nodes/node-traversal.js', 'nodes/node-actions.js', 'components/component-utils.js',
    'components/component-factory.js',
    'overlay/overlay-theme.js', 'canvas/canvas-utils.js', 'selection/selection-manager.js',
    'history/history-manager.js', 'nodes/node-move.js', 'overlay/overlay-actions.js',
    'selection/selection-actions.js', 'history/history-keyboard.js',
    'drag/drag-state.js', 'drag/drag-hitbox.js', 'drag/drag-preview.js',
    'drag/drag-reorder.js', 'drag/drag-engine.js', 'drag/drag-drop.js',
    'utils/html-escape.js', 'overlay/overlay-ui.js'
];
const source = new Map(modules.map(path => [path,
    readFileSync(new URL(`../../resources/js/builder/${path}`, import.meta.url), 'utf8')
]));
const plain = value => JSON.parse(JSON.stringify(value));
const node = (id, type = 'text', children = []) => ({
    id, type, props: { text: id, design: { desktop: { color: 'blue' } } }, children
});
const tree = () => [
    node('section', 'section', [node('a'), node('b'), node('c')]),
    node('other', 'section', [node('d')])
];

function fixture(structure = tree()) {
    const elements = new Map();
    const panel = { innerHTML: '', owner: null };
    const observations = { settings: [], selections: [], renders: [], toolbar: null, confirmations: [] };
    const noStyleMutation = () => { throw new Error('Editor mutated component-authored style or classes'); };
    const canvas = {
        dataset: {},
        querySelectorAll: () => [...elements.values()],
        getBoundingClientRect: () => ({ top: 0, left: 0, width: 800, height: 800 }),
        closest: selector => selector === '#canvas' ? canvas : null
    };
    const document = {
        activeElement: null,
        addEventListener() {},
        getElementById: id => id === 'settings-panel' ? panel : id === 'canvas' ? canvas : null,
        querySelector: selector => elements.get(selector.match(/data-node-id="([^"]+)"/)?.[1]) || null
    };
    const context = vm.createContext({
        console, setTimeout, clearTimeout, structuredClone,
        document, CSS: { escape: value => value },
        confirm: message => { observations.confirmations.push(message); return true; },
        BuilderLogger: { colors: {}, log() {}, group() {}, success() {}, end() {}, warn() {} },
        BuilderSchema: { get: () => ({ tabs: { content: {}, style: {} } }) },
        BuilderComponentRegistry: { text: () => node('factory-id') },
        BuilderSidebar: { setTitle() {}, switchTab() {}, componentLabel: type => type },
        BuilderSettingsPanel: {
            openSettingTabs: {}, settingTabStateKey: (id, tab) => `${id}:${tab}`,
            render: (value, schema, id) => {
                observations.settings.push(id);
                panel.owner = id;
                panel.innerHTML = `Controls for ${value.props.text}`;
            }
        },
        BuilderOverlay: {
            show: (element, id) => { observations.toolbar = id; },
            hide: () => { observations.toolbar = null; }
        },
        BuilderStorage: { refreshDirtyState() {} },
        BuilderRenderManager: { requestRender: source => observations.renders.push(source) }
    });
    context.window = context;
    for (const [path, code] of source) vm.runInContext(`'use strict';\n${code}`, context, { filename: path });
    context.Builder = { findNodeById: id => context.BuilderNodeTraversal.findNodeById(id) };
    const rerender = () => {
        elements.clear();
        context.BuilderNodeTraversal.walk(context.BuilderStore.structure, value => {
            const element = {
                dataset: { nodeId: value.id },
                className: 'relative original-class',
                style: new Proxy({ position: 'relative', backgroundColor: 'orange' }, { set: noStyleMutation }),
                classList: { add: noStyleMutation, remove: noStyleMutation, toggle: noStyleMutation },
                scrollIntoView() {},
                getBoundingClientRect: () => ({ top: 100, left: 20, width: 400, height: 100 }),
                closest: selector => selector === '#canvas' ? canvas : selector === '[data-node-id]' ? element : null
            };
            elements.set(value.id, element);
        });
        context.BuilderSelectionManager.restoreVisuals();
    };
    context.BuilderCanvas = { async render() { rerender(); } };
    context.BuilderEventBus.on(context.BuilderEvents.SELECTION_CHANGED, id => observations.selections.push(id));
    context.BuilderStore.setStructure(structuredClone(structure));
    rerender();
    context.BuilderHistory.init();
    return { c: context, elements, panel, observations, canvas, document, rerender,
        select: id => context.BuilderSelectionManager.select(id),
        order: (id = 'section') => plain(context.Builder.findNodeById(id).children.map(value => value.id)) };
}

function keyEvent(key, options = {}) {
    return { key, ctrlKey: false, metaKey: false, shiftKey: false, defaultPrevented: false,
        preventDefault() { this.defaultPrevented = true; }, ...options };
}

function dragEvent(target, clientY = 150) {
    return { target, clientY, defaultPrevented: false, stopped: false,
        preventDefault() { this.defaultPrevented = true; },
        stopPropagation() { this.stopped = true; },
        dataTransfer: { data: {}, setData(key, value) { this.data[key] = value; } } };
}

test('move boundaries, no-op and invalid destinations preserve structure and history', () => {
    const { c, select, order, observations } = fixture();
    select('a');
    const before = JSON.stringify(c.BuilderStore.structure);
    assert.equal(c.BuilderOverlayActions.moveUp('a'), false);
    assert.equal(c.BuilderOverlayActions.moveDown('c'), false);
    assert.equal(c.BuilderNodeMove.move({ nodeId: 'a', targetId: 'b', position: 'before' }), false);
    for (const options of [
        { nodeId: 'a', targetId: 'a' }, { nodeId: 'missing', targetId: 'b' },
        { nodeId: 'a', targetId: 'missing' }, { nodeId: 'section', targetId: 'a', position: 'inside' }
    ]) assert.equal(c.BuilderNodeMove.move(options), false);
    assert.equal(JSON.stringify(c.BuilderStore.structure), before);
    assert.equal(c.BuilderHistory.stack.length, 1);
    assert.equal(observations.renders.length, 0);
    assert.deepEqual(order(), ['a', 'b', 'c']);
});

test('move up/down preserve selected identity, and undo/redo restore order and selection', async () => {
    const { c, select, order, rerender, observations } = fixture();
    select('b');
    assert.equal(c.BuilderOverlayActions.moveUp('b'), true);
    assert.deepEqual(order(), ['b', 'a', 'c']);
    assert.equal(c.BuilderStore.selectedNodeId, 'b');
    assert.equal(c.BuilderStore.selectedElement, null);
    rerender();
    assert.equal(observations.toolbar, 'b');
    assert.equal(c.BuilderSelectionManager.settingsNodeId, 'b');
    assert.equal(c.BuilderOverlayActions.moveDown('b'), true);
    assert.deepEqual(order(), ['a', 'b', 'c']);
    await c.BuilderHistory.undo();
    assert.deepEqual(order(), ['b', 'a', 'c']);
    assert.equal(c.BuilderStore.selectedNodeId, 'b');
    await c.BuilderHistory.redo();
    assert.deepEqual(order(), ['a', 'b', 'c']);
    assert.equal(observations.toolbar, 'b');
});

test('cross-container moves and incompatible inside requests use existing placement rules', () => {
    const { c, order } = fixture();
    assert.equal(c.BuilderNodeMove.move({ nodeId: 'a', targetId: 'other', position: 'inside' }), true);
    assert.deepEqual(order(), ['b', 'c']);
    assert.deepEqual(order('other'), ['d', 'a']);
    const placement = c.BuilderNodeMove.resolve({ nodeId: 'b', targetId: 'd', position: 'inside' });
    assert.equal(placement.position, 'after');
    assert.equal(placement.parent.id, 'other');
    assert.equal(placement.normalized, true);
    assert.equal(c.BuilderNodeMove.move({ nodeId: 'b', targetId: 'd', position: 'inside' }), true);
    assert.deepEqual(order('other'), ['d', 'b', 'a']);
    assert.equal(c.Builder.findNodeById('d').children.length, 0);
});

test('duplicate recursively creates independent IDs and props, queues controls and survives undo/redo', async () => {
    const { c, select, rerender, observations, panel } = fixture();
    select('section');
    assert.equal(c.BuilderOverlayActions.duplicate('section'), true);
    const copyId = c.BuilderStore.selectedNodeId;
    const copy = c.Builder.findNodeById(copyId);
    assert.notEqual(copyId, 'section');
    assert.equal(panel.innerHTML, '');
    assert.equal(c.BuilderSelectionManager.settingsNodeId, null);
    assert.equal(observations.toolbar, null);
    assert.equal(c.BuilderStore.selectedElement, null);
    const originalIds = new Set(['section', 'a', 'b', 'c']);
    const copyIds = [copyId, ...copy.children.map(value => value.id)];
    assert.equal(new Set(copyIds).size, 4);
    for (const id of copyIds) assert.equal(originalIds.has(id), false);
    copy.children[0].props.design.desktop.color = 'red';
    assert.equal(c.Builder.findNodeById('a').props.design.desktop.color, 'blue');
    rerender();
    assert.equal(observations.toolbar, copyId);
    assert.equal(panel.owner, copyId);
    assert.equal(c.BuilderSelectionManager.settingsNodeId, copyId);
    assert.equal(c.BuilderHistory.stack.length, 2);
    await c.BuilderHistory.undo();
    assert.equal(c.Builder.findNodeById(copyId), null);
    assert.equal(c.BuilderStore.selectedNodeId, 'section');
    await c.BuilderHistory.redo();
    assert.equal(c.BuilderStore.selectedNodeId, copyId);
    assert.deepEqual(plain(c.Builder.findNodeById(copyId).children.map(value => value.id)), copyIds.slice(1));
    assert.equal(panel.owner, copyId);
});

test('pending text edits flush before a structural operation and remain separately undoable', async () => {
    const { c, select } = fixture();
    select('a');
    c.Builder.findNodeById('a').props.text = 'Changed first';
    c.BuilderHistory.schedulePush('text:a');
    c.BuilderOverlayActions.duplicate('a');
    const duplicateId = c.BuilderStore.selectedNodeId;
    assert.notEqual(duplicateId, 'a');
    assert.ok(c.Builder.findNodeById(duplicateId));
    assert.equal(c.Builder.findNodeById(duplicateId).props.text, 'Changed first');
    assert.equal(c.BuilderHistory.pendingTimer, null);
    assert.equal(c.BuilderHistory.stack.length, 3);
    await c.BuilderHistory.undo();
    assert.equal(c.Builder.findNodeById('a').props.text, 'Changed first');
    await c.BuilderHistory.undo();
    assert.equal(c.Builder.findNodeById('a').props.text, 'a');
});

test('pending edits do not leave move destinations referencing detached normalized arrays', async () => {
    const { c, select, order } = fixture();
    select('a');
    c.Builder.findNodeById('a').props.text = 'Edited before move';
    c.BuilderHistory.schedulePush('text:a');
    assert.equal(c.BuilderOverlayActions.moveDown('a'), true);
    assert.deepEqual(order(), ['b', 'a', 'c']);
    assert.equal(c.Builder.findNodeById('a').props.text, 'Edited before move');
    assert.equal(c.BuilderHistory.stack.length, 3);
    await c.BuilderHistory.undo();
    assert.deepEqual(order(), ['a', 'b', 'c']);
    assert.equal(c.Builder.findNodeById('a').props.text, 'Edited before move');
    await c.BuilderHistory.undo();
    assert.equal(c.Builder.findNodeById('a').props.text, 'a');
});

test('palette drop after pending edits resolves the live collection and has a separate undo step', async () => {
    const { c, select, elements, order } = fixture();
    select('a');
    c.Builder.findNodeById('a').props.text = 'Edited before insertion';
    c.BuilderHistory.schedulePush('text:a');
    c.BuilderDragState.setType('text');
    const event = dragEvent(elements.get('b'));
    c.BuilderDragDrop.allowDrop(event);
    assert.equal(event.dataTransfer.dropEffect, 'copy');
    assert.equal(c.BuilderDragDrop.drop(event), true);
    const insertedId = c.BuilderStore.selectedNodeId;
    assert.ok(c.Builder.findNodeById(insertedId));
    assert.deepEqual(order(), ['a', 'b', insertedId, 'c']);
    assert.equal(c.BuilderHistory.stack.length, 3);
    assert.equal(c.BuilderDragState.componentType, null);
    await c.BuilderHistory.undo();
    assert.deepEqual(order(), ['a', 'b', 'c']);
    assert.equal(c.Builder.findNodeById('a').props.text, 'Edited before insertion');
    await c.BuilderHistory.undo();
    assert.equal(c.Builder.findNodeById('a').props.text, 'a');
});

test('structural actions do not mutate the tree during an in-progress history restore', () => {
    const { c, select, elements } = fixture();
    select('a');
    c.BuilderHistory.isRestoring = true;
    const before = JSON.stringify(c.BuilderStore.structure);
    assert.equal(c.BuilderOverlayActions.duplicate('a'), false);
    assert.equal(c.BuilderOverlayActions.delete('a'), false);
    assert.equal(c.BuilderNodeMove.move({ nodeId: 'a', targetId: 'c', position: 'after' }), false);
    assert.equal(c.BuilderDragReorder.start(dragEvent(elements.get('a')), 'a'), false);
    c.BuilderDragState.setType('text');
    assert.equal(c.BuilderDragDrop.drop(dragEvent(elements.get('b'))), false);
    assert.equal(JSON.stringify(c.BuilderStore.structure), before);
    assert.equal(c.BuilderHistory.stack.length, 1);
    assert.equal(c.BuilderDragState.componentType, null);
});

test('toolbar deletion selects next, previous, parent or clears the final selection', async () => {
    const { c, select, rerender, observations } = fixture([node('section', 'section', [node('a'), node('b')])]);
    select('a');
    assert.equal(c.BuilderOverlayActions.delete('a'), true);
    assert.equal(c.BuilderStore.selectedNodeId, 'b');
    await c.BuilderHistory.undo();
    assert.equal(c.BuilderStore.selectedNodeId, 'a');
    select('b');
    c.BuilderOverlayActions.delete('b');
    assert.equal(c.BuilderStore.selectedNodeId, 'a');
    c.BuilderOverlayActions.delete('a');
    assert.equal(c.BuilderStore.selectedNodeId, 'section');
    c.BuilderOverlayActions.delete('section');
    rerender();
    assert.equal(c.BuilderStore.selectedNodeId, null);
    assert.equal(c.BuilderSelectionManager.settingsNodeId, null);
    assert.equal(observations.toolbar, null);
    assert.equal(observations.confirmations.length, 0, 'leaf deletion does not request confirmation');
});

test('toolbar and keyboard share child deletion confirmation, cancellation and history', async () => {
    const { c, select, observations } = fixture();
    select('section');
    c.confirm = message => { observations.confirmations.push(message); return false; };
    const before = JSON.stringify(c.BuilderStore.structure);
    assert.equal(c.BuilderOverlayActions.delete('section'), false);
    const cancelKey = keyEvent('Delete');
    assert.equal(c.BuilderHistoryKeyboard.handle(cancelKey), false);
    assert.equal(cancelKey.defaultPrevented, true);
    assert.equal(JSON.stringify(c.BuilderStore.structure), before);
    assert.equal(c.BuilderHistory.stack.length, 1);
    assert.equal(observations.confirmations.length, 2);
    assert.equal(observations.confirmations[0], observations.confirmations[1]);
    c.confirm = () => true;
    assert.equal(c.BuilderHistoryKeyboard.handle(keyEvent('Backspace')), true);
    assert.equal(c.Builder.findNodeById('section'), null);
    assert.equal(c.BuilderStore.selectedNodeId, 'other');
    await c.BuilderHistoryKeyboard.handle(keyEvent('z', { ctrlKey: true }));
    assert.equal(c.BuilderStore.selectedNodeId, 'section');
    await c.BuilderHistoryKeyboard.handle(keyEvent('z', { metaKey: true, shiftKey: true }));
    assert.equal(c.BuilderStore.selectedNodeId, 'other');
});

test('keyboard does not delete components or intercept undo while typing or inside a dialog', () => {
    const { c, select, document } = fixture();
    select('a');
    for (const key of ['Delete', 'Backspace', 'z', 'y']) {
        document.activeElement = { closest: selector => ({ matchedBy: selector }) };
        const event = keyEvent(key, { ctrlKey: ['z', 'y'].includes(key) });
        assert.equal(c.BuilderHistoryKeyboard.handle(event), undefined);
        assert.equal(event.defaultPrevented, false);
    }
    document.activeElement = null;
    c.BuilderHistoryKeyboard.handle(keyEvent('Delete', { defaultPrevented: true }));
    assert.ok(c.Builder.findNodeById('a'));
    assert.equal(c.BuilderHistory.stack.length, 1);
});

test('selection survives a canvas rerender without rebuilding focused controls', () => {
    const { c, select, rerender, elements, panel, observations } = fixture();
    select('b');
    const oldElement = elements.get('b');
    const calls = observations.settings.length;
    panel.innerHTML = 'Uncommitted control display';
    rerender();
    assert.notEqual(c.BuilderStore.selectedElement, oldElement);
    assert.equal(c.BuilderStore.selectedElement, elements.get('b'));
    assert.equal(observations.settings.length, calls);
    assert.equal(panel.innerHTML, 'Uncommitted control display');
    assert.equal(observations.toolbar, 'b');
    assert.equal(c.BuilderSelectionManager.settingsNodeId, 'b');
    assert.equal(elements.get('b').dataset.builderOutline, 'active');
    assert.equal(elements.get('section').dataset.builderOutline, 'parent');
    assert.equal(c.BuilderSelectionManager.selectParent(), true);
    assert.equal(c.BuilderStore.selectedNodeId, 'section');
    assert.equal(observations.selections.at(-1), 'section');
    assert.equal(c.BuilderSelectionManager.selectParent(), false);
});

test('selection, parent outlines and drag previews never mutate authored classes or styles', () => {
    const { c, select, elements } = fixture();
    const element = elements.get('a');
    select('a');
    c.BuilderDragPreview.show(element, 'before');
    assert.equal(element.dataset.builderDragPreview, 'before');
    c.BuilderDragPreview.hide();
    assert.equal(element.dataset.builderOutline, 'active');
    select('b');
    assert.equal(element.dataset.builderOutline, undefined);
    c.BuilderSelectionManager.clear();
    for (const value of elements.values()) {
        assert.equal(value.className, 'relative original-class');
        assert.equal(value.style.position, 'relative');
        assert.equal(value.style.backgroundColor, 'orange');
        assert.equal(value.dataset.builderOutline, undefined);
    }
});

test('drag preview and drop use the same normalized target and preserve selected identity', () => {
    const { c, select, elements, order, rerender, observations } = fixture();
    select('a');
    const sourceElement = elements.get('a');
    const start = dragEvent(sourceElement);
    assert.equal(c.BuilderDragReorder.start(start, 'a'), true);
    assert.equal(start.dataTransfer.data['reorder-node-id'], 'a');
    const drop = dragEvent(elements.get('d')); // Inside a leaf normalizes to after it.
    c.BuilderDragDrop.allowDrop(drop);
    assert.equal(drop.dataTransfer.dropEffect, 'move');
    assert.equal(c.BuilderDragPreview.activeElement.dataset.nodeId, 'd');
    assert.equal(c.BuilderDragPreview.activeElement.dataset.builderDragPreview, 'after');
    assert.equal(c.BuilderDragDrop.drop(drop), true);
    assert.deepEqual(order(), ['b', 'c']);
    assert.deepEqual(order('other'), ['d', 'a']);
    assert.equal(c.BuilderStore.selectedNodeId, 'a');
    assert.equal(sourceElement.dataset.builderDragging, undefined);
    assert.equal(c.BuilderDragState.draggedNodeId, null);
    assert.equal(c.BuilderDragPreview.activeElement, null);
    rerender();
    assert.equal(observations.toolbar, 'a');
});

test('invalid drag or cancelled drag clears preview/state and does not add history', () => {
    const { c, select, elements } = fixture();
    select('section');
    const before = JSON.stringify(c.BuilderStore.structure);
    c.BuilderDragReorder.start(dragEvent(elements.get('section')), 'section');
    const event = dragEvent(elements.get('a'));
    c.BuilderDragDrop.allowDrop(event);
    assert.equal(event.dataTransfer.dropEffect, 'none');
    assert.equal(c.BuilderDragDrop.drop(event), false);
    assert.equal(JSON.stringify(c.BuilderStore.structure), before);
    assert.equal(c.BuilderHistory.stack.length, 1);
    assert.equal(c.BuilderDragState.draggedNodeId, null);
    c.BuilderDragReorder.start(dragEvent(elements.get('a')), 'a');
    c.BuilderDragPreview.show(elements.get('other'), 'inside');
    c.BuilderDragReorder.end();
    assert.equal(c.BuilderDragPreview.activeElement, null);
    assert.equal(elements.get('a').dataset.builderDragging, undefined);
    assert.equal(c.BuilderStore.selectedNodeId, 'a');
});

test('compact toolbar escapes real labels/IDs with HtmlEscape receiver intact', () => {
    const { c } = fixture();
    const id = 'node_"<&';
    const label = 'Container <x> & "quoted"';
    const capabilities = {
        node: { id, type: 'container', props: { gridSpan: 4 } },
        parent: { type: 'container', props: { display: 'grid' } },
        canDrag: true, canMoveUp: false, canMoveDown: true, canDuplicate: true, canDelete: true
    };
    const html = c.BuilderOverlayUI.render(id, label, {}, 'selected', capabilities);
    assert.match(html, /role="toolbar"/);
    assert.match(html, /Container &lt;x&gt; &amp; &quot;quoted&quot;/);
    assert.match(html, /data-target-node-id="node_&quot;&lt;&amp;"/);
    assert.match(html, /data-builder-drag-handle/);
    assert.match(html, /data-action="overlay-select-parent"/);
    assert.match(html, /<option value="4" selected>/);
    assert.doesNotMatch(html, /data-node-id=/, 'toolbar must not impersonate a canvas node');
    assert.doesNotMatch(html, /undefined/);
    assert.match(c.BuilderOverlayUI.button('up', 'overlay-move-up', id, 'Move up', true), /disabled aria-disabled="true"/);
});
