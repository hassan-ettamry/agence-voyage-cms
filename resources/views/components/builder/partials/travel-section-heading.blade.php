@php
    $props = is_array($props ?? null) ? $props : [];
    $travelType = (string) ($type ?? '');
    $isOfferSection = in_array($travelType, ['offer-grid', 'special-offers', 'offer-comparison'], true);
    $agency = \App\Support\AgencyContext::has()
        ? \App\Models\Agency::query()->find(\App\Support\AgencyContext::get())
        : null;
    $viewAllUrl = $agency
        ? ($isOfferSection
            ? app(\App\Services\PublicSiteUrl::class)->offers($agency)
            : app(\App\Services\PublicSiteUrl::class)->destinations($agency))
        : '#';
    $showViewAll = !in_array($props['showViewAll'] ?? 'yes', ['no', 'false', false, 0, '0'], true);
    $viewAllLabel = trim((string) ($props['viewAllLabel'] ?? ($isOfferSection ? 'View all journeys' : 'View all destinations')));
@endphp

@if(!empty($props['title']) || !empty($props['eyebrow']) || !empty($props['intro']) || ($showViewAll && $viewAllLabel !== ''))
    <div class="site-section-heading">
        <div class="site-section-heading__copy">
            @if(!empty($props['eyebrow']))
                <div class="site-eyebrow">{{ $props['eyebrow'] }}</div>
            @endif
            @if(!empty($props['title']))
                <h2 class="site-heading site-section-heading__title">{{ $props['title'] }}</h2>
            @endif
            @if(!empty($props['intro']))
                <p class="site-section-heading__intro">{{ $props['intro'] }}</p>
            @endif
        </div>
        @if($showViewAll && $viewAllLabel !== '')
            <a href="{{ $isEditor ? '#' : $viewAllUrl }}" @if($isEditor) onclick="return false" @endif class="site-section-link">
                {{ $viewAllLabel }} <span aria-hidden="true">&rarr;</span>
            </a>
        @endif
    </div>
@endif
