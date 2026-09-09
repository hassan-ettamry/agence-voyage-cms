const SVG_WIDTH = 300;
const SVG_HEIGHT = 160;
const PADDING = { top: 30, right: 14, bottom: 20, left: 26 };

export function initDashboardCharts() {
    document.querySelectorAll('[data-dashboard-chart-source]').forEach((source) => {
        const chart = parseChart(source);

        if (!chart || !chart.id) {
            return;
        }

        const target = document.querySelector(`[data-dashboard-chart-target="${chart.id}"]`);

        if (!target) {
            return;
        }

        target.innerHTML = renderChart(chart);
    });
}

function renderChart(chart) {
    if (chart.type === 'bar' || chart.type === 'bars') {
        return renderBarChart(chart);
    }

    if (chart.type === 'donut') {
        return renderDonutChart(chart);
    }

    return renderLineChart(chart);
}

function parseChart(source) {
    try {
        return JSON.parse(source.textContent || '{}');
    } catch (error) {
        return null;
    }
}

function renderLineChart(chart) {
    const labels = Array.isArray(chart.labels) ? chart.labels : [];
    const datasets = Array.isArray(chart.datasets) ? chart.datasets : [];
    const values = datasets.flatMap((dataset) => dataset.values || []);
    const max = resolveMax(chart, values);
    const chartWidth = SVG_WIDTH - PADDING.left - PADDING.right;
    const chartHeight = SVG_HEIGHT - PADDING.top - PADDING.bottom;
    const paths = datasets.map((dataset, index) => {
        const offset = datasets.length > 1 ? (index - (datasets.length - 1) / 2) * 3.5 : 0;
        const points = toPoints(dataset.values || [], max, chartWidth, chartHeight, offset);
        const path = points.length ? `M ${points.map((point) => `${point.x} ${point.y}`).join(' L ')}` : '';

        return `
            <path d="${path}" fill="none" stroke="${escapeAttr(dataset.color || '#64748b')}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            ${points.map((point) => `<circle cx="${point.x}" cy="${point.y}" r="2" fill="#fff" stroke="${escapeAttr(dataset.color || '#64748b')}" stroke-width="1.4" />`).join('')}
        `;
    }).join('');

    return `
        <svg role="img" aria-label="${escapeAttr(chart.title || 'Dashboard chart')}" class="h-full w-full overflow-visible" viewBox="0 0 ${SVG_WIDTH} ${SVG_HEIGHT}" preserveAspectRatio="none">
            ${renderLegend(datasets)}
            ${renderGrid(max)}
            ${paths}
            ${renderXAxis(labels)}
        </svg>
    `;
}

function renderBarChart(chart) {
    const labels = Array.isArray(chart.labels) ? chart.labels : [];
    const values = Array.isArray(chart.values) ? chart.values : [];
    const max = resolveMax(chart, values);
    const chartWidth = SVG_WIDTH - PADDING.left - PADDING.right;
    const chartHeight = SVG_HEIGHT - PADDING.top - PADDING.bottom;
    const slot = values.length ? chartWidth / values.length : chartWidth;
    const barWidth = Math.max(5, Math.min(12, slot * 0.42));
    const color = chart.color || '#2f80ed';

    const bars = values.map((value, index) => {
        const height = Math.max((value / max) * (chartHeight - 6), value > 0 ? 3 : 0);
        const x = PADDING.left + index * slot + (slot - barWidth) / 2;
        const y = PADDING.top + chartHeight - height;

        return `
            <rect x="${x}" y="${y}" width="${barWidth}" height="${height}" rx="2.5" fill="${escapeAttr(color)}" opacity="${value === 0 ? '0' : '0.92'}" />
        `;
    }).join('');

    const legend = Array.isArray(chart.legend) ? chart.legend : [];

    return `
        <svg role="img" aria-label="${escapeAttr(chart.title || 'Dashboard chart')}" class="h-full w-full overflow-visible" viewBox="0 0 ${SVG_WIDTH} ${SVG_HEIGHT}" preserveAspectRatio="none">
            ${renderLegend(legend)}
            ${renderGrid(max)}
            ${bars}
            ${renderXAxis(labels)}
        </svg>
    `;
}

function renderDonutChart(chart) {
    const segments = Array.isArray(chart.segments) ? chart.segments : [];
    const total = Math.max(1, segments.reduce((sum, segment) => sum + Number(segment.value || 0), 0));
    const radius = 43;
    const circumference = 2 * Math.PI * radius;
    let offset = 0;

    const rings = segments.map((segment) => {
        const value = Number(segment.value || 0);
        const length = (value / total) * circumference;
        const dashOffset = -offset;
        offset += length;

        return `
            <circle
                cx="88"
                cy="88"
                r="${radius}"
                fill="none"
                stroke="${escapeAttr(segment.color || '#94a3b8')}"
                stroke-width="17"
                stroke-dasharray="${length} ${circumference - length}"
                stroke-dashoffset="${dashOffset}"
                stroke-linecap="butt"
                transform="rotate(-90 88 88)"
            />
        `;
    }).join('');

    const legend = segments.map((segment, index) => {
        const y = 70 + index * 25;

        return `
            <g transform="translate(180 ${y})">
                <circle cx="0" cy="0" r="4" fill="${escapeAttr(segment.color || '#94a3b8')}" />
                <text x="11" y="4" fill="#0f172a" font-size="10.5" font-weight="600">${escapeHtml(segment.label || '')}</text>
                <text x="82" y="4" fill="#0f172a" font-size="10.5" font-weight="700" text-anchor="end">${escapeHtml(String(segment.value || 0))}</text>
                <text x="86" y="4" fill="#475569" font-size="10.5" font-weight="500">(${escapeHtml(String(segment.percent || 0))}%)</text>
            </g>
        `;
    }).join('');

    return `
        <svg role="img" aria-label="${escapeAttr(chart.title || 'Content status')}" class="h-full w-full overflow-visible" viewBox="0 0 ${SVG_WIDTH} ${SVG_HEIGHT}">
            <circle cx="88" cy="88" r="${radius}" fill="none" stroke="#e8edf4" stroke-width="17" />
            ${rings}
            <text x="88" y="84" text-anchor="middle" fill="#0f172a" font-size="18" font-weight="800">${escapeHtml(String(chart.centerValue || 0))}</text>
            <text x="88" y="103" text-anchor="middle" fill="#475569" font-size="10.5" font-weight="500">${escapeHtml(chart.centerLabel || 'Total')}</text>
            ${legend}
        </svg>
    `;
}

function toPoints(values, max, chartWidth, chartHeight, yOffset = 0) {
    const count = values.length;
    const step = count > 1 ? chartWidth / (count - 1) : chartWidth;

    return values.map((value, index) => ({
        x: round(PADDING.left + index * step),
        y: round(PADDING.top + (1 - value / max) * chartHeight + yOffset),
    }));
}

function renderGrid(max) {
    const chartHeight = SVG_HEIGHT - PADDING.top - PADDING.bottom;
    const chartWidth = SVG_WIDTH - PADDING.left - PADDING.right;
    const rawTicks = max <= 3
        ? [max, Math.ceil(max * 0.66), Math.ceil(max * 0.33), 0]
        : [max, Math.round(max * 0.75), Math.round(max * 0.5), Math.round(max * 0.25), 0];
    const ticks = [...new Set(rawTicks)].sort((a, b) => b - a);

    return ticks.map((value) => {
        const y = PADDING.top + chartHeight * (1 - value / max);

        return `
            <line x1="${PADDING.left}" y1="${y}" x2="${PADDING.left + chartWidth}" y2="${y}" stroke="#e5e7eb" stroke-width="1" />
            <text x="${PADDING.left - 9}" y="${y + 3}" text-anchor="end" fill="#475569" font-size="8.5" font-weight="500">${value}</text>
        `;
    }).join('');
}

function renderXAxis(labels) {
    if (!labels.length) {
        return '';
    }

    const chartWidth = SVG_WIDTH - PADDING.left - PADDING.right;

    return labels.map((label, index) => {
        const x = labels.length > 1
            ? PADDING.left + (chartWidth / (labels.length - 1)) * index
            : PADDING.left;

        return `<text x="${round(x)}" y="${SVG_HEIGHT - 5}" text-anchor="middle" fill="#475569" font-size="8" font-weight="500">${escapeHtml(label)}</text>`;
    }).join('');
}

function renderLegend(datasets) {
    return datasets.map((dataset, index) => {
        const x = PADDING.left + index * 72;

        return `
            <g transform="translate(${x}, 12)">
                <circle cx="0" cy="0" r="3.2" fill="${escapeAttr(dataset.color || '#64748b')}" />
                <text x="8" y="3" fill="#334155" font-size="8.5" font-weight="600">${escapeHtml(dataset.label || 'Series')}</text>
            </g>
        `;
    }).join('');
}

function resolveMax(chart, values) {
    const actualMax = Math.max(1, ...values.map((value) => Number(value || 0)));
    const requestedMax = Number(chart.max || 0);

    return Math.max(requestedMax || 1, actualMax);
}

function round(value) {
    return Math.round(value * 10) / 10;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function escapeAttr(value) {
    return escapeHtml(value);
}
