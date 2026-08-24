@php
    $publicAgency = $siteAgency ?? (isset($page) ? $page->agency : null);
    $publicUrls = app(\App\Services\PublicSiteUrl::class);
    $agencySettings = is_array($publicAgency?->settings) ? $publicAgency->settings : [];
    $tagline = data_get($agencySettings, 'public_site.tagline', 'Tailor-made journeys, thoughtfully designed.');
    $socialLinks = collect(data_get($agencySettings, 'social_links', []))
        ->filter(fn ($url) => is_string($url) && preg_match('/^https?:\/\//i', $url) === 1);
    $legalLinks = $publicAgency ? [
        ['title' => 'Privacy policy', 'url' => $publicUrls->page($publicAgency, 'privacy-policy')],
        ['title' => 'Terms & conditions', 'url' => $publicUrls->page($publicAgency, 'terms')],
    ] : [];
@endphp

<footer class="mt-auto border-t text-sm" style="border-color: var(--site-border); background: var(--site-secondary); color: color-mix(in srgb, white 76%, transparent);">
    <div class="site-container grid gap-10 py-16 md:grid-cols-2 lg:grid-cols-[1.25fr_.75fr_.75fr_1fr]">
        <div class="max-w-sm">
            <a href="{{ $publicAgency ? $publicUrls->home($publicAgency) : url('/') }}" class="text-2xl font-bold text-white no-underline" style="font-family: var(--site-heading-font); letter-spacing: -.025em;">
                {{ $publicAgency?->name ?? 'Signature Travel' }}
            </a>
            <p class="mt-4 leading-7">{{ $tagline }}</p>
            @if($socialLinks->isNotEmpty())
                <div class="mt-5 flex flex-wrap gap-3" aria-label="Social media">
                    @foreach($socialLinks as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="rounded-md border border-white/20 px-3 py-2 text-xs font-bold uppercase tracking-wider text-white no-underline transition hover:bg-white/10">
                            {{ str($network)->headline() }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h2 class="text-base font-bold text-white">Explore</h2>
            <ul class="mt-4 space-y-3">
                @foreach($menu ?? [] as $item)
                    <li><a href="{{ $item['url'] }}" class="no-underline transition hover:text-white">{{ $item['title'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="text-base font-bold text-white">Information</h2>
            <ul class="mt-4 space-y-3">
                @foreach($legalLinks as $item)
                    <li><a href="{{ $item['url'] }}" class="no-underline transition hover:text-white">{{ $item['title'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="text-base font-bold text-white">Contact</h2>
            <address class="mt-4 space-y-3 not-italic leading-6">
                @if($publicAgency?->email)
                    <div><a href="mailto:{{ $publicAgency->email }}" class="break-all no-underline transition hover:text-white">{{ $publicAgency->email }}</a></div>
                @endif
                @if($publicAgency?->phone)
                    <div><a href="tel:{{ preg_replace('/[^+0-9]/', '', $publicAgency->phone) }}" class="no-underline transition hover:text-white">{{ $publicAgency->phone }}</a></div>
                @endif
                @if($publicAgency?->address)
                    <div>{{ $publicAgency->address }}</div>
                @endif
            </address>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="site-container flex flex-col gap-2 py-5 text-xs sm:flex-row sm:items-center sm:justify-between">
            <span>&copy; {{ date('Y') }} {{ $publicAgency?->name ?? 'Signature Travel' }}. All rights reserved.</span>
            <span>Travel experiences, thoughtfully curated.</span>
        </div>
    </div>
</footer>
