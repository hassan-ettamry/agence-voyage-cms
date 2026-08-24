@extends('frontend.layout')

@section('content')
@php
    $cover = $offer->media ?: $offer->destination?->media?->first();
    $currency = $siteAgency->catalogCurrency();
@endphp
<article>
    <section class="relative isolate min-h-[560px] overflow-hidden bg-[var(--site-secondary)] text-white">
        @if($cover)<img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $offer->title }}" class="absolute inset-0 -z-20 h-full w-full object-cover">@endif
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/90 via-black/45 to-black/15"></div>
        <div class="site-container flex min-h-[560px] flex-col justify-end py-14">
            <a href="{{ app(\App\Services\PublicSiteUrl::class)->offers($siteAgency) }}" class="mb-8 font-bold text-white/80">&larr; All journeys</a>
            @if($offer->is_special)<div class="site-eyebrow" style="color: var(--site-accent);">SIGNATURE CHOICE</div>@endif
            <h1 class="site-heading mt-3 max-w-5xl text-[clamp(3rem,7vw,6.5rem)]">{{ $offer->title }}</h1>
            @if($offer->summary)<p class="mt-5 max-w-3xl text-xl leading-8 text-white/85">{{ $offer->summary }}</p>@endif
            @if($offer->destination)<a href="{{ app(\App\Services\PublicSiteUrl::class)->destination($siteAgency, $offer->destination) }}" class="mt-5 w-fit text-lg font-bold text-white">{{ $offer->destination->name }} &rarr;</a>@endif
        </div>
    </section>

    <section class="site-section"><div class="site-container grid gap-12 lg:grid-cols-[1fr_340px]">
        <div><div class="site-eyebrow">THE JOURNEY</div><p class="site-lead mt-4 whitespace-pre-line">{{ $offer->description }}</p></div>
        <aside class="site-card h-fit p-7"><dl class="divide-y divide-slate-200"><div class="flex justify-between gap-4 py-4"><dt class="site-copy">Duration</dt><dd class="font-bold">{{ $offer->duration_days }} days</dd></div><div class="flex justify-between gap-4 py-4"><dt class="site-copy">From</dt><dd class="text-xl font-bold text-[var(--site-primary)]">{{ number_format((float) $offer->price, 2) }} {{ $currency }}</dd></div>@if($offer->destination)<div class="flex justify-between gap-4 py-4"><dt class="site-copy">Destination</dt><dd class="font-bold">{{ $offer->destination->name }}</dd></div>@endif</dl><a href="{{ app(\App\Services\PublicSiteUrl::class)->page($siteAgency, 'contact') }}" class="site-button mt-6 w-full">Plan this journey</a><p class="mt-3 text-center text-xs text-[var(--site-muted)]">Price is indicative until the agency confirms your trip.</p></aside>
    </div></section>

    @if($offer->itinerary)
        <section class="site-section pt-0"><div class="site-container"><div class="site-eyebrow">YOUR PROGRAMME</div><h2 class="site-heading mt-3 text-3xl sm:text-5xl">Day by day</h2><ol class="mt-8 space-y-4">@foreach($offer->itinerary as $day)<li class="site-card grid gap-4 p-6 sm:grid-cols-[80px_1fr]"><div class="text-sm font-black uppercase tracking-wider text-[var(--site-primary)]">Day {{ $day['day'] }}</div><div><h3 class="site-heading text-2xl">{{ $day['title'] }}</h3>@if($day['description'] ?? null)<p class="site-copy mt-3 whitespace-pre-line">{{ $day['description'] }}</p>@endif</div></li>@endforeach</ol></div></section>
    @endif

    @if($offer->inclusions || $offer->exclusions)
        <section class="site-section" style="background: var(--site-surface);"><div class="site-container grid gap-6 lg:grid-cols-2">
            @if($offer->inclusions)<div class="site-card p-7"><div class="site-eyebrow">INCLUDED</div><ul class="mt-5 space-y-3">@foreach($offer->inclusions as $item)<li class="flex gap-3"><span class="font-black text-emerald-600">✓</span><span class="site-copy">{{ $item }}</span></li>@endforeach</ul></div>@endif
            @if($offer->exclusions)<div class="site-card p-7"><div class="site-eyebrow">NOT INCLUDED</div><ul class="mt-5 space-y-3">@foreach($offer->exclusions as $item)<li class="flex gap-3"><span class="font-black text-slate-400">—</span><span class="site-copy">{{ $item }}</span></li>@endforeach</ul></div>@endif
        </div></section>
    @endif

    @if($offer->practical_information)
        <section class="site-section"><div class="site-container"><div class="site-card p-7 sm:p-10"><div class="site-eyebrow">GOOD TO KNOW</div><h2 class="site-heading mt-3 text-3xl">Practical information</h2><p class="site-copy mt-5 whitespace-pre-line">{{ $offer->practical_information }}</p></div></div></section>
    @endif

    <section class="site-section" style="background: var(--site-primary); color: #fff;"><div class="site-container flex flex-col items-start justify-between gap-7 lg:flex-row lg:items-center"><div><div class="site-eyebrow text-white/70">TAILORED TO YOU</div><h2 class="site-heading mt-3 text-3xl sm:text-5xl">Make this journey your own</h2><p class="mt-4 max-w-2xl text-white/85">Change the pace, stays and experiences with help from our travel designers.</p></div><a href="{{ app(\App\Services\PublicSiteUrl::class)->page($siteAgency, 'contact') }}" class="site-button site-button--secondary">Talk to an expert</a></div></section>

    @if($relatedOffers->isNotEmpty())<section class="site-section"><div class="site-container"><div class="site-eyebrow">KEEP EXPLORING</div><h2 class="site-heading mt-3 text-3xl sm:text-5xl">Related journeys</h2><div class="site-card-grid mt-9">@foreach($relatedOffers as $relatedOffer)@include('frontend.partials.offer-card', ['offer' => $relatedOffer])@endforeach</div></div></section>@endif
</article>
@endsection
