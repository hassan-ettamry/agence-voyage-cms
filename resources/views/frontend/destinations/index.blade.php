@extends('frontend.layout')

@section('content')
<section class="site-section" style="background: var(--site-secondary); color: #fff;">
    <div class="site-container py-8 sm:py-14">
        <div class="site-eyebrow" style="color: var(--site-accent);">EXPLORE THE WORLD</div>
        <h1 class="site-heading mt-4 text-[clamp(3rem,7vw,6.5rem)]">Places that move you</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-white/75">Handpicked destinations, local insight, and journeys designed around how you want to travel.</p>
    </div>
</section>

<section class="site-section">
    <div class="site-container">
        <form method="get" action="{{ app(\App\Services\PublicSiteUrl::class)->destinations($siteAgency) }}" class="site-card grid gap-4 p-5 md:grid-cols-[1fr_220px_auto_auto]" role="search">
            <label><span class="sr-only">Search destinations</span><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search a place or experience" class="min-h-12 w-full rounded-[var(--site-radius)] border border-slate-300 bg-white px-4 outline-none focus:border-[var(--site-primary)]"></label>
            <label><span class="sr-only">Country</span><select name="country" class="min-h-12 w-full rounded-[var(--site-radius)] border border-slate-300 bg-white px-4"><option value="">All countries</option>@foreach($countries as $country)<option value="{{ $country }}" @selected(($filters['country'] ?? '') === $country)>{{ $country }}</option>@endforeach</select></label>
            <label class="flex min-h-12 items-center gap-2 px-2"><input type="checkbox" name="featured" value="1" @checked(($filters['featured'] ?? '') === '1')><span class="font-semibold">Featured only</span></label>
            <button class="site-button" type="submit">Find places</button>
        </form>

        <div class="mt-10 flex items-end justify-between gap-5"><div><div class="site-eyebrow">{{ $destinations->total() }} RESULTS</div><h2 class="site-heading mt-2 text-3xl sm:text-5xl">Choose your next chapter</h2></div>@if(array_filter($filters))<a href="{{ app(\App\Services\PublicSiteUrl::class)->destinations($siteAgency) }}" class="font-bold text-[var(--site-primary)]">Clear filters</a>@endif</div>

        @if($destinations->isEmpty())
            <div class="site-card mt-10 p-10 text-center"><h3 class="site-heading text-2xl">No destinations found</h3><p class="site-copy mt-3">Try a broader search or clear the current filters.</p><a href="{{ app(\App\Services\PublicSiteUrl::class)->destinations($siteAgency) }}" class="site-button mt-6">View all destinations</a></div>
        @else
            <div class="site-card-grid mt-10">
                @foreach($destinations as $destination)
                    @php($cover = $destination->media->first())
                    <a href="{{ app(\App\Services\PublicSiteUrl::class)->destination($siteAgency, $destination) }}" class="site-card group overflow-hidden no-underline">
                        <div class="aspect-[4/3] overflow-hidden bg-slate-100">@if($cover)<img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@endif</div>
                        <div class="p-6"><div class="site-eyebrow">{{ $destination->country }}</div><h3 class="site-heading mt-2 text-2xl">{{ $destination->name }}</h3><p class="site-copy mt-3 line-clamp-3">{{ $destination->description }}</p><span class="mt-5 inline-flex font-bold text-[var(--site-primary)]">Explore destination &rarr;</span></div>
                    </a>
                @endforeach
            </div>
            <div class="mt-10">{{ $destinations->links() }}</div>
        @endif
    </div>
</section>
@endsection
