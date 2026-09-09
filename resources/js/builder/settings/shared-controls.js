const typographyTypes = new Set([
    'heading', 'text', 'richtext', 'button'
]);

const gridTypes = new Set([
    'gallery', 'feature-grid', 'destination-grid', 'featured-destinations',
    'destination-carousel', 'offer-grid', 'special-offers', 'offer-comparison',
    'testimonials', 'stats-counter', 'trust-logos'
]);

export function resolveResponsiveDesign(design = {}, device = 'desktop') {
    const desktop = design?.desktop && typeof design.desktop === 'object' ? design.desktop : {};
    const tablet = design?.tablet && typeof design.tablet === 'object' ? design.tablet : {};
    const mobile = design?.mobile && typeof design.mobile === 'object' ? design.mobile : {};

    if (device === 'mobile') return { ...desktop, ...tablet, ...mobile };
    if (device === 'tablet' || device === 'tab') return { ...desktop, ...tablet };
    return { ...desktop };
}

function baseLayoutFields(type) {
    const fields = {
        'design.desktop.marginTop': { type: 'number', label: 'Margin Top', min: 0, max: 240, default: '' },
        'design.desktop.marginBottom': { type: 'number', label: 'Margin Bottom', min: 0, max: 240, default: '' },
        'design.desktop.paddingTop': { type: 'number', label: 'Padding Top', min: 0, max: 240, default: '' },
        'design.desktop.paddingRight': { type: 'number', label: 'Padding Right', min: 0, max: 240, default: '' },
        'design.desktop.paddingBottom': { type: 'number', label: 'Padding Bottom', min: 0, max: 240, default: '' },
        'design.desktop.paddingLeft': { type: 'number', label: 'Padding Left', min: 0, max: 240, default: '' },
        'design.desktop.width': { type: 'text', label: 'Width', default: '', help: 'Examples: 100%, 720px, 48rem. Empty uses the component default.' },
        'design.desktop.maxWidth': { type: 'text', label: 'Max Width', default: '', help: 'Examples: 1180px, 90%, 72rem.' },
        'design.desktop.alignment': {
            type: 'select', label: 'Content Alignment', default: '',
            options: [{ value: '', label: 'Theme default' }, 'left', 'center', 'right', 'justify']
        }
    };
    if (gridTypes.has(type)) {
        fields['design.desktop.columns'] = { type: 'number', label: 'Columns', min: 1, max: 6, default: '' };
        fields['design.desktop.gap'] = { type: 'number', label: 'Gap', min: 0, max: 96, default: '' };
    }
    return fields;
}

function baseStyleFields(type) {
    const fields = {
        'design.desktop.backgroundColor': { type: 'color', label: 'Background Color', default: '#ffffff' },
        'design.desktop.backgroundImage': { type: 'media', label: 'Background Image', default: '' },
        'design.desktop.borderColor': { type: 'color', label: 'Border Color', default: '#e5e7eb' },
        'design.desktop.borderWidth': { type: 'number', label: 'Border Width', min: 0, max: 12, default: '' },
        'design.desktop.borderRadius': { type: 'number', label: 'Border Radius', min: 0, max: 120, default: '' },
        'design.desktop.shadow': {
            type: 'select', label: 'Shadow', default: '',
            options: [{ value: '', label: 'Theme default' }, 'none', 'soft', 'medium', 'strong']
        }
    };
    if (typographyTypes.has(type)) {
        fields['design.desktop.textColor'] = { type: 'color', label: 'Text Color', default: '#111827' };
        fields['design.desktop.fontSize'] = { type: 'number', label: 'Font Size', min: 10, max: 120, default: '' };
        fields['design.desktop.fontWeight'] = { type: 'number', label: 'Font Weight', min: 100, max: 900, step: 100, default: '' };
        fields['design.desktop.lineHeight'] = { type: 'number', label: 'Line Height', min: 0.8, max: 3, step: 0.05, default: '' };
    }
    return fields;
}

function responsiveFields(type, device) {
    if (device === 'desktop') {
        return {
            responsiveHelp: { type: 'notice', text: 'Desktop is the base. Select Tablet or Mobile in the top bar to edit that device override.' },
            'design.tablet.visibility': { type: 'select', label: 'Tablet Visibility', default: '', options: [{ value: '', label: 'Inherit' }, 'visible', 'hidden'] },
            'design.mobile.visibility': { type: 'select', label: 'Mobile Visibility', default: '', options: [{ value: '', label: 'Inherit' }, 'visible', 'hidden'] }
        };
    }

    const prefix = `design.${device}`;
    const label = device === 'tablet' ? 'Tablet' : 'Mobile';
    const fields = {
        responsiveHelp: { type: 'notice', text: `${label} values inherit from the larger device when left empty.` },
        [`${prefix}.visibility`]: { type: 'select', label: 'Visibility', default: '', options: [{ value: '', label: 'Inherit' }, 'visible', 'hidden'] },
        [`${prefix}.paddingTop`]: { type: 'number', label: 'Padding Top', min: 0, max: 240, default: '' },
        [`${prefix}.paddingRight`]: { type: 'number', label: 'Padding Right', min: 0, max: 240, default: '' },
        [`${prefix}.paddingBottom`]: { type: 'number', label: 'Padding Bottom', min: 0, max: 240, default: '' },
        [`${prefix}.paddingLeft`]: { type: 'number', label: 'Padding Left', min: 0, max: 240, default: '' },
        [`${prefix}.alignment`]: { type: 'select', label: 'Alignment', default: '', options: [{ value: '', label: 'Inherit' }, 'left', 'center', 'right', 'justify'] },
        [`${prefix}.width`]: { type: 'text', label: 'Width', default: '' },
        [`${prefix}.maxWidth`]: { type: 'text', label: 'Max Width', default: '' }
    };
    if (typographyTypes.has(type)) {
        fields[`${prefix}.fontSize`] = { type: 'number', label: 'Font Size', min: 10, max: 120, default: '' };
        fields[`${prefix}.lineHeight`] = { type: 'number', label: 'Line Height', min: 0.8, max: 3, step: 0.05, default: '' };
    }
    if (gridTypes.has(type)) {
        fields[`${prefix}.columns`] = { type: 'number', label: 'Columns', min: 1, max: 6, default: '' };
        fields[`${prefix}.gap`] = { type: 'number', label: 'Gap', min: 0, max: 96, default: '' };
    }
    return fields;
}

export function mergeSharedControls(type, schema = {}, viewport = 'desktop') {
    const device = viewport === 'tab' ? 'tablet' : viewport;
    const tabs = structuredClone(schema.tabs || {});
    tabs.layout = { title: tabs.layout?.title || 'Layout', fields: { ...(tabs.layout?.fields || {}), ...baseLayoutFields(type) } };
    tabs.style = { title: tabs.style?.title || 'Style', fields: { ...(tabs.style?.fields || {}), ...baseStyleFields(type) } };
    tabs.responsive = { title: device === 'desktop' ? 'Responsive' : `Responsive · ${device}`, fields: { ...(tabs.responsive?.fields || {}), ...responsiveFields(type, device) } };
    return { schema_version: schema.schema_version || 2, ...schema, tabs };
}

if (typeof window !== 'undefined') window.BuilderSharedControls = {
    merge: mergeSharedControls,
    resolve: resolveResponsiveDesign
};
