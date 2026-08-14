@php
    $limit = max(1, min(12, (int) ($props['limit'] ?? 6)));
    $query = \App\Models\Destination::published()->with('media');
    if (($props['source'] ?? 'featured') === 'featured') $query->featured();
    $destinations = $query->latest()->limit($limit)->get();
@endphp

<section @if($isEditor) data-type="destination-carousel" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section overflow-hidden {{ $isEditor ? 'builder-node' : '' }}">
    <div class="site-container">
        <h2 class="site-heading text-[clamp(2.25rem,5vw,4rem)] font-bold">{{ $props['title'] ?? 'Explore remarkable places' }}</h2>
        <div class="mt-9 flex snap-x snap-mandatory gap-5 overflow-x-auto pb-6" style="scrollbar-width: thin;">
            @forelse($destinations as $destination)
                @php($cover = $destination->media->first())
                <a href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->destination($destination->agency, $destination) }}" @if($isEditor) onclick="return false" @endif class="site-card group relative min-h-[420px] min-w-[82%] snap-start overflow-hidden no-underline sm:min-w-[48%] lg:min-w-[31%]">
                    @if($cover)<img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">@endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/15 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                        <div class="text-xs font-bold uppercase tracking-[.15em] text-white/70">{{ $destination->country }}</div>
                        <h3 class="site-heading mt-2 text-3xl font-bold">{{ $destination->name }}</h3>
                    </div>
                </a>
            @empty
                <div class="site-card w-full border-dashed p-8 text-center" style="color: var(--site-muted);">{{ $isEditor ? 'Add published destinations to populate this carousel.' : 'New destinations are coming soon.' }}</div>
            @endforelse
        </div>
    </div>
</section>
