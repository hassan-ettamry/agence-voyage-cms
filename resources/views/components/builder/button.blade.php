@php
    $props = is_array($props ?? null) ? $props : [];

    $text = $props['text'] ?? 'Button';
    $linkType = $props['linkType'] ?? (!empty($props['url']) ? 'external' : 'none');
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
    $rawAlign = $props['align'] ?? 'left';
    $align = in_array($rawAlign, ['left', 'center', 'right'], true) ? $rawAlign : 'left';
    $justify = ['left' => 'justify-start', 'center' => 'justify-center', 'right' => 'justify-end'][$align];
    $rawSize = $props['size'] ?? 'md';
    $size = in_array($rawSize, ['sm', 'md', 'lg'], true) ? $rawSize : 'md';
    $sizeClasses = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-5 py-2.5 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ][$size];
    $fullWidth = ($props['width'] ?? 'auto') === 'full';
    $rawVariant = $props['variant'] ?? 'solid';
    $variant = in_array($rawVariant, ['solid', 'outline', 'ghost'], true) ? $rawVariant : 'solid';
    $background = $props['backgroundColor'] ?? 'var(--site-primary, #2563eb)';
    $textColor = $props['textColor'] ?? '#ffffff';
    $hoverBackground = $props['hoverBackgroundColor'] ?? $background;
    $hoverText = $props['hoverTextColor'] ?? $textColor;
    $radius = is_numeric($props['borderRadius'] ?? null) ? ((int) $props['borderRadius']) . 'px' : 'var(--site-radius, 14px)';
    $padding = is_numeric($props['padding'] ?? null) ? max(0, min(120, (int) $props['padding'])) : 16;
    $marginTop = is_numeric($props['marginTop'] ?? null) ? (int) $props['marginTop'] : 0;
    $marginBottom = is_numeric($props['marginBottom'] ?? null) ? (int) $props['marginBottom'] : 0;

    $buttonStyle = match ($variant) {
        'outline' => "background-color: transparent; color: {$background}; border-color: {$background};",
        'ghost' => "background-color: transparent; color: {$background}; border-color: transparent;",
        default => "background-color: {$background}; color: {$textColor}; border-color: transparent;",
    };

    $hoverEnter = match ($variant) {
        'outline' => "this.style.backgroundColor='{$hoverBackground}'; this.style.color='{$hoverText}';",
        'ghost' => "this.style.backgroundColor='transparent'; this.style.color='{$hoverText}';",
        default => "this.style.backgroundColor='{$hoverBackground}'; this.style.color='{$hoverText}';",
    };

    $hoverLeave = match ($variant) {
        'outline' => "this.style.backgroundColor='transparent'; this.style.color='{$background}';",
        'ghost' => "this.style.backgroundColor='transparent'; this.style.color='{$background}';",
        default => "this.style.backgroundColor='{$background}'; this.style.color='{$textColor}';",
    };

    $icon = $props['icon'] ?? '';
    $iconPosition = ($props['iconPosition'] ?? 'before') === 'after' ? 'after' : 'before';
@endphp

<div
    @if($isEditor)
        data-type="button"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} flex {{ $justify }} transition-all duration-150"
    style="padding: {{ $padding }}px; margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;"
>
    <{{ $hasLink ? 'a' : 'button' }}
        @if(!$hasLink)
            type="button"
        @endif
        @if($hasLink)
            href="{{ $isEditor ? '#' : $href }}"
            target="{{ $target }}"
            @if($target === '_blank')
                rel="noopener noreferrer"
            @endif
        @endif
        @if($isEditor)
            contenteditable="true"
            data-field="text"
            onclick="return false"
        @endif
        class="{{ $isEditor ? 'outline-none' : '' }} inline-flex items-center gap-2 border font-semibold transition-colors {{ $sizeClasses }} {{ $fullWidth ? 'w-full justify-center' : '' }}"
        style="{{ $buttonStyle }} border-radius: {{ $radius }};"
        onmouseenter="{{ $hoverEnter }}"
        onmouseleave="{{ $hoverLeave }}"
    >
        @if($icon && $iconPosition === 'before')
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                @include('components.builder.partials.icon-svg', ['name' => $icon])
            </svg>
        @endif

        <span>{{ $text }}</span>

        @if($icon && $iconPosition === 'after')
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                @include('components.builder.partials.icon-svg', ['name' => $icon])
            </svg>
        @endif
    </{{ $hasLink ? 'a' : 'button' }}>
</div>
