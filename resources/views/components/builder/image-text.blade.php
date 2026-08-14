@php
    $props = is_array($props ?? null) ? $props : [];
    $position = ($props['imagePosition'] ?? 'left') === 'right' ? 'right' : 'left';
    $image = trim((string) ($props['image'] ?? ''));
    $href = trim((string) ($props['url'] ?? '#')) ?: '#';
    if (!$isEditor && \App\Support\AgencyContext::has()) {
        $agency = \App\Models\Agency::query()->find(\App\Support\AgencyContext::get());
        $href = $agency ? app(\App\Services\PublicSiteUrl::class)->fromStoredUrl($agency, $href) : $href;
    }
@endphp

<section @if($isEditor) data-type="image-text" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}" style="background: {{ $props['backgroundColor'] ?? 'transparent' }};">
    <div class="site-container grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
        <div class="{{ $position === 'right' ? 'lg:order-2' : '' }}">
            <div class="site-card aspect-[4/3] bg-black/5">
                @if($image !== '')
                    <img src="{{ $image }}" alt="{{ $props['imageAlt'] ?? '' }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
                @elseif($isEditor)
                    <div class="grid h-full place-items-center text-sm" style="color: var(--site-muted);">Choose an image</div>
                @endif
            </div>
        </div>
        <div class="{{ $position === 'right' ? 'lg:order-1' : '' }}">
            <div class="site-eyebrow">{{ $props['eyebrow'] ?? 'OUR APPROACH' }}</div>
            <h2 @if($isEditor) contenteditable="true" data-field="title" @endif class="site-heading mt-4 text-[clamp(2.25rem,5vw,4.5rem)] font-bold {{ $isEditor ? 'outline-none' : '' }}">{{ $props['title'] ?? 'Travel designed around you' }}</h2>
            <p @if($isEditor) contenteditable="true" data-field="text" @endif class="site-lead mt-6 {{ $isEditor ? 'outline-none' : '' }}">{{ $props['text'] ?? 'We combine local knowledge with personal service to create journeys that feel entirely your own.' }}</p>
            @if(!empty($props['buttonText']))
                <a href="{{ $isEditor ? '#' : $href }}" @if($isEditor) onclick="return false" @endif class="site-button mt-8">{{ $props['buttonText'] }} <span aria-hidden="true">&rarr;</span></a>
            @endif
        </div>
    </div>
</section>
