@php
    $props = is_array($props ?? null) ? $props : [];
    $src = trim((string) ($props['src'] ?? ''));
    $alt = $props['altText'] ?? $props['alt'] ?? '';
    $title = $props['title'] ?? '';
    $caption = $props['caption'] ?? '';
    $height = is_numeric($props['height'] ?? null) ? max(80, min(720, (int) $props['height'])) : 256;
    $objectFit = in_array($props['objectFit'] ?? 'cover', ['cover', 'contain', 'fill', 'none'], true) ? $props['objectFit'] : 'cover';
    $radius = is_numeric($props['borderRadius'] ?? null) ? max(0, min(80, (int) $props['borderRadius'])) : 12;
    $padding = is_numeric($props['padding'] ?? null) ? max(0, min(120, (int) $props['padding'])) : 16;
    $marginTop = is_numeric($props['marginTop'] ?? null) ? (int) $props['marginTop'] : 0;
    $marginBottom = is_numeric($props['marginBottom'] ?? null) ? (int) $props['marginBottom'] : 0;

    $linkType = $props['linkType'] ?? 'none';
    $href = '#';

    if ($linkType === 'external') {
        $href = trim((string) ($props['url'] ?? '#')) ?: '#';

        if (!$isEditor && \App\Support\AgencyContext::has()) {
            $publicAgency = \App\Models\Agency::query()->find(\App\Support\AgencyContext::get());
            $href = $publicAgency
                ? app(\App\Services\PublicSiteUrl::class)->fromStoredUrl($publicAgency, $href)
                : $href;
        }
    } elseif ($linkType === 'email' && !empty($props['email'])) {
        $href = 'mailto:' . trim((string) $props['email']);
    } elseif ($linkType === 'phone' && !empty($props['phone'])) {
        $href = 'tel:' . preg_replace('/\s+/', '', (string) $props['phone']);
    } elseif ($linkType === 'anchor' && !empty($props['anchor'])) {
        $href = '#' . ltrim((string) $props['anchor'], '#');
    }

    $hasLink = $linkType !== 'none' && $href !== '#';
    $target = ($props['target'] ?? 'same-tab') === 'new-tab' ? '_blank' : '_self';
@endphp

<div
    @if($isEditor)
        data-type="image"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} transition-all duration-150"
    style="padding: {{ $padding }}px; margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;"
>
    @if($hasLink)
        <a
            href="{{ $isEditor ? '#' : $href }}"
            target="{{ $target }}"
            @if($target === '_blank')
                rel="noopener noreferrer"
            @endif
            @if($isEditor)
                onclick="return false"
            @endif
            class="block"
        >
    @endif

    <figure>
        <div
            class="flex items-center justify-center overflow-hidden border"
            style="
                height: {{ $height }}px;
                background-color: {{ $props['backgroundColor'] ?? '#f3f4f6' }};
                border-style: {{ $props['borderStyle'] ?? 'dashed' }};
                border-color: {{ $props['borderColor'] ?? '#d1d5db' }};
                border-radius: {{ $radius }}px;
            "
        >
            @if($src !== '')
                <img
                    src="{{ $src }}"
                    alt="{{ $alt }}"
                    title="{{ $title }}"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full"
                    style="object-fit: {{ $objectFit }};"
                >
            @elseif($isEditor)
                <div class="px-4 text-center text-sm text-gray-400">
                    {{ $alt ?: 'Image' }}
                </div>
            @endif
        </div>

        @if($caption !== '')
            <figcaption class="mt-2 text-center text-sm" style="color: var(--site-muted, #64748b);">
                {{ $caption }}
            </figcaption>
        @endif
    </figure>

    @if($hasLink)
        </a>
    @endif
</div>
