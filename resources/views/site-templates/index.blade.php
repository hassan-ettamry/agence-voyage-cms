@extends('layouts.admin')

@section('topbar')
<x-layout.topbar>
    <x-slot name="left">
        <a href="{{ route('pages.index') }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
            </svg>
            Pages
        </a>
    </x-slot>
</x-layout.topbar>
@endsection

@section('content')
@php
    $preferredOrder = collect([
        'premium-escapes' => 0,
        'travel-agency-starter' => 1,
        'mountain-adventure' => 2,
        'city-escape' => 3,
        'beach-paradise' => 4,
        'culture-journey' => 5,
    ]);

    $templates = $templates
        ->sortBy(fn ($template) => $preferredOrder->get($template->slug, 100 + abs(crc32($template->slug))))
        ->values();

    $themeStyles = [
        'sunset-luxe' => [
            'brand' => 'Sunset Luxe',
            'dot' => '#e99a83',
            'image' => 'images/site-templates/sunset-luxe.png',
            'heading' => 'Premium stays and private tours',
            'description' => 'A polished site template for premium tours, special offers, and custom travel requests.',
            'nav' => ['Home', 'Destinations', 'Offers'],
        ],
        'ocean-blue' => [
            'brand' => 'Ocean Blue',
            'dot' => '#4b86ea',
            'image' => 'images/site-templates/ocean-blue.png',
            'heading' => 'Travel made simple',
            'description' => 'A complete starter website for a travel agency: home, destinations, offers, and contact.',
            'nav' => ['Home', 'Destinations', 'Offers'],
        ],
        'wild-nature' => [
            'brand' => 'Wild Nature',
            'dot' => '#6c9b78',
            'image' => 'images/site-templates/mountain-adventure.png',
            'heading' => 'Adventure is calling',
            'description' => 'Perfect template for adventure tours, hiking, and nature experiences.',
            'nav' => ['Home', 'Trips', 'About'],
        ],
        'urban-blue' => [
            'brand' => 'City Escape',
            'dot' => '#4f7ea0',
            'image' => 'images/site-templates/city-escape.png',
            'heading' => 'Urban breaks with style',
            'description' => 'City tours, galleries, events, and fast trip planning for modern travelers.',
            'nav' => ['Home', 'Tours', 'Gallery'],
        ],
        'coastal-glow' => [
            'brand' => 'Beach Paradise',
            'dot' => '#47aab5',
            'image' => 'images/site-templates/beach-paradise.png',
            'heading' => 'Blue water, easy bookings',
            'description' => 'A bright package layout for beach stays, resort offers, and island tours.',
            'nav' => ['Home', 'Packages', 'About'],
        ],
        'heritage-rose' => [
            'brand' => 'Culture Journey',
            'dot' => '#a66a86',
            'image' => 'images/site-templates/culture-journey.png',
            'heading' => 'Culture-led travel stories',
            'description' => 'An editorial template for heritage routes, local guides, and travel articles.',
            'nav' => ['Home', 'Destinations', 'Blog'],
        ],
    ];

    $draftSlugs = collect([
        'mountain-adventure',
        'desert-retreat',
        'nordic-lights',
        'safari-trails',
        'wellness-retreat',
    ]);

    $fallbackCurrentTemplateId = $currentTemplateId
        ?: optional($templates->firstWhere('slug', 'premium-escapes'))->id
        ?: optional($templates->first())->id;

    $countSections = function (array $nodes) use (&$countSections): int {
        $total = 0;

        foreach ($nodes as $node) {
            if (($node['type'] ?? null) === 'section') {
                $total++;
            }

            $total += $countSections($node['children'] ?? []);
        }

        return $total;
    };

    $templateCards = $templates->map(function ($template) use ($fallbackCurrentTemplateId, $countSections, $draftSlugs, $themeStyles) {
        $pages = collect($template->pages ?? []);
        $sections = $pages->sum(fn ($page) => $countSections($page['structure'] ?? []));
        $pageNames = $pages->pluck('title')->filter()->values();
        $theme = $template->theme;
        $themeSlug = $theme?->slug ?? 'ocean-blue';
        $style = $themeStyles[$themeSlug] ?? $themeStyles['ocean-blue'];
        $isCurrent = $fallbackCurrentTemplateId === $template->id;
        $status = $isCurrent ? 'active' : ($draftSlugs->contains($template->slug) ? 'draft' : 'ready');
        $preview = ltrim($template->preview ?: $style['image'], '/');

        return [
            'model' => $template,
            'pages' => $pages,
            'sections' => $sections,
            'pageNames' => $pageNames,
            'theme' => $theme,
            'themeSlug' => $themeSlug,
            'style' => $style,
            'isCurrent' => $isCurrent,
            'status' => $status,
            'preview' => $preview,
            'search' => \Illuminate\Support\Str::lower($template->name.' '.$template->slug.' '.$template->description.' '.($theme?->name ?? '').' '.$pageNames->implode(' ')),
        ];
    });

    $templateCount = $templateCards->count();
    $activeCount = $templateCards->where('status', 'active')->count();
    $draftCount = $templateCards->where('status', 'draft')->count();
    $readyCount = max($templateCount - $activeCount - $draftCount, 0);
    $themeGroups = $templates->filter(fn ($template) => $template->theme)->groupBy(fn ($template) => $template->theme->slug);

    $stats = [
        ['label' => 'All Templates', 'value' => $templateCount, 'sub' => '100% of templates', 'iconBg' => 'bg-violet-100', 'iconText' => 'text-violet-600', 'numColor' => 'text-violet-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 2 2 7l10 5 10-5-10-5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m2 17 10 5 10-5"/><path stroke-linecap="round" stroke-linejoin="round" d="m2 12 10 5 10-5"/>'],
        ['label' => 'Active', 'value' => $activeCount, 'sub' => 'Currently in use', 'iconBg' => 'bg-emerald-100', 'iconText' => 'text-emerald-600', 'numColor' => 'text-emerald-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4"/>'],
        ['label' => 'Ready', 'value' => $readyCount, 'sub' => 'Published & ready', 'iconBg' => 'bg-blue-100', 'iconText' => 'text-blue-600', 'numColor' => 'text-blue-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15 9 12c.74-1.97 1.95-3.78 3.5-5 1.97-1.61 4.5-2.42 6.5-2 .42 2-.39 4.54-2 6.5-1.22 1.55-3.03 2.76-5 3.5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>'],
        ['label' => 'Draft', 'value' => $draftCount, 'sub' => 'Not finished yet', 'iconBg' => 'bg-amber-100', 'iconText' => 'text-amber-600', 'numColor' => 'text-amber-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 13H8M16 17H8M10 9H8"/>'],
    ];

    $statusLabels = [
        'active' => ['label' => 'ACTIVE', 'border' => 'border-emerald-300', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500'],
        'ready' => ['label' => 'READY', 'border' => 'border-blue-300', 'text' => 'text-blue-600', 'dot' => 'bg-blue-500'],
        'draft' => ['label' => 'DRAFT', 'border' => 'border-amber-300', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500'],
    ];
@endphp

<style>
    [data-site-template-toolbar] {
        display: grid;
        gap: 12px;
        align-items: center;
    }

    .site-template-toolbar__filters {
        display: flex;
        min-width: 0;
        gap: 4px;
        overflow-x: auto;
    }

    .site-template-toolbar__divider {
        display: none;
    }

    .site-template-toolbar__controls {
        display: grid;
        min-width: 0;
        grid-template-columns: minmax(0, 1fr);
        gap: 12px;
        align-items: center;
    }

    .site-template-toolbar__view {
        justify-self: start;
    }

    @media (min-width: 900px) {
        .site-template-toolbar__controls {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .site-template-toolbar__search {
            grid-column: 1 / -1;
        }
    }

    @media (min-width: 1150px) {
        .site-template-toolbar__controls {
            grid-template-columns: minmax(190px, 1fr) minmax(130px, 150px) minmax(145px, 165px) minmax(160px, 180px) auto;
        }

        .site-template-toolbar__search {
            grid-column: auto;
        }

        .site-template-toolbar__view {
            justify-self: end;
        }
    }

    @media (min-width: 1280px) {
        .site-template-toolbar__controls {
            grid-template-columns: minmax(220px, 1fr) minmax(150px, 170px) minmax(160px, 185px) minmax(170px, 200px) auto;
        }

        .site-template-toolbar__search {
            grid-column: auto;
        }

        .site-template-toolbar__view {
            justify-self: end;
        }
    }

    @media (min-width: 1700px) {
        [data-site-template-toolbar] {
            grid-template-columns: auto 1px minmax(0, 1fr);
        }

        .site-template-toolbar__divider {
            display: block;
            width: 1px;
            height: 40px;
            background: #e0e7f2;
        }
    }

    @media (min-width: 1280px) {
        [data-site-templates-page] .site-template-density {
            max-width: 1611px !important;
            zoom: 0.9;
        }
    }
</style>

<div class="-m-6 min-h-[calc(100%+3rem)] overflow-x-hidden bg-[#f8fafc] px-4 py-6 text-slate-950 sm:px-6 lg:px-[30px] lg:py-[32px]" data-site-templates-page>
    <div class="site-template-density mx-auto max-w-[1450px]">
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-100">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5"/>
                    </svg>
                </span>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 flex items-center gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-rose-100">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                    </svg>
                </span>
                {{ $errors->first() }}
            </div>
        @endif

        <header class="flex items-start justify-between gap-5">
            <div class="min-w-0">
                <h1 class="text-[24px] font-black leading-none tracking-[0] text-[#0b1736] md:text-[26px]">Site Templates</h1>
                <p class="mt-3 text-[13.5px] font-medium leading-none text-[#17294f]">Choose a template to build your website faster. You can customize everything later.</p>
            </div>

            <div class="inline-flex h-10 shrink-0 overflow-hidden rounded-[8px] shadow-[0_8px_20px_-8px_rgba(79,70,229,0.7)]">
                <button type="button" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#694ff0] to-[#4d43df] px-4 text-[12.5px] font-black text-white transition hover:brightness-105">
                    <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                    </svg>
                    New Template
                </button>
                <button type="button" class="grid w-9 place-items-center border-l border-white/20 bg-[#5545e5] text-white transition hover:brightness-105" aria-label="More options">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
            </div>
        </header>

        <section class="mt-[30px] grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($stats as $stat)
                <div class="h-[112px] rounded-[10px] border border-[#e0e7f2] bg-white px-[18px] py-[25px] shadow-[0_10px_30px_-18px_rgba(15,23,42,0.25)]">
                    <div class="flex items-center gap-[19px]">
                        <span class="grid h-[58px] w-[58px] shrink-0 place-items-center rounded-full {{ $stat['iconBg'] }} {{ $stat['iconText'] }}">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $stat['svg'] !!}</svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[25px] font-black leading-none {{ $stat['numColor'] }} tabular-nums">{{ $stat['value'] }}</div>
                            <div class="mt-2 text-[13px] font-black leading-none text-[#101b39]">{{ $stat['label'] }}</div>
                            <div class="mt-2 text-[12px] font-medium leading-none text-[#647293]">{{ $stat['sub'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="mt-[18px] min-h-[78px] rounded-[10px] border border-[#e0e7f2] bg-white p-[14px] shadow-[0_8px_26px_-20px_rgba(15,23,42,0.35)]" data-site-template-toolbar>
            <div class="site-template-toolbar__filters no-scrollbar" aria-label="Site template filters">
                <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-[8px] border border-violet-300 bg-violet-50 px-3 text-[12px] font-black text-violet-600 transition" data-template-filter="all">
                    All <span class="grid h-5 min-w-5 place-items-center rounded-full px-1 text-[10px] font-black text-violet-500">{{ $templateCount }}</span>
                </button>
                <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-[8px] border border-[#e0e7f2] bg-white px-3 text-[12px] font-black text-[#263a60] transition hover:border-violet-200 hover:bg-violet-50 hover:text-violet-600" data-template-filter="active">
                    Active <span class="grid h-5 min-w-5 place-items-center rounded-full bg-slate-100 px-1 text-[10px] font-black text-[#7784a0]">{{ $activeCount }}</span>
                </button>
                <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-[8px] border border-[#e0e7f2] bg-white px-3 text-[12px] font-black text-[#263a60] transition hover:border-violet-200 hover:bg-violet-50 hover:text-violet-600" data-template-filter="ready">
                    Ready <span class="grid h-5 min-w-5 place-items-center rounded-full bg-slate-100 px-1 text-[10px] font-black text-[#7784a0]">{{ $readyCount }}</span>
                </button>
                <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-[8px] border border-[#e0e7f2] bg-white px-3 text-[12px] font-black text-[#263a60] transition hover:border-violet-200 hover:bg-violet-50 hover:text-violet-600" data-template-filter="draft">
                    Draft <span class="grid h-5 min-w-5 place-items-center rounded-full bg-slate-100 px-1 text-[10px] font-black text-[#7784a0]">{{ $draftCount }}</span>
                </button>
            </div>

            <span class="site-template-toolbar__divider" aria-hidden="true"></span>

            <div class="site-template-toolbar__controls">
                <label class="site-template-toolbar__search flex h-10 min-w-0 items-center gap-3 rounded-[8px] border border-[#dce5f1] bg-white px-4 text-[#91a0bc] transition focus-within:border-violet-300 focus-within:ring-2 focus-within:ring-violet-100">
                    <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input type="search" class="w-full min-w-0 appearance-none border-0 bg-transparent p-0 text-[12px] font-semibold text-slate-700 outline-none placeholder:text-[#91a0bc] focus:border-transparent focus:outline-none focus:ring-0" placeholder="Search templates..." data-template-search>
                </label>

                <label class="flex h-10 min-w-0 items-center gap-2 rounded-[8px] border border-[#dce5f1] bg-white px-3 text-[12px] font-black text-[#5c6c89]">
                    Theme:
                    <select class="w-full min-w-0 border-0 bg-transparent p-0 pr-5 text-[12px] font-black text-[#111b35] outline-none focus:border-transparent focus:outline-none focus:ring-0" data-template-theme>
                        <option value="all">All themes</option>
                        @foreach($themeGroups as $themeSlug => $themeTemplates)
                            <option value="{{ $themeSlug }}">{{ $themeTemplates->first()->theme?->name ?? \Illuminate\Support\Str::headline($themeSlug) }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="flex h-10 min-w-0 items-center gap-2 rounded-[8px] border border-[#dce5f1] bg-white px-3 text-[12px] font-black text-[#5c6c89]">
                    Status:
                    <select class="w-full min-w-0 border-0 bg-transparent p-0 pr-5 text-[12px] font-black text-[#111b35] outline-none focus:border-transparent focus:outline-none focus:ring-0" data-template-status>
                        <option value="all">All statuses</option>
                        <option value="active">Active</option>
                        <option value="ready">Ready</option>
                        <option value="draft">Draft</option>
                    </select>
                </label>

                <label class="flex h-10 min-w-0 items-center gap-2 rounded-[8px] border border-[#dce5f1] bg-white px-3 text-[12px] font-black text-[#5c6c89]">
                    Sort:
                    <select class="w-full min-w-0 border-0 bg-transparent p-0 pr-5 text-[12px] font-black text-[#111b35] outline-none focus:border-transparent focus:outline-none focus:ring-0" data-template-sort>
                        <option value="recommended">Recommended</option>
                        <option value="active">Active first</option>
                        <option value="name">Name</option>
                        <option value="pages">Most pages</option>
                        <option value="sections">Most sections</option>
                    </select>
                </label>

                <div class="site-template-toolbar__view inline-flex h-10 shrink-0 overflow-hidden rounded-[8px] border border-[#d8e1ef] bg-white">
                    <button type="button" class="grid w-11 place-items-center bg-violet-50 text-violet-600" data-template-view="grid" title="Grid view" aria-label="Grid view">
                        <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                        </svg>
                    </button>
                    <button type="button" class="grid w-11 place-items-center border-l border-[#e0e7f2] text-[#8a99b5] transition hover:bg-slate-50 hover:text-slate-700" data-template-view="list" title="List view" aria-label="List view">
                        <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 6h13M8 12h13M8 18h13"/>
                            <path d="M3 6h.01M3 12h.01M3 18h.01"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <section class="mt-[18px]" data-template-results-shell>
            <div class="grid gap-[18px] md:grid-cols-2 xl:grid-cols-3" data-template-grid>
                @forelse($templateCards as $card)
                    @php
                        $template = $card['model'];
                        $isCurrent = $card['isCurrent'];
                        $requiresThemeReset = $hasThemeOverrides && ! $isCurrent;
                        $pageNames = $card['pageNames'];
                        $status = $card['status'];
                        $statusMeta = $statusLabels[$status];
                        $style = $card['style'];
                        $themeDotColor = $style['dot'];
                        $navItems = collect($style['nav'])->take(3);
                        $ctaLabel = $status === 'draft' || $isCurrent ? 'Continue editing' : 'Use this template';

                        $previewPayload = [
                            'name' => $template->name,
                            'slug' => $template->slug,
                            'description' => $template->description,
                            'theme' => $card['theme']?->name ?? 'No theme',
                            'pages' => $pageNames->values()->all(),
                            'pagesCount' => $card['pages']->count(),
                            'sectionsCount' => $card['sections'],
                            'brand' => $style['brand'],
                            'heading' => $style['heading'],
                            'image' => asset($card['preview']),
                        ];
                    @endphp

                    <article
                        class="group flex min-h-[468px] min-w-0 flex-col overflow-hidden rounded-[12px] border bg-white shadow-[0_6px_24px_-18px_rgba(15,23,42,0.45)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_34px_-24px_rgba(15,23,42,0.45)] {{ $isCurrent ? 'border-violet-300 ring-1 ring-violet-200' : 'border-[#dfe7f2]' }}"
                        data-template-card
                        data-active="{{ $isCurrent ? '1' : '0' }}"
                        data-status="{{ $status }}"
                        data-theme="{{ $card['themeSlug'] }}"
                        data-name="{{ \Illuminate\Support\Str::lower($template->name) }}"
                        data-pages="{{ $card['pages']->count() }}"
                        data-sections="{{ $card['sections'] }}"
                        data-search="{{ $card['search'] }}">

                        <div class="relative p-[14px] pb-0">
                            <div class="overflow-hidden rounded-[7px] bg-slate-950">
                                <div class="template-preview-chrome flex h-[28px] items-center justify-between gap-3 bg-[#f7f9fc] px-[11px]">
                                    <div class="flex items-center gap-[7px]">
                                        <span class="h-[7px] w-[7px] rounded-full bg-[#ff6b79]"></span>
                                        <span class="h-[7px] w-[7px] rounded-full bg-[#ffc84d]"></span>
                                        <span class="h-[7px] w-[7px] rounded-full bg-[#48d18a]"></span>
                                    </div>
                                    <span class="template-status-pill inline-flex h-[22px] items-center gap-1 rounded-full border {{ $statusMeta['border'] }} bg-[#f7f9fc] px-3 text-[10px] font-black uppercase tracking-[0] {{ $statusMeta['text'] }}">
                                        <span class="h-[6px] w-[6px] rounded-full {{ $statusMeta['dot'] }}"></span>
                                        {{ $statusMeta['label'] }}
                                    </span>
                                </div>

                                <div class="relative h-[205px] overflow-hidden text-white">
                                    <img src="{{ asset($card['preview']) }}" alt="{{ $template->name }} preview" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/78 via-slate-950/28 to-slate-950/6"></div>
                                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-slate-950/60 to-transparent"></div>

                                    <div class="relative z-10 flex items-center justify-between gap-3 px-[20px] pt-[17px] text-[9px] font-black uppercase tracking-[0] text-white">
                                        <span>{{ \Illuminate\Support\Str::upper($style['brand']) }}</span>
                                        <span class="hidden gap-[20px] sm:flex">
                                            @foreach($navItems as $navItem)
                                                <span>{{ \Illuminate\Support\Str::upper($navItem) }}</span>
                                            @endforeach
                                        </span>
                                    </div>

                                    <div class="absolute bottom-[16px] left-[20px] z-10 max-w-[255px]">
                                        <h2 class="max-w-[240px] text-[20px] font-black leading-[1.05] tracking-[0] text-white">{{ $style['heading'] }}</h2>
                                        <p class="mt-[12px] max-w-[250px] text-[11px] font-medium leading-[1.35] text-white/88">{{ \Illuminate\Support\Str::limit($style['description'], 96) }}</p>
                                        <span class="mt-[14px] inline-flex h-[28px] items-center rounded-[5px] bg-white px-[13px] text-[10.5px] font-black text-slate-950 shadow-sm">Explore -></span>
                                    </div>

                                    <div class="absolute bottom-[12px] right-[25px] h-[34px] w-[34px] rounded-[9px] border border-white/25 bg-white/5"></div>
                                </div>
                            </div>

                        </div>

                        <div class="flex flex-1 flex-col gap-[14px] px-[20px] pb-[16px] pt-[14px]">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h2 class="truncate text-[17px] font-black leading-none tracking-[0] text-[#0f1835]">{{ $template->name }}</h2>
                                    <p class="mt-[13px] flex min-w-0 items-center gap-2 text-[12px] font-semibold leading-none text-[#536480]">
                                        <span class="truncate">{{ $card['theme']?->name ?? 'No theme' }}</span>
                                        <span class="text-[#a7b1c5]">&bull;</span>
                                        <span class="truncate">Full site template</span>
                                    </p>
                                </div>
                                <button type="button" class="grid h-7 w-7 shrink-0 place-items-center rounded text-[#29466f] transition hover:bg-slate-100" aria-label="More options">
                                    <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="grid h-[28px] w-[28px] shrink-0 place-items-center rounded-[5px] bg-[#f2f5fa] text-[#667793]">
                                        <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 leading-none">
                                        <div class="text-[12px] font-black text-[#0f1835] tabular-nums">{{ $card['pages']->count() }}</div>
                                        <div class="mt-1 text-[10px] font-medium text-[#61708b]">Pages</div>
                                    </div>
                                </div>
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="grid h-[28px] w-[28px] shrink-0 place-items-center rounded-[5px] bg-[#f2f5fa] text-[#667793]">
                                        <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20V8l8-4 8 4v12"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 18v-7l4-2 4 2v7"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 leading-none">
                                        <div class="text-[12px] font-black text-[#0f1835] tabular-nums">{{ $card['sections'] }}</div>
                                        <div class="mt-1 text-[10px] font-medium text-[#61708b]">Sections</div>
                                    </div>
                                </div>
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="grid h-[28px] w-[28px] shrink-0 place-items-center rounded-full" style="background: {{ $themeDotColor }}28;">
                                        <span class="h-[16px] w-[16px] rounded-full" style="background: {{ $themeDotColor }};"></span>
                                    </span>
                                    <div class="min-w-0 leading-none">
                                        <div class="truncate text-[11px] font-black text-[#0f1835]">{{ $card['theme']?->name ?? 'None' }}</div>
                                        <div class="mt-1 text-[10px] font-medium text-[#61708b]">Theme</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @forelse($pageNames->take(4) as $pageName)
                                    <span class="inline-flex h-[24px] items-center rounded-[5px] bg-[#f2f5fa] px-3 text-[10.5px] font-black text-[#182746]">{{ $pageName }}</span>
                                @empty
                                    <span class="inline-flex h-[24px] items-center rounded-[5px] bg-[#f2f5fa] px-3 text-[10.5px] font-black text-[#6b7890]">No pages listed</span>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('site-templates.apply', $template) }}" class="mt-auto" data-template-apply-form data-requires-replace="{{ $hasPages ? '1' : '0' }}" data-requires-theme-reset="{{ $requiresThemeReset ? '1' : '0' }}" data-template-name="{{ $template->name }}">
                                @csrf
                                @if($hasPages)
                                    <input type="hidden" name="confirm_replace" value="1" data-template-confirm-replace>
                                @endif
                                @if($requiresThemeReset)
                                    <input type="hidden" name="confirm_theme_reset" value="1" data-template-confirm-theme-reset>
                                @endif

                                <div class="grid grid-cols-2 gap-[16px]">
                                    <button type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-[7px] border border-violet-300 bg-white px-3 text-[12px] font-black text-violet-600 transition hover:bg-violet-50" data-template-preview-button data-template-preview-id="{{ $template->id }}" aria-label="Preview {{ $template->name }}">
                                        <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Preview
                                    </button>
                                    <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-[7px] bg-gradient-to-r from-violet-600 to-indigo-600 px-3 text-[12px] font-black text-white shadow-[0_8px_18px_-10px_rgba(79,70,229,0.75)] transition hover:brightness-105" data-template-submit>
                                        @if($status === 'draft' || $isCurrent)
                                            <svg class="h-[16px] w-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"/>
                                            </svg>
                                        @endif
                                        {{ $ctaLabel }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </article>

                    <script type="application/json" id="site-template-preview-{{ $template->id }}">@json($previewPayload)</script>
                @empty
                    <div class="rounded-[12px] border border-dashed border-slate-300 bg-white p-10 text-center">
                        <h2 class="text-lg font-black text-slate-950">No active templates available</h2>
                        <p class="mt-2 text-sm text-slate-500">Add active site templates from the database seeders or admin tools, then return here.</p>
                    </div>
                @endforelse
            </div>

            <div class="hidden rounded-[12px] border border-dashed border-slate-300 bg-white p-10 text-center" data-template-empty>
                <h2 class="text-lg font-black text-slate-950">No templates match your filters</h2>
                <p class="mt-2 text-sm text-slate-500">Try another search term, filter, or sort option.</p>
            </div>
        </section>

        <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" data-template-preview-modal aria-hidden="true">
            <div class="w-full max-w-5xl overflow-hidden rounded-[12px] bg-white shadow-2xl">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
                    <div class="min-w-0">
                        <div class="text-[11px] font-black uppercase tracking-wide text-indigo-500" data-preview-theme>Site template</div>
                        <h3 class="truncate text-xl font-black tracking-[0] text-slate-950" data-preview-title>Template preview</h3>
                    </div>
                    <button type="button" class="grid h-9 w-9 place-items-center rounded-[8px] border border-slate-200 text-slate-500 transition hover:bg-slate-50 hover:text-slate-900" data-template-preview-close aria-label="Close preview">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>

                <div class="grid max-h-[78vh] gap-5 overflow-y-auto p-5 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <div class="overflow-hidden rounded-[10px] border border-slate-200 bg-white">
                        <div class="flex items-center gap-1.5 bg-slate-950 px-4 py-3">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-300"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 h-6 flex-1 rounded-full bg-white/10 text-center text-[10px] font-bold leading-6 text-slate-300" data-preview-url>agency-site.test</span>
                        </div>
                        <div class="relative min-h-[390px] overflow-hidden px-8 py-10 text-white" data-preview-hero>
                            <img src="" alt="" class="absolute inset-0 h-full w-full object-cover" data-preview-image>
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/78 via-slate-950/35 to-slate-950/10"></div>
                            <div class="relative z-10 flex items-center justify-between gap-4 border-b border-white/10 pb-5">
                                <strong data-preview-brand>Travel Agency</strong>
                                <div class="hidden gap-5 text-xs font-bold text-white/80 sm:flex">
                                    <span>Home</span>
                                    <span>Destinations</span>
                                    <span>Offers</span>
                                    <span>Contact</span>
                                </div>
                            </div>
                            <div class="relative z-10 mt-16 max-w-xl">
                                <h2 class="text-4xl font-black leading-tight tracking-[0]" data-preview-heading>Travel theme preview.</h2>
                                <p class="mt-4 max-w-lg text-sm leading-6 text-white/80" data-preview-description>A complete site structure for your travel agency.</p>
                                <span class="mt-6 inline-flex h-10 items-center rounded-[8px] bg-white px-4 text-sm font-black text-slate-950">Explore -></span>
                            </div>
                        </div>
                    </div>

                    <aside class="space-y-4">
                        <div class="rounded-[10px] border border-slate-200 bg-slate-50 p-4">
                            <div class="text-[11px] font-black uppercase tracking-wide text-slate-400">Included pages</div>
                            <div class="mt-3 space-y-2" data-preview-pages></div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-[10px] border border-slate-200 bg-white p-4">
                                <div class="text-[11px] font-black uppercase tracking-wide text-slate-400">Pages</div>
                                <div class="mt-1 text-2xl font-black text-slate-950" data-preview-page-count>0</div>
                            </div>
                            <div class="rounded-[10px] border border-slate-200 bg-white p-4">
                                <div class="text-[11px] font-black uppercase tracking-wide text-slate-400">Sections</div>
                                <div class="mt-1 text-2xl font-black text-slate-950" data-preview-section-count>0</div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-site-templates-page]');
    if (!root) return;

    const cards = Array.from(root.querySelectorAll('[data-template-card]'));
    const grid = root.querySelector('[data-template-grid]');
    const empty = root.querySelector('[data-template-empty]');
    const filters = Array.from(root.querySelectorAll('[data-template-filter]'));
    const search = root.querySelector('[data-template-search]');
    const sort = root.querySelector('[data-template-sort]');
    const themeSelect = root.querySelector('[data-template-theme]');
    const statusSelect = root.querySelector('[data-template-status]');
    const viewButtons = Array.from(root.querySelectorAll('[data-template-view]'));
    let activeFilter = 'all';

    const setActiveFilterStyles = () => {
        filters.forEach((button) => {
            const isActive = button.dataset.templateFilter === activeFilter;
            button.classList.toggle('border-violet-300', isActive);
            button.classList.toggle('bg-violet-50', isActive);
            button.classList.toggle('text-violet-600', isActive);
            button.classList.toggle('border-[#e0e7f2]', !isActive);
            button.classList.toggle('bg-white', !isActive);
            button.classList.toggle('text-[#263a60]', !isActive);
        });
    };

    const matchesFilter = (card) => {
        if (activeFilter === 'all') return true;
        return card.dataset.status === activeFilter;
    };

    const matchesTheme = (card) => {
        const value = themeSelect?.value || 'all';
        return value === 'all' || card.dataset.theme === value;
    };

    const matchesStatus = (card) => {
        const value = statusSelect?.value || 'all';
        return value === 'all' || card.dataset.status === value;
    };

    const sortCards = () => {
        const mode = sort?.value || 'recommended';
        const sorted = [...cards].sort((a, b) => {
            if (mode === 'active') return Number(b.dataset.active) - Number(a.dataset.active);
            if (mode === 'name') return a.dataset.name.localeCompare(b.dataset.name);
            if (mode === 'pages') return Number(b.dataset.pages) - Number(a.dataset.pages);
            if (mode === 'sections') return Number(b.dataset.sections) - Number(a.dataset.sections);
            return Number(b.dataset.active) - Number(a.dataset.active);
        });

        sorted.forEach((card) => grid.appendChild(card));
    };

    const applyFilters = () => {
        const query = (search?.value || '').trim().toLowerCase();
        let visible = 0;

        sortCards();
        cards.forEach((card) => {
            const isVisible = matchesFilter(card)
                && matchesTheme(card)
                && matchesStatus(card)
                && (!query || (card.dataset.search || '').includes(query));

            card.classList.toggle('hidden', !isVisible);
            if (isVisible) visible++;
        });

        empty?.classList.toggle('hidden', visible !== 0);
        setActiveFilterStyles();
    };

    filters.forEach((button) => {
        button.addEventListener('click', () => {
            activeFilter = button.dataset.templateFilter || 'all';
            applyFilters();
        });
    });

    search?.addEventListener('input', applyFilters);
    sort?.addEventListener('change', applyFilters);
    themeSelect?.addEventListener('change', applyFilters);
    statusSelect?.addEventListener('change', applyFilters);

    viewButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const view = button.dataset.templateView;
            viewButtons.forEach((item) => {
                const active = item === button;
                item.classList.toggle('bg-violet-50', active);
                item.classList.toggle('text-violet-600', active);
                item.classList.toggle('text-[#8a99b5]', !active);
            });

            if (view === 'list') {
                grid.className = 'grid grid-cols-1 gap-[18px]';
            } else {
                grid.className = 'grid gap-[18px] md:grid-cols-2 xl:grid-cols-3';
            }
        });
    });

    root.querySelectorAll('[data-template-apply-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const needsReplace = form.dataset.requiresReplace === '1';
            const needsThemeReset = form.dataset.requiresThemeReset === '1';
            const name = form.dataset.templateName || 'this template';
            const messages = [];
            if (needsReplace) messages.push('This will archive and replace the current pages and menus.');
            if (needsThemeReset) messages.push('This will reset the current theme customizations.');
            if (messages.length === 0) return;

            const confirmed = window.confirm(`Apply "${name}"?\n\n${messages.join('\n')}\n\nContinue?`);
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    const modal = root.querySelector('[data-template-preview-modal]');
    const closePreview = () => {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
    };

    root.querySelectorAll('[data-template-preview-button]').forEach((button) => {
        button.addEventListener('click', () => {
            const payloadElement = document.getElementById(`site-template-preview-${button.dataset.templatePreviewId}`);
            if (!payloadElement || !modal) return;

            const payload = JSON.parse(payloadElement.textContent || '{}');
            modal.querySelector('[data-preview-title]').textContent = payload.name || 'Template preview';
            modal.querySelector('[data-preview-theme]').textContent = payload.theme || 'Site template';
            modal.querySelector('[data-preview-url]').textContent = `${payload.slug || 'template'}.agency-site.test`;
            modal.querySelector('[data-preview-brand]').textContent = payload.brand || payload.theme || 'Travel Agency';
            modal.querySelector('[data-preview-heading]').textContent = payload.heading || payload.name || 'Travel template preview.';
            modal.querySelector('[data-preview-description]').textContent = payload.description || 'A complete site structure for your travel agency.';
            modal.querySelector('[data-preview-page-count]').textContent = payload.pagesCount || 0;
            modal.querySelector('[data-preview-section-count]').textContent = payload.sectionsCount || 0;

            const previewImage = modal.querySelector('[data-preview-image]');
            previewImage.src = payload.image || '';
            previewImage.alt = payload.name || 'Template preview';

            const pages = modal.querySelector('[data-preview-pages]');
            pages.innerHTML = '';
            (payload.pages || []).forEach((page) => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between rounded-[8px] border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700';
                item.innerHTML = `<span>${page}</span><span class="text-slate-300">/</span>`;
                pages.appendChild(item);
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
        });
    });

    root.querySelector('[data-template-preview-close]')?.addEventListener('click', closePreview);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) closePreview();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closePreview();
    });

    applyFilters();
});
</script>
@endsection
