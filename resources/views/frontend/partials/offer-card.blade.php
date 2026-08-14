@php($cover = $offer->media ?: $offer->destination?->media?->first())
<a href="{{ app(\App\Services\PublicSiteUrl::class)->offer($siteAgency ?? $offer->agency, $offer) }}" class="overflow-hidden border transition hover:-translate-y-0.5" style="background-color: var(--site-surface, #ffffff); border-color: var(--site-border, #f3f4f6); border-radius: var(--site-radius, 14px); box-shadow: var(--site-shadow, none);">
    <div class="aspect-video" style="background-color: var(--site-background, #f1f5f9);">
        @if($cover)
            <img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $offer->title }}" class="h-full w-full object-cover">
        @endif
    </div>
    <div class="p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold" style="color: var(--site-text, #111827); font-family: var(--site-heading-font, ui-sans-serif, system-ui, sans-serif);">{{ $offer->title }}</h2>
            @if($offer->is_special)
                <span class="rounded-full px-2 py-1 text-xs font-semibold" style="background-color: color-mix(in srgb, var(--site-accent, #f43f5e) 14%, transparent); color: var(--site-accent, #e11d48);">Special</span>
            @endif
        </div>
        <p class="mt-2 line-clamp-2 text-sm" style="color: var(--site-muted, #6b7280);">{{ $offer->description }}</p>
        <div class="mt-4 flex items-center justify-between text-sm">
            <span class="font-semibold" style="color: var(--site-primary, #059669);">{{ number_format((float) $offer->price, 2) }}</span>
            <span style="color: var(--site-muted, #9ca3af);">{{ $offer->duration_days }} days</span>
        </div>
    </div>
</a>
