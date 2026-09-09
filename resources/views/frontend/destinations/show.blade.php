@extends('frontend.layout')

@section('content')
@php
    $cover = $destination->media->first();
    $location = collect([$destination->region, $destination->country])->filter()->join(', ');
    $mapUrl = ($destination->latitude !== null && $destination->longitude !== null)
        ? 'https://www.openstreetmap.org/?mlat='.urlencode($destination->latitude).'&mlon='.urlencode($destination->longitude).'#map=10/'.$destination->latitude.'/'.$destination->longitude
        : null;
@endphp
<article>
    <section class="relative isolate min-h-[540px] overflow-hidden bg-[var(--site-secondary)] text-white">
        @if($cover)<img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" class="absolute inset-0 -z-20 h-full w-full object-cover">@endif
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/85 via-black/40 to-black/15"></div>
        <div class="site-container flex min-h-[540px] flex-col justify-end py-14">
            <a href="{{ app(\App\Services\PublicSiteUrl::class)->destinations($siteAgency) }}" class="mb-8 font-bold text-white/80">&larr; All destinations</a>
            <div class="site-eyebrow" style="color: var(--site-accent);">{{ $location }}</div>
            <h1 class="site-heading mt-3 max-w-5xl text-[clamp(3.5rem,8vw,7.5rem)]">{{ $destination->name }}</h1>
        </div>
    </section>

    <section class="site-section">
        <div class="site-container grid gap-12 lg:grid-cols-[1fr_340px]">
            <div>
                <div class="site-eyebrow">THE DESTINATION</div>
                <p class="site-lead mt-4 whitespace-pre-line">{{ $destination->description }}</p>

                @if($destination->travel_types)
                    <div class="mt-8 flex flex-wrap gap-2">@foreach($destination->travel_types as $type)<span class="rounded-full bg-[var(--site-surface)] px-4 py-2 text-sm font-bold">{{ \App\Support\TravelCatalog::travelTypeLabel($type) }}</span>@endforeach</div>
                @endif
            </div>
            <aside class="site-card h-fit p-7">
                <h2 class="site-heading text-2xl">Plan your visit</h2>
                @if($destination->ideal_months)
                    <div class="mt-5"><div class="text-xs font-bold uppercase tracking-wider text-[var(--site-muted)]">Best time</div><p class="mt-2 font-bold">{{ collect($destination->ideal_months)->map(fn ($month) => \App\Support\TravelCatalog::monthLabel($month))->filter()->join(', ') }}</p></div>
                @endif
                @if($mapUrl)<a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="site-button site-button--secondary mt-5 w-full">View location</a>@endif
                <a href="{{ app(\App\Services\PublicSiteUrl::class)->offers($siteAgency) }}?destination={{ urlencode($destination->slug) }}" class="site-button mt-5 w-full">View journeys</a>
                <a href="{{ app(\App\Services\PublicSiteUrl::class)->page($siteAgency, 'contact') }}" class="site-button site-button--secondary mt-3 w-full">Plan a custom trip</a>
            </aside>
        </div>
    </section>

    @if($destination->practical_information)
        <section class="site-section pt-0"><div class="site-container"><div class="site-card p-7 sm:p-10"><div class="site-eyebrow">GOOD TO KNOW</div><h2 class="site-heading mt-3 text-3xl">Practical information</h2><p class="site-copy mt-5 whitespace-pre-line">{{ $destination->practical_information }}</p></div></div></section>
    @endif

    @if($destination->media->count() > 1)
        <section class="site-section pt-0"><div class="site-container"><div class="site-eyebrow">GALLERY</div><div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach($destination->media->skip(1) as $media)<img src="{{ $media->url }}" alt="{{ $media->alt_text ?? $destination->name }}" loading="lazy" class="site-card aspect-[4/3] h-full w-full object-cover">@endforeach</div></div></section>
    @endif

    <section class="site-section" style="background: var(--site-surface);"><div class="site-container"><div class="site-eyebrow">JOURNEYS IN {{ strtoupper($destination->name) }}</div><h2 class="site-heading mt-3 text-3xl sm:text-5xl">Ways to experience it</h2>@if($destination->offers->isEmpty())<div class="site-card mt-8 p-8"><p class="site-copy">No published journeys are available yet. Our travel designers can still create one around you.</p><a href="{{ app(\App\Services\PublicSiteUrl::class)->page($siteAgency, 'contact') }}" class="site-button mt-5">Start planning</a></div>@else<div class="site-card-grid mt-9">@foreach($destination->offers as $offer)@include('frontend.partials.offer-card', ['offer' => $offer])@endforeach</div>@endif</div></section>
</article>
@endsection
