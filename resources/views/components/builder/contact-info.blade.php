@php
    $props = is_array($props ?? null) ? $props : [];
    $agency = \App\Support\AgencyContext::has() ? \App\Models\Agency::query()->find(\App\Support\AgencyContext::get()) : null;
    $email = trim((string) ($props['email'] ?? '')) ?: ($agency?->email ?? '');
    $phone = trim((string) ($props['phone'] ?? '')) ?: ($agency?->phone ?? '');
    $address = trim((string) ($props['address'] ?? '')) ?: ($agency?->address ?? '');
@endphp
<section @if($isEditor) data-type="contact-info" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}"><div class="site-container"><h2 class="site-heading text-3xl sm:text-5xl">{{ $props['title'] ?? 'Talk to a travel expert' }}</h2><div class="site-card-grid mt-8">@if($email)<a class="site-card p-6" href="mailto:{{ $email }}"><div class="site-eyebrow">EMAIL</div><div class="mt-3 text-lg font-bold">{{ $email }}</div></a>@endif @if($phone)<a class="site-card p-6" href="tel:{{ preg_replace('/[^+0-9]/', '', $phone) }}"><div class="site-eyebrow">PHONE</div><div class="mt-3 text-lg font-bold">{{ $phone }}</div></a>@endif @if($address)<div class="site-card p-6"><div class="site-eyebrow">VISIT US</div><div class="mt-3 text-lg font-bold">{{ $address }}</div></div>@endif</div></div></section>
