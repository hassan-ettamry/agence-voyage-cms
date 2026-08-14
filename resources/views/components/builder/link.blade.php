@php
    $linkType = $props['linkType'] ?? (!empty($props['url']) ? 'external' : 'none');
    $url = '#';

    if ($linkType === 'external') {
        $rawUrl = trim((string) ($props['url'] ?? '#'));
        $url = $rawUrl === '' ? '#' : $rawUrl;

        if (!$isEditor && \App\Support\AgencyContext::has()) {
            $publicAgency = \App\Models\Agency::query()->find(\App\Support\AgencyContext::get());
            $url = $publicAgency
                ? app(\App\Services\PublicSiteUrl::class)->fromStoredUrl($publicAgency, $url)
                : $url;
        }
    } elseif ($linkType === 'email' && !empty($props['email'])) {
        $url = 'mailto:' . trim((string) $props['email']);
    } elseif ($linkType === 'phone' && !empty($props['phone'])) {
        $url = 'tel:' . preg_replace('/\s+/', '', (string) $props['phone']);
    } elseif ($linkType === 'anchor' && !empty($props['anchor'])) {
        $url = '#' . ltrim((string) $props['anchor'], '#');
    }

    $target = ($props['target'] ?? 'same-tab') === 'new-tab' ? '_blank' : '_self';
    $underline = ($props['underline'] ?? 'yes') === 'yes' ? 'underline' : 'none';
    $align = in_array($props['align'] ?? 'left', ['left', 'center', 'right'], true) ? $props['align'] : 'left';
    $fontSize = is_numeric($props['fontSize'] ?? null) ? max(10, min(80, (int) $props['fontSize'])) : 16;
    $fontWeight = is_numeric($props['fontWeight'] ?? null) ? max(100, min(900, (int) $props['fontWeight'])) : 500;
    $padding = is_numeric($props['padding'] ?? null) ? max(0, min(120, (int) $props['padding'])) : 16;
    $marginTop = is_numeric($props['marginTop'] ?? null) ? (int) $props['marginTop'] : 0;
    $marginBottom = is_numeric($props['marginBottom'] ?? null) ? (int) $props['marginBottom'] : 0;
    $hoverColor = $props['hoverColor'] ?? ($props['color'] ?? 'var(--site-primary, #2563eb)');
@endphp

<div
    @if($isEditor)
        data-type="link"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} transition-all duration-150"
    style="text-align: {{ $align }}; padding: {{ $padding }}px; margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;"
>
    <a
        href="{{ $isEditor ? '#' : $url }}"
        target="{{ $target }}"
        @if($target === '_blank')
            rel="noopener noreferrer"
        @endif
        @if($isEditor)
            contenteditable="true"
            data-field="text"
            onclick="return false"
        @endif
        class="{{ $isEditor ? 'outline-none' : '' }} inline-flex items-center gap-2 font-bold transition-all hover:-translate-y-px"
        style="
            color: {{ $props['color'] ?? 'var(--site-primary, #2563eb)' }};
            font-size: {{ $fontSize }}px;
            font-weight: {{ $fontWeight }};
            line-height: {{ is_numeric($props['lineHeight'] ?? null) ? $props['lineHeight'] : 1.5 }};
            text-decoration: {{ $underline }};
        "
        onmouseenter="this.style.color='{{ $hoverColor }}';"
        onmouseleave="this.style.color='{{ $props['color'] ?? 'var(--site-primary, #2563eb)' }}';"
    >
        {{ $props['text'] ?? 'Link text' }}
    </a>
</div>
