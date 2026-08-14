@extends('frontend.layout')

@section('content')
<section class="site-section" style="background: var(--site-secondary); color: #fff;"><div class="site-container py-8 sm:py-14"><div class="site-eyebrow" style="color: var(--site-accent);">CURATED JOURNEYS</div><h1 class="site-heading mt-4 text-[clamp(3rem,7vw,6.5rem)]">Travel offers, made personal</h1><p class="mt-5 max-w-2xl text-lg leading-8 text-white/75">Compare inspiring itineraries and find the journey that matches your pace, interests, and budget.</p></div></section>

<section class="site-section"><div class="site-container">
    <form method="get" action="{{ app(\App\Services\PublicSiteUrl::class)->offers($siteAgency) }}" class="site-card grid gap-4 p-5 md:grid-cols-2 lg:grid-cols-4" role="search">
        <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Search</span><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Journey or experience" class="min-h-12 w-full rounded-[var(--site-radius)] border border-slate-300 px-4"></label>
        <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Destination</span><select name="destination" class="min-h-12 w-full rounded-[var(--site-radius)] border border-slate-300 bg-white px-4"><option value="">Anywhere</option>@foreach($destinations as $destination)<option value="{{ $destination->slug }}" @selected(($filters['destination'] ?? '') === $destination->slug)>{{ $destination->name }}</option>@endforeach</select></label>
        <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Price range</span><span class="flex gap-2"><input type="number" min="0" step="1" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min" class="min-h-12 min-w-0 w-full rounded-[var(--site-radius)] border border-slate-300 px-3"><input type="number" min="0" step="1" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max" class="min-h-12 min-w-0 w-full rounded-[var(--site-radius)] border border-slate-300 px-3"></span></label>
        <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Maximum duration</span><select name="duration" class="min-h-12 w-full rounded-[var(--site-radius)] border border-slate-300 bg-white px-4"><option value="">Any length</option>@foreach([3,7,10,14,21] as $days)<option value="{{ $days }}" @selected((string) ($filters['duration'] ?? '') === (string) $days)>Up to {{ $days }} days</option>@endforeach</select></label>
        <label class="flex min-h-12 items-center gap-2"><input type="checkbox" name="special" value="1" @checked(($filters['special'] ?? '') === '1')><span class="font-semibold">Special offers only</span></label>
        <div class="flex gap-3 lg:col-span-3 lg:justify-end"><a href="{{ app(\App\Services\PublicSiteUrl::class)->offers($siteAgency) }}" class="site-button site-button--secondary">Clear</a><button type="submit" class="site-button">Show journeys</button></div>
    </form>

    <div class="mt-10"><div class="site-eyebrow">{{ $offers->total() }} RESULTS</div><h2 class="site-heading mt-2 text-3xl sm:text-5xl">Find your perfect journey</h2></div>
    @if($offers->isEmpty())<div class="site-card mt-10 p-10 text-center"><h3 class="site-heading text-2xl">No journeys match these filters</h3><p class="site-copy mt-3">Change the destination, budget, or duration and try again.</p><a href="{{ app(\App\Services\PublicSiteUrl::class)->offers($siteAgency) }}" class="site-button mt-6">View all offers</a></div>@else<div class="site-card-grid mt-10">@foreach($offers as $offer)@include('frontend.partials.offer-card', ['offer' => $offer])@endforeach</div><div class="mt-10">{{ $offers->links() }}</div>@endif
</div></section>
@endsection
