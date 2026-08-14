@php
    $limit = max(2, min(4, (int) ($props['limit'] ?? 3)));
    $offers = \App\Models\Offer::published()->with('destination')->orderByDesc('is_special')->latest()->limit($limit)->get();
@endphp

<section @if($isEditor) data-type="offer-comparison" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}">
    <div class="site-container">
        <h2 class="site-heading text-[clamp(2.25rem,5vw,4rem)] font-bold">{{ $props['title'] ?? 'Compare our journeys' }}</h2>
        <div class="mt-9 grid gap-5 lg:grid-cols-3">
            @forelse($offers as $offer)
                <article class="site-card flex flex-col p-6 sm:p-8">
                    @if($offer->is_special)<div class="site-eyebrow">Signature choice</div>@endif
                    <h3 class="site-heading mt-3 text-2xl font-bold">{{ $offer->title }}</h3>
                    <p class="mt-3 line-clamp-3 leading-7" style="color: var(--site-muted);">{{ $offer->description }}</p>
                    <dl class="mt-6 divide-y" style="border-color: var(--site-border);">
                        <div class="flex justify-between gap-4 py-3"><dt style="color: var(--site-muted);">Destination</dt><dd class="font-bold">{{ $offer->destination?->name ?? 'Flexible' }}</dd></div>
                        <div class="flex justify-between gap-4 py-3"><dt style="color: var(--site-muted);">Duration</dt><dd class="font-bold">{{ $offer->duration_days }} days</dd></div>
                        <div class="flex justify-between gap-4 py-3"><dt style="color: var(--site-muted);">From</dt><dd class="font-bold" style="color: var(--site-primary);">{{ number_format((float) $offer->price, 2) }}</dd></div>
                    </dl>
                    <a href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->offer($offer->agency, $offer) }}" @if($isEditor) onclick="return false" @endif class="site-button mt-7">{{ $props['buttonText'] ?? 'View journey' }}</a>
                </article>
            @empty
                <div class="site-card border-dashed p-8 text-center lg:col-span-3" style="color: var(--site-muted);">{{ $isEditor ? 'Add published offers to compare.' : 'New offers are coming soon.' }}</div>
            @endforelse
        </div>
    </div>
</section>
