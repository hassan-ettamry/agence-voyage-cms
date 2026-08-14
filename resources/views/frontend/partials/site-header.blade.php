@php
    $publicAgency = $siteAgency ?? (isset($page) ? $page->agency : null);
    $publicUrls = app(\App\Services\PublicSiteUrl::class);
    $homeUrl = $publicAgency ? $publicUrls->home($publicAgency) : url('/');
    $agencySettings = is_array($publicAgency?->settings) ? $publicAgency->settings : [];
    $ctaLabel = data_get($agencySettings, 'public_site.cta_label', 'Plan your journey');
    $ctaPath = data_get($agencySettings, 'public_site.cta_url', '/offers');
    $ctaUrl = $publicAgency ? $publicUrls->fromStoredUrl($publicAgency, $ctaPath) : '#';
    $logoPath = $publicAgency?->logo;
    $logoUrl = null;

    if (is_string($logoPath) && $logoPath !== '') {
        $logoUrl = preg_match('/^https?:\/\//i', $logoPath) === 1 || str_starts_with($logoPath, '/')
            ? $logoPath
            : \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath);
    }
@endphp

<header class="sticky top-0 z-50 border-b backdrop-blur-xl" data-site-navigation style="border-color: color-mix(in srgb, var(--site-border) 78%, transparent); background: color-mix(in srgb, var(--site-surface) 94%, transparent);">
    <div class="site-container flex min-h-[76px] items-center justify-between gap-6">
        <a href="{{ $homeUrl }}" class="group inline-flex min-w-0 items-center gap-3 no-underline" aria-label="{{ $publicAgency?->name ?? 'Travel agency' }} home">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $publicAgency?->name }}" class="h-11 w-auto max-w-[180px] object-contain">
            @else
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full text-white" style="background: var(--site-secondary);" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 17.5 9.5 11l4 4L21 7.5"/>
                        <path d="M14 7.5h7v7"/>
                    </svg>
                </span>
                <span class="truncate text-lg font-bold tracking-tight" style="color: var(--site-secondary); font-family: var(--site-heading-font);">
                    {{ $publicAgency?->name ?? 'Signature Travel' }}
                </span>
            @endif
        </a>

        <nav class="hidden items-center gap-7 lg:flex" aria-label="Primary navigation">
            @foreach($menu ?? [] as $item)
                <a href="{{ $item['url'] }}" class="text-sm font-bold no-underline transition hover:opacity-65" style="color: var(--site-text);">
                    {{ $item['title'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @if($ctaUrl)
                <a href="{{ $ctaUrl }}" class="site-button hidden sm:inline-flex">{{ $ctaLabel }}</a>
            @endif
            <button type="button" class="grid h-11 w-11 place-items-center rounded-md border lg:hidden" data-site-nav-toggle aria-expanded="false" aria-controls="site-mobile-navigation" aria-label="Open navigation" style="border-color: var(--site-border); color: var(--site-secondary);">
                <svg class="h-5 w-5" data-site-nav-open-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                <svg class="hidden h-5 w-5" data-site-nav-close-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m6 6 12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>
    </div>

    <nav id="site-mobile-navigation" class="hidden border-t lg:hidden" data-site-mobile-nav aria-label="Mobile navigation" style="border-color: var(--site-border); background: var(--site-surface);">
        <div class="site-container grid gap-1 py-4">
            @foreach($menu ?? [] as $item)
                <a href="{{ $item['url'] }}" class="rounded-md px-3 py-3 text-sm font-bold no-underline transition hover:bg-black/5" style="color: var(--site-text);">
                    {{ $item['title'] }}
                </a>
            @endforeach
            @if($ctaUrl)
                <a href="{{ $ctaUrl }}" class="site-button mt-3 sm:hidden">{{ $ctaLabel }}</a>
            @endif
        </div>
    </nav>
</header>
