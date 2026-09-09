@php
    $props = is_array($props ?? null) ? $props : [];
    $searchType = ($props['searchType'] ?? 'destinations') === 'offers' ? 'offers' : 'destinations';
    $agency = \App\Support\AgencyContext::has()
        ? \App\Models\Agency::query()->find(\App\Support\AgencyContext::get())
        : null;
    $action = $agency
        ? ($searchType === 'offers'
            ? app(\App\Services\PublicSiteUrl::class)->offers($agency)
            : app(\App\Services\PublicSiteUrl::class)->destinations($agency))
        : '#';
    $backgroundImage = trim((string) ($props['backgroundImage'] ?? ''));
    $overlayOpacity = max(0, min(90, (int) ($props['overlayOpacity'] ?? 55))) / 100;
    $minHeight = max(360, min(900, (int) ($props['minHeight'] ?? 620)));
    $requestedBackgroundPosition = $props['backgroundPosition'] ?? 'center';
    $backgroundPosition = in_array($requestedBackgroundPosition, ['center', 'top', 'bottom', 'left', 'right'], true)
        ? $requestedBackgroundPosition
        : 'center';
    $centered = ($props['contentAlign'] ?? 'left') === 'center';
@endphp

<section @if($isEditor) data-type="search-hero" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="relative isolate flex items-end overflow-hidden {{ $isEditor ? 'builder-node' : '' }}" style="min-height: {{ $minHeight }}px; background: {{ $props['backgroundColor'] ?? 'var(--site-secondary, #12372f)' }}; color: #fff;">
    @if($backgroundImage !== '')
        <img src="{{ $backgroundImage }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" style="object-position: {{ $backgroundPosition }};" @if(!$isEditor) loading="eager" fetchpriority="high" @endif>
    @endif
    <div class="absolute inset-0 -z-10 bg-black" style="opacity: {{ $overlayOpacity }};"></div>
    <div class="site-container py-16 sm:py-24">
        <div class="max-w-4xl {{ $centered ? 'mx-auto text-center' : '' }}">
            <div class="site-eyebrow" style="color: var(--site-accent);">{{ $props['eyebrow'] ?? 'YOUR JOURNEY STARTS HERE' }}</div>
            <h1 @if($isEditor) contenteditable="true" data-field="title" @endif class="site-heading mt-5 text-[clamp(3rem,8vw,7rem)] font-bold {{ $isEditor ? 'outline-none' : '' }}">{{ $props['title'] ?? 'Where will you go next?' }}</h1>
            <p @if($isEditor) contenteditable="true" data-field="description" @endif class="mt-5 max-w-2xl text-lg leading-8 text-white/85 {{ $isEditor ? 'outline-none' : '' }}">{{ $props['description'] ?? 'Discover thoughtful journeys created by local travel experts.' }}</p>
        </div>
        <form action="{{ $isEditor ? '#' : $action }}" method="get" class="mt-9 flex max-w-3xl flex-col gap-3 rounded-[var(--site-radius)] bg-white p-3 shadow-2xl sm:flex-row {{ $centered ? 'mx-auto' : '' }}" @if($isEditor) onsubmit="return false" @endif role="search">
            <label class="sr-only" for="travel-search-{{ $nodeId }}">{{ $props['placeholder'] ?? 'Search destinations' }}</label>
            <input id="travel-search-{{ $nodeId }}" name="q" type="search" placeholder="{{ $props['placeholder'] ?? 'Search destinations' }}" class="min-h-12 flex-1 border-0 bg-transparent px-4 text-base text-slate-950 outline-none placeholder:text-slate-400">
            <button type="submit" class="site-button min-w-32">{{ $props['buttonText'] ?? 'Explore' }}</button>
        </form>
    </div>
</section>
