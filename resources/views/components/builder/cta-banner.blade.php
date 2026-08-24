@php
    $props = is_array($props ?? null) ? $props : [];
    $agency = \App\Support\AgencyContext::has() ? \App\Models\Agency::query()->find(\App\Support\AgencyContext::get()) : null;
    $storedUrl = (string) ($props['url'] ?? '/contact');
    $buttonUrl = $agency ? app(\App\Services\PublicSiteUrl::class)->fromStoredUrl($agency, $storedUrl) : $storedUrl;
    $centered = ($props['contentAlign'] ?? 'left') === 'center';
@endphp
<section @if($isEditor) data-type="cta-banner" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}" style="background: {{ $props['backgroundColor'] ?? 'var(--site-primary)' }}; color: #fff;">
    <div class="site-container flex flex-col {{ $centered ? 'items-center text-center' : 'items-start justify-between lg:flex-row lg:items-center' }} gap-8">
        <div class="max-w-3xl">@if(!empty($props['eyebrow']))<div class="site-eyebrow text-white">{{ $props['eyebrow'] }}</div>@endif<h2 class="site-heading mt-3 text-3xl sm:text-5xl">{{ $props['title'] ?? 'Ready for your next story?' }}</h2>@if(!empty($props['text']))<p class="mt-4 max-w-2xl text-lg text-white">{{ $props['text'] }}</p>@endif</div>
        <a href="{{ $isEditor ? '#' : $buttonUrl }}" class="site-button site-button--secondary shrink-0">{{ $props['buttonText'] ?? 'Plan my trip' }}</a>
    </div>
</section>
