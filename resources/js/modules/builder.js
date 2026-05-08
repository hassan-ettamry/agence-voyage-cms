/**
 * ==============================
 * GLOBAL STATE
 * ==============================
 */

// structure من DB أو فارغ
let structure = window.structure || [];

// component المختار
let selectedIndex = null;


/**
 * ==============================
 * INIT
 * ==============================
 */
document.addEventListener("DOMContentLoaded", () => {
    console.log("Builder loaded");

    if (structure.length) {
        renderPreview();
    }
});


/**
 * ==============================
 * ADD COMPONENT
 * ==============================
 */
window.addComponent = function(type) {
    console.log("Add component:", type);

    structure.push({
        type: type,
        props: {},
        children: []
    });

    renderPreview();
};


/**
 * ==============================
 * RENDER PREVIEW
 * ==============================
 */
window.renderPreview = function() {
    fetch('/builder/render', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(structure)
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById('canvas').innerHTML = html;

        attachClickEvents();
    })
    .catch(err => {
        console.error("Render error:", err);
    });
};


/**
 * ==============================
 * CLICK EVENTS (SELECT COMPONENT)
 * ==============================
 */
function attachClickEvents() {
    document.querySelectorAll('[data-index]').forEach(el => {

        el.style.border = '';

        el.onclick = (e) => {
            e.stopPropagation();

            selectedIndex = parseInt(el.dataset.index);

            console.log("Selected:", selectedIndex);

            highlightSelected();
            loadConfig();
        };
    });
}


/**
 * ==============================
 * HIGHLIGHT SELECTED
 * ==============================
 */
function highlightSelected() {
    document.querySelectorAll('[data-index]').forEach(el => {
        el.style.border = 'none';
    });

    let selectedEl = document.querySelector(`[data-index="${selectedIndex}"]`);

    if (selectedEl) {
        selectedEl.style.border = '2px solid blue';
    }
}


/**
 * ==============================
 * CONFIG PANEL
 * ==============================
 */
function loadConfig() {
    if (selectedIndex === null) return;

    let comp = structure[selectedIndex];

    if (!comp) return;

    let html = '';

    // HERO
    if (comp.type === 'hero') {
        html = `
            <label>Title</label><br/>
            <input 
                type="text" 
                value="${comp.props.title || ''}" 
                onchange="updateProp('title', this.value)"
            />
        `;
    }

    // TEXT
    if (comp.type === 'text') {
        html = `
            <label>Text</label><br/>
            <textarea 
                onchange="updateProp('text', this.value)"
            >${comp.props.text || ''}</textarea>
        `;
    }

    // SECTION
    if (comp.type === 'section') {
        html = `
            <label>Title</label><br/>
            <input 
                type="text" 
                value="${comp.props.title || ''}" 
                onchange="updateProp('title', this.value)"
            />

            <br/><br/>

            <label>Class</label><br/>
            <input 
                type="text" 
                value="${comp.props.class || ''}" 
                onchange="updateProp('class', this.value)"
            />
        `;
    }

    // DEFAULT
    if (!html) {
        html = `<p>No config available</p>`;
    }

    // render
    document.getElementById('config-panel').innerHTML = html;
}


/**
 * ==============================
 * UPDATE PROPS
 * ==============================
 */
function updateProp(key, value) {
    if (selectedIndex === null) return;

    structure[selectedIndex].props[key] = value;

    renderPreview();
}


/**
 * ==============================
 * DELETE COMPONENT
 * ==============================
 */
window.deleteComponent = function() {
    if (selectedIndex === null) return;

    structure.splice(selectedIndex, 1);

    selectedIndex = null;

    renderPreview();

    document.getElementById('config-panel').innerHTML = '';
};


/**
 * ==============================
 * SAVE PAGE
 * ==============================
 */
window.savePage = function() {
    fetch('/pages/' + window.pageId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            structure: structure
        })
    })
    .then(res => res.json())
    .then(() => {
        alert("Page saved successfully!");
    })
    .catch(err => {
        console.error("Save error:", err);
    });
};