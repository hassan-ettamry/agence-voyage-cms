@php
    $props = is_array($props ?? null) ? $props : [];
    $rawVariant = $props['variant'] ?? 'left';
    $variant = in_array($rawVariant, ['left', 'center', 'split'], true) ? $rawVariant : 'left';
    $rawBackgroundMode = $props['backgroundMode'] ?? 'color';
    $backgroundMode = in_array($rawBackgroundMode, ['color', 'image', 'video'], true) ? $rawBackgroundMode : 'color';
    $backgroundImage = trim((string) ($props['backgroundImage'] ?? ''));
    $padding = is_numeric($props['padding'] ?? null) ? max(40, min(160, (int) $props['padding'])) : 88;
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
        $href = 'mailto:'.trim((string) $props['email']);
    } elseif ($linkType === 'phone' && !empty($props['phone'])) {
        $href = 'tel:'.preg_replace('/[^+0-9]/', '', (string) $props['phone']);
    } elseif ($linkType === 'anchor' && !empty($props['anchor'])) {
        $href = '#'.ltrim((string) $props['anchor'], '#');
    }

    $hasButton = trim((string) ($props['buttonText'] ?? '')) !== '' && $href !== '#';
    $alignment = $variant === 'center' ? 'items-center text-center' : 'items-start text-left';
@endphp

<section
    @if($isEditor)
        data-type="hero"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="relative isolate flex min-h-[520px] overflow-hidden {{ $isEditor ? 'builder-node' : '' }}"
    style="background-color: {{ $props['backgroundColor'] ?? 'var(--site-secondary, #12372f)' }}; color: {{ $props['textColor'] ?? '#ffffff' }};"
>
    @if($backgroundMode === 'image' && $backgroundImage !== '')
        <img src="{{ $backgroundImage }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" @if(!$isEditor) loading="eager" fetchpriority="high" @endif>
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/75 via-black/50 to-black/15" aria-hidden="true"></div>

    <div class="site-container flex w-full {{ $alignment }} justify-center" style="padding-top: {{ $padding }}px; padding-bottom: {{ $padding }}px;">
        <div class="flex max-w-3xl flex-col {{ $alignment }} {{ $variant === 'split' ? 'lg:max-w-[52%]' : '' }}">
            @if(!empty($props['eyebrow']))
                <div class="site-eyebrow mb-5" style="color: var(--site-accent);">{{ $props['eyebrow'] }}</div>
            @endif
            <h1
                @if($isEditor) contenteditable="true" data-field="title" @endif
                class="site-heading text-[clamp(2.75rem,7vw,6.5rem)] font-bold {{ $isEditor ? 'outline-none' : '' }}"
            >{{ $props['title'] ?? 'Travel deeper. Return inspired.' }}</h1>

            <p
                @if($isEditor) contenteditable="true" data-field="description" @endif
                class="mt-6 max-w-2xl text-[clamp(1rem,2vw,1.25rem)] leading-8 text-white/85 {{ $isEditor ? 'outline-none' : '' }}"
            >{{ $props['description'] ?? 'Thoughtfully designed journeys with local insight and room for discovery.' }}</p>

            @if($hasButton)
                <a href="{{ $isEditor ? '#' : $href }}" @if($isEditor) onclick="return false" @endif class="site-button mt-8" style="background: var(--site-primary);">
                    {{ $props['buttonText'] }}
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </div>
</section>
