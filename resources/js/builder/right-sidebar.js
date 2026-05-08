/**
 * BuilderRightSidebar — Layers Panel
 *
 * Reads window.structure (or the live canvas) and renders
 * an interactive tree matching the reference UI in the images.
 *
 * Integrates with:
 *   - BuilderState.selectedElement  (highlights active row)
 *   - canvas mutations (MutationObserver)
 *   - BuilderDragDrop / BuilderCanvas element selection
 */

const BuilderRightSidebar = (() => {

    /* ── Config ───────────────────────────────────────────── */
    const TYPE_META = {
        section:   { badge: '§',  label: 'Section',   cls: 'type-section' },
        container: { badge: '[]', label: 'Container',  cls: 'type-container' },
        row:       { badge: '—',  label: 'Row',        cls: 'type-row' },
        column:    { badge: '|',  label: 'Column',     cls: 'type-column' },
        box:       { badge: '☐',  label: 'Box',        cls: 'type-box' },
        heading:   { badge: 'H',  label: 'Heading',    cls: 'type-heading' },
        text:      { badge: 'T',  label: 'Text',       cls: 'type-text' },
        image:     { badge: '🖼', label: 'Image',      cls: 'type-box' },
        button:    { badge: 'B',  label: 'Button',     cls: 'type-box' },
        hero:      { badge: '★',  label: 'Hero',       cls: 'type-section' },
    };

    const DEFAULT_META = { badge: '◇', label: 'Element', cls: 'type-box' };

    let collapsed   = false;
    let expandState = {};   // elementId → boolean (open/closed)
    let observer    = null;
    let refreshTimer = null;

    /* ── Helpers ───────────────────────────────────────────── */
    function getMeta(type) {
        return TYPE_META[(type || '').toLowerCase()] || DEFAULT_META;
    }

    function getLabel(el) {
        // prefer data-label, then element type, then tag
        if (el.dataset && el.dataset.label) return el.dataset.label;
        if (el.dataset && el.dataset.type)  return getMeta(el.dataset.type).label;
        return el.tagName ? el.tagName.toLowerCase() : 'element';
    }

    function getType(el) {
        return (el.dataset && el.dataset.type) ? el.dataset.type.toLowerCase() : 'box';
    }

    function getElemId(el) {
        if (!el.dataset.layerId) {
            el.dataset.layerId = 'ly-' + Math.random().toString(36).slice(2, 7);
        }
        return el.dataset.layerId;
    }

    function isCanvasElement(el) {
        return el && el.dataset && (el.dataset.type || el.dataset.component);
    }

    function getCanvasChildren(parentEl) {
        return Array.from(parentEl.children).filter(isCanvasElement);
    }

    /* ── Build tree DOM ────────────────────────────────────── */
    function buildTree(elements, depth, fragment) {
        elements.forEach(el => {
            const id       = getElemId(el);
            const type     = getType(el);
            const meta     = getMeta(type);
            const label    = getLabel(el);
            const children = getCanvasChildren(el);
            const hasKids  = children.length > 0;
            const isOpen   = expandState[id] !== false; // default open

            // ── Row ──
            const row = document.createElement('div');
            row.className = 'layer-item' + (BuilderState?.selectedElement === el ? ' active' : '');
            row.dataset.elemId = id;
            row.style.paddingLeft = (8 + depth * 14) + 'px';

            // Toggle arrow
            if (hasKids) {
                row.innerHTML += `
                    <span class="layer-toggle ${isOpen ? 'open' : ''}" data-toggle="${id}">
                        <svg viewBox="0 0 10 10" fill="currentColor"><path d="M3 2l4 3-4 3V2z"/></svg>
                    </span>`;
            } else {
                row.innerHTML += `<span class="layer-toggle-spacer"></span>`;
            }

            // Type badge
            row.innerHTML += `<span class="layer-type-badge ${meta.cls}">${meta.badge}</span>`;

            // Label
            row.innerHTML += `<span class="layer-label">${label}</span>`;

            // Eye (visibility toggle)
            row.innerHTML += `
                <button class="layer-eye" data-eye="${id}" title="Toggle visibility">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>`;

            // Click to select
            row.addEventListener('click', (e) => {
                if (e.target.closest('[data-toggle]')) {
                    toggleExpand(id, row, el, depth, fragment);
                    return;
                }
                if (e.target.closest('[data-eye]')) {
                    toggleVisibility(el, row);
                    return;
                }
                selectElement(el, row);
            });

            fragment.appendChild(row);

            // Recursively add children if open
            if (hasKids && isOpen) {
                buildTree(children, depth + 1, fragment);
            }
        });
    }

    function toggleExpand(id, row, el, depth, container) {
        expandState[id] = expandState[id] === false ? true : false;
        scheduleRefresh();
    }

    function toggleVisibility(el, row) {
        el.style.opacity = el.style.opacity === '0.3' ? '' : '0.3';
        el.style.pointerEvents = el.style.pointerEvents === 'none' ? '' : 'none';
    }

    function selectElement(el, row) {
        // Remove active from all
        document.querySelectorAll('#layers-tree .layer-item').forEach(r => r.classList.remove('active'));
        row.classList.add('active');

        // Tell builder to select this element
        if (typeof BuilderCanvas !== 'undefined' && BuilderCanvas.selectElement) {
            BuilderCanvas.selectElement(el);
        } else if (typeof BuilderSettings !== 'undefined' && BuilderSettings.select) {
            BuilderSettings.select(el);
        }

        // Scroll element into view in canvas
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /* ── Render ────────────────────────────────────────────── */
    function render() {
        const tree  = document.getElementById('layers-tree');
        const empty = document.getElementById('layers-empty');
        if (!tree) return;

        const canvas = document.getElementById('canvas');
        if (!canvas) return;

        const topLevel = getCanvasChildren(canvas);

        if (topLevel.length === 0) {
            tree.innerHTML = '';
            if (empty) empty.classList.remove('hidden');
            return;
        }

        if (empty) empty.classList.add('hidden');

        const frag = document.createDocumentFragment();
        buildTree(topLevel, 0, frag);

        tree.innerHTML = '';
        tree.appendChild(frag);
    }

    function scheduleRefresh() {
        clearTimeout(refreshTimer);
        refreshTimer = setTimeout(render, 80);
    }

    /* ── MutationObserver ──────────────────────────────────── */
    function observe() {
        const canvas = document.getElementById('canvas');
        if (!canvas || observer) return;

        observer = new MutationObserver(() => scheduleRefresh());
        observer.observe(canvas, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-type', 'data-label'] });
    }

    /* ── Toggle sidebar collapse ───────────────────────────── */
    function toggle() {
        const panel = document.getElementById('right-panel');
        if (!panel) return;
        collapsed = !collapsed;
        panel.classList.toggle('collapsed', collapsed);
    }

    /* ── Public highlightElement (called from canvas clicks) ── */
    function highlightElement(el) {
        // Ensure expanded path is visible first
        if (el) expandState[getElemId(el)] = true;
        render();
        // Then highlight
        setTimeout(() => {
            if (!el) return;
            const id = el.dataset.layerId;
            if (!id) return;
            const row = document.querySelector(`#layers-tree .layer-item[data-elem-id="${id}"]`);
            if (row) {
                document.querySelectorAll('#layers-tree .layer-item').forEach(r => r.classList.remove('active'));
                row.classList.add('active');
                row.scrollIntoView({ block: 'nearest' });
            }
        }, 90);
    }

    /* ── Init ──────────────────────────────────────────────── */
    function init() {
        render();
        observe();
    }

    return { init, render, toggle, highlightElement, scheduleRefresh };

})();

// Auto-init when DOM ready
document.addEventListener('DOMContentLoaded', () => BuilderRightSidebar.init());