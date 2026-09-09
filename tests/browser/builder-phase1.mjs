/**
 * Phase 1 integration regression: actual builder ES modules in Chromium.
 * The HTTP fixture replaces ONLY the server-rendered component HTML. It does
 * not authenticate, save a page, or read/write the application database.
 * Run: node tests/browser/builder-phase1.mjs
 * Optional: PLAYWRIGHT_MODULE_PATH and BROWSER_EXECUTABLE_PATH.
 */
import assert from 'node:assert/strict';
import { createServer } from 'node:http';
import { readFile, mkdir, writeFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import { resolve, dirname, extname, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';

const repo = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const require = createRequire(import.meta.url);
const playwrightPath = process.env.PLAYWRIGHT_MODULE_PATH?.trim() || 'playwright';
let chromium;

try {
    ({ chromium } = require(playwrightPath));
} catch (error) {
    throw new Error(
        'Playwright is required for this optional Chromium check. Install it locally or set PLAYWRIGHT_MODULE_PATH to an existing Playwright module.',
        { cause: error },
    );
}
const output = resolve(repo, 'output/builder-phase1');
await mkdir(output, { recursive: true });

const node = (id, type, props = {}, children = []) => ({ id, type, props, children });
const initialStructure = [
    node('hero', 'hero', { text: 'Travel without limits' }),
    node('section', 'section', {}, [
        node('content', 'container', { containerRole: 'content' }, [
            node('heading', 'heading', { text: 'Explore the world' }),
            node('text', 'text', { text: 'Design your next journey.' }),
            node('button', 'button', { text: 'Start planning' }),
        ]),
    ]),
    node('cta', 'cta-banner', { text: 'Found somewhere inspiring?' }),
    node('footer', 'text', { text: 'A final independent component' }),
];
const components = ['hero', 'section', 'container', 'heading', 'text', 'button', 'cta-banner']
    .map(type => ({ type, name: type, schema_json: { tabs: { content: {
        title: 'Content', fields: { text: { type: 'text', label: 'Text', default: '' } },
    } } } }));
const escape = value => String(value ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
})[c]);

function renderNodes(nodes) {
    return nodes.map(item => {
        const child = renderNodes(item.children || []);
        const content = `<span class="fixture-content">${escape(item.props.text || item.type)}</span>`;
        const styles = ['--fixture-original: 1'];
        if (Number(item.props.minHeight) > 0) styles.push(`min-height: ${Number(item.props.minHeight)}px`);
        if (Number(item.props.paddingTop) > 0) styles.push(`padding-top: ${Number(item.props.paddingTop)}px`);
        if (Number(item.props.paddingBottom) > 0) styles.push(`padding-bottom: ${Number(item.props.paddingBottom)}px`);
        const inner = item.type === 'hero'
            ? `<div class="absolute fixture-hero-shade"></div><div class="relative fixture-hero-copy">${content}</div>`
            : `${content}${child}`;
        return `<div data-node-id="${escape(item.id)}" data-node-type="${escape(item.type)}"
            data-drag-action="reorder" draggable="true"
            class="fixture-node fixture-${escape(item.type)} relative"
            style="${styles.join('; ')}">${inner}</div>`;
    }).join('');
}

function hasInvalidSectionMaxWidth(nodes) {
    return nodes.some(item => (
        item?.type === 'section'
        && item.props?.maxWidth !== undefined
        && typeof item.props.maxWidth !== 'number'
    ) || hasInvalidSectionMaxWidth(item?.children || []));
}

let compiledCss = '';
try {
    const manifest = JSON.parse(await readFile(resolve(repo, 'public/build/manifest.json'), 'utf8'));
    const asset = manifest['resources/css/app.css'];
    if (asset) compiledCss = `<link rel="stylesheet" href="/public/build/${asset.file}">`;
} catch { /* The two production interaction styles below are always loaded. */ }
const html = `<!doctype html><html><head><meta charset="UTF-8"><meta name="csrf-token" content="fixture-only">
<meta name="viewport" content="width=device-width, initial-scale=1"><title>Builder Phase 1 isolated browser validation</title>
${compiledCss}<link rel="stylesheet" href="/resources/css/components/builder-toolbar.css">
<link rel="stylesheet" href="/resources/css/components/builder-interaction.css">
<style>
* {box-sizing:border-box} body {margin:0;font:14px Arial,sans-serif;color:#1e293b;background:#f1f5f9}
.hidden {display:none!important} .relative {position:relative} .absolute {position:absolute}
#builder-topbar {height:58px;background:white;border-bottom:1px solid #cbd5e1;display:flex;align-items:center;gap:18px;padding:12px}
#fixture-layout {display:grid;grid-template-columns:265px minmax(0,1fr) 245px;height:calc(100vh - 58px)}
#left-panel,#right-panel {background:white;overflow:auto;border:1px solid #e2e8f0}
#builder-panel-title {padding:16px;font-weight:bold}.left-tabs {display:flex}.left-tab {padding:12px}
#builder-workspace {overflow:auto;padding:24px 16px;display:flex;flex-direction:column;align-items:center;min-width:0;position:relative}
#canvas-wrapper {position:relative;width:100%;flex-shrink:0} #canvas {min-height:1200px;background:white}
#builder-overlay-root {position:absolute;inset:0;pointer-events:none;z-index:50}
.fixture-node {box-sizing:border-box}.fixture-hero {min-height:190px;background:#1d5a69;color:white;padding:38px;font-size:28px}
.fixture-hero-shade {inset:0;background:rgba(0,0,0,.3)}.fixture-hero-copy {z-index:1}
.fixture-section {padding:30px 20px;background:#fff9ed}.fixture-container {padding:20px;background:white;border:1px solid #ddd}
.fixture-heading {padding:16px;font-size:25px;background:#eef6ff}.fixture-text {padding:16px;min-height:58px;background:#fff}
.fixture-button {margin:8px 16px;padding:14px;background:#1d4ed8;color:white;width:max-content}
.fixture-cta-banner {min-height:150px;padding:32px;background:#c84c2e;color:white;font-size:26px}
#layers-panel {padding:12px 4px} #layers-tree svg {width:14px;height:14px} .layer-item{display:flex;align-items:center;gap:6px;padding:7px;cursor:pointer}
.layer-children{margin-left:12px} .bg-indigo-500{background:#6366f1}.text-white{color:white}
#settings-panel input,#settings-panel select {max-width:100%;border:1px solid #cbd5e1;padding:7px}
#builder-render-feedback {position:sticky;top:8px;z-index:70;width:min(680px,100%);padding:12px;background:#fef2f2;border:1px solid #fecaca;justify-content:space-between}
#builder-template-library,#builder-section-layout-picker {position:fixed;inset:0;z-index:80;background:rgba(15,23,42,.45);align-items:center;justify-content:center}
#builder-template-grid,#builder-section-layout-picker>div {background:white;padding:24px;min-width:320px}
body.narrow #fixture-layout {grid-template-columns:minmax(0,1fr)} body.narrow #left-panel,body.narrow #right-panel {display:none}
body.narrow #builder-workspace {padding:12px 8px} #fixture-caption {font-size:12px;color:#475569}
</style></head><body>
<header id="builder-topbar"><strong>Site Builder</strong><span id="fixture-caption">Isolated Phase 1 validation — no site data saved</span>
<button data-action="history-undo">Undo</button><button data-action="history-redo">Redo</button><span id="builder-save-status"></span></header>
<main id="fixture-layout"><aside id="left-panel"><div id="builder-panel-title" data-page-title="Fixture">Fixture</div>
<div class="left-tabs"><button class="left-tab" data-action="switch-tab" data-tab="page">Page</button><button class="left-tab" data-action="switch-tab" data-tab="controls">Controls</button><button class="left-tab" data-action="switch-tab" data-tab="widgets">Widgets</button></div>
<div class="left-tab-content" id="tab-page"><input id="builder-page-title" value="Fixture"><input id="builder-page-slug" value="fixture"></div>
<div class="left-tab-content" id="tab-controls"><div id="settings-panel"></div></div>
<div class="left-tab-content" id="tab-widgets"><button draggable="true" data-drag-action="component" data-component="text">Text</button></div></aside>
<div id="builder-workspace">
<div id="builder-render-feedback" data-builder-floating-ui="true" role="alert" class="hidden"><span data-builder-render-message></span><span><button data-action="retry-canvas-render">Retry</button><button data-action="reload-builder" class="hidden">Reload</button></span></div>
<div id="canvas-wrapper"><div id="canvas"></div><div id="builder-overlay-root"></div></div>
<div id="builder-template-library" class="hidden"><div><button data-template-tab="blocks" data-action="template-library-tab">Sections</button><div id="builder-template-categories"></div><div id="builder-template-grid"></div><div id="builder-template-empty" class="hidden"></div></div></div>
<div id="builder-section-layout-picker" class="hidden"><div><button data-action="choose-section-layout" data-section-layout="normal">Normal section</button></div></div>
</div>
<aside id="right-panel"><div id="layers-panel"><strong>Layers</strong><div id="layers-tree"></div><div id="layers-empty">No components</div></div></aside></main>
<script>window.initialStructure=${JSON.stringify(initialStructure)};window.builderComponents=${JSON.stringify(components)};window.pageId='fixture-never-save';window.builderMenuItems=[];window.builderSavedBlocksUrl='/fixture/saved-blocks';</script>
<script type="module" src="/resources/js/builder/index.js"></script></body></html>`;

let renderRequests = 0;
let renderDelayMs = 0;
let renderFailureStatus = null;
const server = createServer(async (req, res) => {
    try {
        const pathname = new URL(req.url, 'http://localhost').pathname;
        if (pathname === '/') { res.setHeader('Content-Type', 'text/html'); res.end(html); return; }
        if (pathname === '/fixture/saved-blocks' && req.method === 'GET') {
            res.setHeader('Content-Type', 'application/json'); res.end('[]'); return;
        }
        if (pathname === '/builder/render' && req.method === 'POST') {
            let body = ''; for await (const chunk of req) body += chunk;
            renderRequests++;
            if (renderDelayMs > 0) await new Promise(resolve => setTimeout(resolve, renderDelayMs));
            if (renderFailureStatus) {
                const status = renderFailureStatus;
                renderFailureStatus = null;
                res.writeHead(status, { 'Content-Type': 'text/plain' });
                res.end(`Forced render failure ${status}`);
                return;
            }
            const structure = JSON.parse(body).structure;
            const rendered = hasInvalidSectionMaxWidth(structure)
                ? '<!-- Invalid type for maxWidth -->'
                : renderNodes(structure);
            res.setHeader('Content-Type', 'text/html'); res.end(rendered); return;
        }
        if (req.method !== 'GET') throw new Error(`Unexpected mutating request: ${req.method} ${pathname}`);
        if (pathname === '/favicon.ico') { res.writeHead(204); res.end(); return; }
        let filename = resolve(repo, '.' + decodeURIComponent(pathname));
        if (!filename.startsWith(repo + sep)) throw new Error('Path outside fixture root');
        if (!extname(filename)) filename += '.js';
        if (!existsSync(filename) && /\.(avif|gif|jpe?g|png|svg|webp)$/i.test(filename)) {
            res.writeHead(204);
            res.end();
            return;
        }
        res.setHeader('Content-Type', extname(filename) === '.css' ? 'text/css' : 'application/javascript');
        res.end(await readFile(filename));
    } catch (error) { res.writeHead(500); res.end(String(error)); }
});
await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
const baseUrl = `http://127.0.0.1:${server.address().port}`;
const launchOptions = { headless: true };
const executable = process.env.BROWSER_EXECUTABLE_PATH;
if (executable) launchOptions.executablePath = executable;
let browser;
try { browser = await chromium.launch(launchOptions); }
catch (error) {
    const edge = 'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe';
    if (!existsSync(edge)) throw error;
    browser = await chromium.launch({ ...launchOptions, executablePath: edge });
}
const context = await browser.newContext({ viewport: { width: 1440, height: 1000 }, reducedMotion: 'reduce' });
const page = await context.newPage();
page.setDefaultTimeout(8000);
const pageErrors = [];
page.on('pageerror', error => pageErrors.push(error.stack || error.message));
page.on('console', message => { if (message.type() === 'error') pageErrors.push(message.text()); });
const checks = [];
let dialogDecision = true;
let dialogCount = 0;
page.on('dialog', async dialog => { dialogCount++; await (dialogDecision ? dialog.accept() : dialog.dismiss()); });
const canvasNode = id => page.locator(`#canvas [data-node-id="${id}"]`);
const toolbar = page.locator('[data-builder-overlay-toolbar]');
const action = value => toolbar.locator(`[data-action="${value}"]`);
const waitRender = async () => {
    await page.waitForFunction(() => !BuilderRenderManager.scheduled && !BuilderRenderManager.inFlight && !BuilderHistory.isRestoring);
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
};
const state = () => page.evaluate(() => ({
    store: BuilderStore.selectedNodeId,
    canvas: document.querySelector('#canvas [data-builder-outline="active"]')?.dataset.nodeId || null,
    controls: BuilderSelectionManager.settingsNodeId,
    controlTargets: [...new Set([...document.querySelectorAll('#settings-panel [data-target-node-id]')].map(e => e.dataset.targetNodeId))],
    layer: document.querySelector('#layers-tree .bg-indigo-500')?.dataset.layerNode || null,
    overlay: BuilderOverlay.currentNodeId,
    toolbarTargets: [...new Set([...document.querySelectorAll('#builder-overlay-root [data-target-node-id]')].map(e => e.dataset.targetNodeId))],
}));
const expectSelection = async id => {
    await waitRender();
    await page.waitForFunction(id => document.querySelector('#layers-tree .bg-indigo-500')?.dataset.layerNode === id, id);
    const actual = await state();
    for (const key of ['store', 'canvas', 'controls', 'layer', 'overlay']) assert.equal(actual[key], id, `Selection ${key}`);
    assert.deepEqual(actual.toolbarTargets, [id]);
    assert.deepEqual(actual.controlTargets, [id]);
};
const check = async (name, task) => {
    try { await task(); checks.push({ name, passed: true }); console.log(`PASS ${name}`); }
    catch (error) { checks.push({ name, passed: false, error: String(error) }); console.error(`FAIL ${name}: ${error}`); throw error; }
};
const selectLayer = async id => { await page.locator(`[data-layer-node="${id}"]`).click(); await expectSelection(id); };
const addBlankSection = async () => {
    await page.locator('#canvas [data-action="open-template-library"]').last().click();
    await page.locator('#builder-template-grid [data-action="add-blank-section"]').click();
    await page.locator('#builder-section-layout-picker [data-section-layout="normal"]').click();
    return page.evaluate(() => BuilderStore.selectedNodeId);
};
const consumeExpectedRenderErrors = (start, status) => {
    const errors = pageErrors.splice(start);
    assert.ok(errors.length > 0, `Expected browser diagnostics for forced status ${status}`);
    assert.ok(errors.some(error => error.includes(String(status))), `Missing status ${status} in browser diagnostics`);
    assert.ok(errors.every(error => error.includes(String(status)) || error.includes('Failed to load resource')),
        `Unexpected browser diagnostic while testing ${status}: ${errors.join('\n')}`);
};

try {
    await page.goto(baseUrl);
    await canvasNode('heading').waitFor();
    const originalHero = await canvasNode('hero').evaluate(el => ({ className: el.className, style: el.getAttribute('style'), position: getComputedStyle(el).position }));
    await check('Canvas selection, outline, Controls, Layers and single compact toolbar agree', async () => {
        await canvasNode('heading').click(); await expectSelection('heading');
        assert.equal(await toolbar.count(), 1);
        assert.equal(await canvasNode('heading').evaluate(el => getComputedStyle(el).outlineStyle), 'solid');
        assert.equal(await toolbar.evaluate(el => getComputedStyle(el).visibility), 'visible');
    });
    await check('Hover does not replace the selection or toolbar target', async () => {
        await canvasNode('text').hover(); await expectSelection('heading');
        assert.equal(await canvasNode('text').getAttribute('data-builder-hover-state'), 'hover');
    });
    await check('Layers selection targets the same component in all surfaces', async () => { await selectLayer('text'); });
    await check('Move up and down preserve selection through server rerender', async () => {
        await action('overlay-move-up').click(); await expectSelection('text');
        assert.deepEqual(await page.evaluate(() => Builder.findNodeById('content').children.map(n => n.id)), ['text', 'heading', 'button']);
        assert.equal(await action('overlay-move-up').isDisabled(), true);
        await action('overlay-move-down').click(); await expectSelection('text');
        assert.deepEqual(await page.evaluate(() => Builder.findNodeById('content').children.map(n => n.id)), ['heading', 'text', 'button']);
    });
    let copyId;
    await check('Duplicate selects an independent copy and rebinds the Controls', async () => {
        await action('overlay-duplicate').click(); await waitRender();
        copyId = await page.evaluate(() => BuilderStore.selectedNodeId);
        assert.notEqual(copyId, 'text'); await expectSelection(copyId);
        const input = page.locator('#settings-panel [data-setting-field="text"]').first();
        await input.fill('Independent copied content'); await waitRender();
        assert.equal(await page.evaluate(() => Builder.findNodeById('text').props.text), 'Design your next journey.');
        assert.equal(await page.evaluate(id => Builder.findNodeById(id).props.text, copyId), 'Independent copied content');
        assert.equal(await input.inputValue(), 'Independent copied content');
    });
    await check('Same-node rerender preserves active Controls input and cursor', async () => {
        const input = page.locator('#settings-panel [data-setting-field="text"]').first();
        await input.focus();
        await input.evaluate(el => { el.dataset.focusSentinel = 'preserved'; el.setSelectionRange(3, 3); });
        assert.equal(await page.evaluate(() => BuilderCanvas.render('phase1-browser-check')), true);
        await expectSelection(copyId);
        assert.deepEqual(await input.evaluate(el => ({ marker: el.dataset.focusSentinel, focused: document.activeElement === el, cursor: el.selectionStart })), { marker: 'preserved', focused: true, cursor: 3 });
    });
    await check('Keyboard Delete while editing a field does not delete a component', async () => {
        const before = await page.evaluate(() => Builder.findNodeById('content').children.length);
        await page.keyboard.press('Delete'); await waitRender();
        assert.equal(await page.evaluate(() => Builder.findNodeById('content').children.length), before);
    });
    await check('Toolbar delete and Undo/Redo restore component and selection', async () => {
        await action('overlay-delete').click(); await waitRender();
        assert.equal(await page.evaluate(id => !!Builder.findNodeById(id), copyId), false);
        await page.locator('#builder-topbar [data-action="history-undo"]').click(); await expectSelection(copyId);
        await page.locator('#builder-topbar [data-action="history-redo"]').click(); await waitRender();
        assert.equal(await page.evaluate(id => !!Builder.findNodeById(id), copyId), false);
    });
    await check('Keyboard delete and keyboard Undo/Redo use the same action lifecycle', async () => {
        await selectLayer('text'); await canvasNode('text').click();
        await page.keyboard.press('Delete'); await waitRender();
        assert.equal(await page.evaluate(() => !!Builder.findNodeById('text')), false);
        await page.keyboard.press('Control+z'); await expectSelection('text');
        await page.keyboard.press('Control+Shift+z'); await waitRender();
        assert.equal(await page.evaluate(() => !!Builder.findNodeById('text')), false);
        await page.keyboard.press('Control+z'); await expectSelection('text');
    });
    await check('Select parent is explicit; deleting a group can be cancelled', async () => {
        await action('overlay-select-parent').click(); await expectSelection('content');
        const before = dialogCount; dialogDecision = false;
        await action('overlay-delete').click(); await expectSelection('content');
        assert.equal(dialogCount, before + 1);
        assert.equal(await page.evaluate(() => Builder.findNodeById('content').children.length), 3);
        dialogDecision = true;
    });
    await check('Native pointer drag from compact handle reorders without losing selection', async () => {
        await selectLayer('text');
        const handle = toolbar.locator('[data-builder-drag-handle]');
        const from = await handle.boundingBox(); const to = await canvasNode('heading').boundingBox();
        await page.mouse.move(from.x + from.width / 2, from.y + from.height / 2);
        await page.mouse.down();
        await page.mouse.move(from.x + from.width / 2 + 12, from.y + from.height / 2 + 12, { steps: 6 });
        await page.mouse.move(to.x + to.width / 2, to.y + 3, { steps: 12 });
        await page.mouse.move(to.x + to.width / 2 + 1, to.y + 4);
        await page.mouse.up(); await waitRender(); await expectSelection('text');
        assert.deepEqual(await page.evaluate(() => Builder.findNodeById('content').children.map(n => n.id)), ['text', 'heading', 'button']);
        assert.equal(await page.locator('[data-builder-drag-preview]').count(), 0);
        assert.equal(await page.locator('[data-builder-dragging]').count(), 0);
    });
    await check('No original component classes, inline styles or positioning changed', async () => {
        await selectLayer('hero'); await selectLayer('cta');
        const current = await canvasNode('hero').evaluate(el => ({ className: el.className, style: el.getAttribute('style'), position: getComputedStyle(el).position }));
        assert.deepEqual(current, originalHero);
        const shade = await canvasNode('hero').locator('.fixture-hero-shade').evaluate(el => ({ rect: el.getBoundingClientRect().toJSON(), parent: el.parentElement.getBoundingClientRect().toJSON() }));
        assert.equal(shade.rect.width, shade.parent.width); assert.equal(shade.rect.height, shade.parent.height);
        assert.equal(await canvasNode('cta').evaluate(el => getComputedStyle(el).backgroundColor), 'rgb(200, 76, 46)');
    });
    await check('Toolbar stays inside workspace; scroll reposition keeps the same DOM buttons', async () => {
        await selectLayer('text');
        await action('overlay-duplicate').evaluate(el => { el.dataset.identityMarker = 'stable'; el.focus(); });
        await page.locator('#builder-workspace').evaluate(el => { el.scrollTop += 40; }); await waitRender();
        assert.equal(await action('overlay-duplicate').getAttribute('data-identity-marker'), 'stable');
        const bounds = await toolbar.boundingBox(); const space = await page.locator('#builder-workspace').boundingBox();
        assert.ok(bounds.x >= space.x && bounds.x + bounds.width <= space.x + space.width);
        assert.ok(bounds.y >= space.y && bounds.y + bounds.height <= space.y + space.height);
        await page.screenshot({ path: resolve(output, 'desktop-selection.png') });
    });
    await check('Offscreen selection retains state and hides toolbar; it returns on scroll', async () => {
        await selectLayer('hero');
        await page.locator('#builder-workspace').evaluate(el => { el.scrollTop = el.scrollHeight; }); await waitRender();
        assert.equal(await toolbar.evaluate(el => getComputedStyle(el).visibility), 'hidden');
        assert.equal(await page.evaluate(() => BuilderStore.selectedNodeId), 'hero');
        await canvasNode('hero').scrollIntoViewIfNeeded(); await waitRender();
        assert.equal(await toolbar.evaluate(el => getComputedStyle(el).visibility), 'visible');
        await expectSelection('hero');
    });
    let delayedSectionId;
    await check('Add section waits for a render delayed beyond 350 ms, then selects and reveals it', async () => {
        await page.evaluate(() => {
            window.__builderScrollTargets = [];
            if (!window.__builderOriginalScrollIntoView) {
                window.__builderOriginalScrollIntoView = Element.prototype.scrollIntoView;
                Element.prototype.scrollIntoView = function (...args) {
                    window.__builderScrollTargets.push(this.dataset?.nodeId || null);
                    return window.__builderOriginalScrollIntoView.apply(this, args);
                };
            }
        });
        const rootsBefore = await page.evaluate(() => BuilderStore.structure.length);
        renderDelayMs = 700;
        delayedSectionId = await addBlankSection();
        await page.waitForTimeout(400);
        assert.equal(await canvasNode(delayedSectionId).count(), 0, 'Canvas must still be waiting for the delayed response');
        assert.equal(await page.locator(`[data-layer-node="${delayedSectionId}"]`).count(), 1);
        assert.equal(await page.locator('#builder-save-status').textContent(), 'Unsaved changes');
        await waitRender();
        renderDelayMs = 0;

        assert.equal(await page.evaluate(() => BuilderStore.structure.length), rootsBefore + 1);
        const childId = await page.evaluate(id => Builder.findNodeById(id).children[0].id, delayedSectionId);
        assert.equal(await canvasNode(delayedSectionId).count(), 1);
        assert.equal(await canvasNode(childId).count(), 1);
        assert.ok(await canvasNode(childId).evaluate(el => el.getBoundingClientRect().height >= 120));
        assert.ok((await canvasNode(childId).textContent()).includes('container'));
        assert.ok(await page.evaluate(id => window.__builderScrollTargets.includes(id), delayedSectionId));
        await expectSelection(delayedSectionId);
    });
    await check('Undo and Redo immediately after insertion restore structure and selection', async () => {
        await page.locator('#builder-topbar [data-action="history-undo"]').click();
        await waitRender();
        assert.equal(await page.evaluate(id => !!Builder.findNodeById(id), delayedSectionId), false);
        assert.equal(await canvasNode(delayedSectionId).count(), 0);

        await page.locator('#builder-topbar [data-action="history-redo"]').click();
        await expectSelection(delayedSectionId);
        assert.equal(await canvasNode(delayedSectionId).count(), 1);
        assert.equal(await page.locator('#builder-save-status').textContent(), 'Unsaved changes');
    });
    await check('A 500 response preserves the section and offers a successful Retry', async () => {
        const errorStart = pageErrors.length;
        renderFailureStatus = 500;
        const failedSectionId = await addBlankSection();
        await waitRender();

        assert.ok(await page.evaluate(id => !!Builder.findNodeById(id), failedSectionId));
        assert.equal(await canvasNode(failedSectionId).count(), 0);
        assert.equal(await page.locator(`[data-layer-node="${failedSectionId}"]`).count(), 1);
        assert.equal(await page.locator('#builder-save-status').textContent(), 'Unsaved changes');
        const feedback = page.locator('#builder-render-feedback');
        assert.equal(await feedback.getAttribute('data-render-status'), '500');
        assert.equal(await feedback.locator('[data-action="retry-canvas-render"]').isVisible(), true);
        assert.equal(await feedback.locator('[data-action="reload-builder"]').isVisible(), false);
        consumeExpectedRenderErrors(errorStart, 500);

        await feedback.locator('[data-action="retry-canvas-render"]').click();
        await waitRender();
        assert.equal(await canvasNode(failedSectionId).count(), 1);
        assert.equal(await feedback.isHidden(), true);
        await expectSelection(failedSectionId);
    });
    await check('A 419 response preserves state and explicitly requests a reload', async () => {
        const errorStart = pageErrors.length;
        renderFailureStatus = 419;
        const expiredSectionId = await addBlankSection();
        await waitRender();

        assert.ok(await page.evaluate(id => !!Builder.findNodeById(id), expiredSectionId));
        assert.equal(await canvasNode(expiredSectionId).count(), 0);
        assert.equal(await page.locator('#builder-save-status').textContent(), 'Unsaved changes');
        const feedback = page.locator('#builder-render-feedback');
        assert.equal(await feedback.getAttribute('data-render-status'), '419');
        assert.ok((await feedback.locator('[data-builder-render-message]').textContent()).includes('session has expired'));
        assert.equal(await feedback.locator('[data-action="retry-canvas-render"]').isVisible(), false);
        assert.equal(await feedback.locator('[data-action="reload-builder"]').isVisible(), true);
        consumeExpectedRenderErrors(errorStart, 419);
    });
    await check('Travel hero template renders its complete section and synchronizes selection', async () => {
        await page.evaluate(() => BuilderCanvas.retryRender());
        await waitRender();
        const rootsBefore = await page.evaluate(() => BuilderStore.structure.length);
        await page.locator('#canvas [data-action="open-template-library"]').last().click();
        await page.locator('#builder-template-grid [data-template-id="travel-hero"]').click();
        await page.waitForFunction(before => BuilderStore.structure.length === before + 1, rootsBefore);
        await waitRender();

        const heroId = await page.evaluate(() => BuilderStore.structure.at(-1).id);
        const hero = canvasNode(heroId);
        assert.equal(await hero.count(), 1);
        assert.ok((await hero.textContent()).includes('Discover your next journey'));
        assert.ok((await hero.textContent()).includes('Start planning'));
        assert.deepEqual(await page.evaluate(id => {
            const types = [];
            BuilderNodeTraversal.walk([Builder.findNodeById(id)], node => types.push(node.type));
            return types;
        }, heroId), ['section', 'container', 'container', 'container', 'text', 'text', 'button', 'container', 'image']);
        await expectSelection(heroId);
        assert.equal(await page.locator('#builder-render-feedback').isHidden(), true);
    });
    await check('390px workspace wraps compact toolbar without clipping controls', async () => {
        await page.evaluate(() => document.body.classList.add('narrow'));
        await page.setViewportSize({ width: 390, height: 844 });
        await canvasNode('text').scrollIntoViewIfNeeded(); await canvasNode('text').click(); await waitRender();
        const bounds = await toolbar.boundingBox(); const space = await page.locator('#builder-workspace').boundingBox();
        assert.ok(bounds.x >= space.x && bounds.x + bounds.width <= space.x + space.width);
        for (const button of await toolbar.locator('button').all()) {
            const b = await button.boundingBox(); assert.ok(b.width >= 32 && b.height >= 32);
            assert.ok(b.x >= bounds.x && b.x + b.width <= bounds.x + bounds.width);
        }
        await page.screenshot({ path: resolve(output, 'mobile-selection.png') });
    });
    await check('No uncaught or console errors, including overlay HTML escaping', async () => { assert.deepEqual(pageErrors, []); });
} catch (error) {
    await page.screenshot({ path: resolve(output, 'failure.png'), fullPage: true });
    console.error(error.stack);
    process.exitCode = 1;
} finally {
    await writeFile(resolve(output, 'results.json'), JSON.stringify({ environment: 'Isolated Chromium frontend fixture; production JS modules; mocked render endpoint; no authenticated Laravel checks or database writes', checks, pageErrors, renderRequests }, null, 2));
    await browser.close();
    await new Promise(resolve => server.close(resolve));
}
