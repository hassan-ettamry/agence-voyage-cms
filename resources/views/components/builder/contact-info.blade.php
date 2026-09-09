@php
    $props = is_array($props ?? null) ? $props : [];
    $agency = \App\Support\AgencyContext::has() ? \App\Models\Agency::query()->find(\App\Support\AgencyContext::get()) : null;
    $email = trim((string) ($props['email'] ?? '')) ?: ($agency?->email ?? '');
    $phone = trim((string) ($props['phone'] ?? '')) ?: ($agency?->phone ?? '');
    $address = trim((string) ($props['address'] ?? '')) ?: ($agency?->address ?? '');
    $embedded = ($props['presentation'] ?? 'standalone') === 'embedded';
@endphp

<section
    @if($isEditor)
        data-type="contact-info"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $embedded ? 'h-full' : 'site-section' }} {{ $isEditor ? 'builder-node' : '' }}"
>
    <div class="{{ $embedded ? 'site-card h-full p-7 sm:p-9' : 'site-container' }}">
        <div class="site-eyebrow">CONTACT</div>
        <h2 class="site-heading mt-4 text-3xl sm:text-5xl">{{ $props['title'] ?? 'Talk to a travel expert' }}</h2>

        @if(filled($props['text'] ?? null))
            <p class="site-copy mt-5 max-w-xl leading-7" style="color: var(--site-muted);">{{ $props['text'] }}</p>
        @endif

        <div class="{{ $embedded ? 'mt-8 divide-y' : 'site-card-grid mt-8' }}" @if($embedded) style="border-color: var(--site-border);" @endif>
            @if($email)
                <a class="{{ $embedded ? 'block py-5 first:pt-0' : 'site-card p-6' }} no-underline" href="mailto:{{ $email }}">
                    <div class="site-eyebrow">EMAIL</div>
                    <div class="mt-2 break-all text-base font-bold sm:text-lg">{{ $email }}</div>
                </a>
            @endif
            @if($phone)
                <a class="{{ $embedded ? 'block py-5' : 'site-card p-6' }} no-underline" href="tel:{{ preg_replace('/[^+0-9]/', '', $phone) }}">
                    <div class="site-eyebrow">PHONE</div>
                    <div class="mt-2 text-base font-bold sm:text-lg">{{ $phone }}</div>
                </a>
            @endif
            @if($address)
                <div class="{{ $embedded ? 'py-5 last:pb-0' : 'site-card p-6' }}">
                    <div class="site-eyebrow">VISIT US</div>
                    <div class="mt-2 text-base font-bold sm:text-lg">{{ $address }}</div>
                </div>
            @endif
        </div>
    </div>
</section>
