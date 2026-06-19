@extends('layouts.admin')

@section('topbar')
<x-layout.topbar>
    <x-slot name="left">
        <a href="{{ route('site-templates.index') }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
            </svg>
            Templates
        </a>
    </x-slot>
</x-layout.topbar>
@endsection

@section('content')
@php
    $defaults = config('site-theme.defaults', []);
    $currentTheme = $themes->firstWhere('id', $currentThemeId);
    $currentVariables = array_replace($defaults, $effectiveVariables ?? []);

    $preferredOrder = collect([
        'sunset-luxe' => 0,
        'ocean-blue' => 1,
        'mountain-escape' => 2,
        'city-journey' => 3,
        'desert-roads' => 4,
        'urban-life' => 5,
        'beach-paradise' => 6,
        'culture-journey' => 7,
        'wild-nature' => 8,
        'urban-blue' => 9,
        'coastal-glow' => 10,
        'heritage-rose' => 11,
    ]);

    $themes = $themes
        ->sortBy(fn ($theme) => $preferredOrder->get($theme->slug, 100 + abs(crc32($theme->slug))))
        ->take(12)
        ->values();

    $visuals = [
        'sunset-luxe' => [
            'brand' => 'Sunset Luxe',
            'heading' => 'Premium stays and private tours',
            'description' => 'A polished site template for premium tours, special offers, and custom travel requests',
            'image' => 'images/site-templates/sunset-luxe.png',
            'nav' => ['Home', 'Destinations', 'Offers', 'About'],
            'category' => 'Luxury',
            'pages' => 12,
            'sections' => 28,
        ],
        'ocean-blue' => [
            'brand' => 'Ocean Blue',
            'heading' => 'Travel made simple',
            'description' => 'A complete starter website for a travel agency: home, destinations, offers, and contact',
            'image' => 'images/site-templates/ocean-blue.png',
            'nav' => ['Home', 'Destinations', 'Offers', 'About'],
            'category' => 'Coastal',
            'pages' => 10,
            'sections' => 22,
        ],
        'mountain-escape' => [
            'brand' => 'Mountain Escape',
            'heading' => 'Adventure is calling',
            'description' => 'Perfect template for adventure tours, hiking, and nature experiences',
            'image' => 'images/site-templates/mountain-adventure.png',
            'nav' => ['Home', 'Tours', 'Blog', 'Contact'],
            'category' => 'Adventure',
            'pages' => 8,
            'sections' => 18,
        ],
        'city-journey' => [
            'brand' => 'City Journey',
            'heading' => 'Bright city breaks',
            'description' => 'A vibrant city travel theme for tours, galleries, and weekend packages',
            'image' => 'images/site-templates/city-escape.png',
            'nav' => ['Home', 'Tours', 'Gallery', 'Contact'],
            'category' => 'Urban',
            'pages' => 9,
            'sections' => 20,
        ],
        'desert-roads' => [
            'brand' => 'Desert Roads',
            'heading' => 'Slow journeys, warm light',
            'description' => 'Earthy travel storytelling for desert routes, private stays, and guides',
            'image' => 'images/site-templates/sunset-luxe.png',
            'nav' => ['Home', 'Tours', 'Blog', 'Contact'],
            'category' => 'Editorial',
            'pages' => 7,
            'sections' => 16,
        ],
        'urban-life' => [
            'brand' => 'Urban Life',
            'heading' => 'Night tours and city stays',
            'description' => 'A dark editorial preset for city tours, services, and local discoveries',
            'image' => 'images/site-templates/city-escape.png',
            'nav' => ['Home', 'Services', 'Blog', 'Contact'],
            'category' => 'Urban',
            'pages' => 11,
            'sections' => 24,
        ],
        'beach-paradise' => [
            'brand' => 'Beach Paradise',
            'heading' => 'Blue water, easy bookings',
            'description' => 'A clean booking theme for island packages, resorts, and coastal tours',
            'image' => 'images/site-templates/beach-paradise.png',
            'nav' => ['Home', 'Packages', 'About', 'Contact'],
            'category' => 'Coastal',
            'pages' => 8,
            'sections' => 21,
        ],
        'culture-journey' => [
            'brand' => 'Culture Journey',
            'heading' => 'Culture-led travel stories',
            'description' => 'An editorial preset for heritage routes, local guides, and articles',
            'image' => 'images/site-templates/culture-journey.png',
            'nav' => ['Home', 'Destinations', 'Blog', 'Contact'],
            'category' => 'Editorial',
            'pages' => 9,
            'sections' => 19,
        ],
        'wild-nature' => [
            'brand' => 'Wild Nature',
            'heading' => 'Trails built for discovery',
            'description' => 'A grounded nature preset for hikes, camps, and outdoor experiences',
            'image' => 'images/site-templates/mountain-adventure.png',
            'nav' => ['Home', 'Trips', 'About', 'Contact'],
            'category' => 'Adventure',
            'pages' => 8,
            'sections' => 18,
        ],
        'urban-blue' => [
            'brand' => 'Urban Blue',
            'heading' => 'Modern city escapes',
            'description' => 'A crisp blue preset for city tours, events, and fast trip planning',
            'image' => 'images/site-templates/city-escape.png',
            'nav' => ['Home', 'Tours', 'Gallery', 'Contact'],
            'category' => 'Urban',
            'pages' => 10,
            'sections' => 23,
        ],
        'coastal-glow' => [
            'brand' => 'Coastal Glow',
            'heading' => 'Packages with sunlight',
            'description' => 'A bright coastal preset for beach stays, resorts, and summer offers',
            'image' => 'images/site-templates/beach-paradise.png',
            'nav' => ['Home', 'Packages', 'About', 'Contact'],
            'category' => 'Coastal',
            'pages' => 8,
            'sections' => 20,
        ],
        'heritage-rose' => [
            'brand' => 'Heritage Rose',
            'heading' => 'Stories from every street',
            'description' => 'A warm editorial theme for cultural journeys and curated city guides',
            'image' => 'images/site-templates/culture-journey.png',
            'nav' => ['Home', 'Destinations', 'Blog', 'Contact'],
            'category' => 'Editorial',
            'pages' => 7,
            'sections' => 17,
        ],
    ];

    $statusSlugs = [
        'active' => collect(['sunset-luxe', 'coastal-glow', 'heritage-rose']),
        'draft' => collect(['mountain-escape', 'urban-life', 'wild-nature']),
        'customized' => collect(['city-journey']),
    ];

    $statusMeta = [
        'active' => ['label' => 'ACTIVE', 'pill' => 'bg-[#ffd43b] text-[#10203f]', 'dot' => 'bg-[#10203f]', 'text' => 'text-emerald-600', 'action' => 'bg-emerald-50 text-emerald-600'],
        'ready' => ['label' => 'READY', 'pill' => 'bg-[#2f8cff] text-white', 'dot' => 'bg-white', 'text' => 'text-blue-600', 'action' => 'bg-violet-600 text-white'],
        'customized' => ['label' => 'CUSTOMIZED', 'pill' => 'bg-violet-600 text-white', 'dot' => 'bg-white', 'text' => 'text-violet-600', 'action' => 'bg-violet-600 text-white'],
        'draft' => ['label' => 'DRAFT', 'pill' => 'bg-emerald-500 text-white', 'dot' => 'bg-white', 'text' => 'text-emerald-600', 'action' => 'bg-emerald-50 text-emerald-600'],
    ];

    $themeCards = $themes->map(function ($theme) use ($defaults, $currentThemeId, $hasOverrides, $visuals, $statusSlugs) {
        $variables = array_replace($defaults, $theme->variables ?? []);
        $visual = $visuals[$theme->slug] ?? [
            'brand' => $theme->name,
            'heading' => $theme->name.' style system',
            'description' => 'A polished visual preset for travel websites and booking pages',
            'image' => $theme->preview ?: 'images/site-templates/ocean-blue.png',
            'nav' => ['Home', 'Tours', 'Blog', 'Contact'],
            'category' => 'General',
            'pages' => 6,
            'sections' => 14,
        ];

        $isApplied = $currentThemeId === $theme->id;
        $isCustomized = ($isApplied && $hasOverrides) || $statusSlugs['customized']->contains($theme->slug);
        $status = $isCustomized && ! $isApplied
            ? 'customized'
            : ($isApplied || $statusSlugs['active']->contains($theme->slug)
                ? 'active'
                : ($statusSlugs['draft']->contains($theme->slug) ? 'draft' : 'ready'));

        $preview = ltrim($theme->preview ?: $visual['image'], '/');

        return [
            'model' => $theme,
            'variables' => $variables,
            'visual' => $visual,
            'status' => $status,
            'isApplied' => $isApplied,
            'isCustomized' => $isCustomized,
            'preview' => $preview,
            'search' => \Illuminate\Support\Str::lower($theme->name.' '.$theme->slug.' '.$visual['brand'].' '.$visual['heading'].' '.$visual['category'].' '.implode(' ', $variables)),
        ];
    });

    $themeCount = $themeCards->count();
    $activeCount = $themeCards->where('status', 'active')->count();
    $draftCount = $themeCards->where('status', 'draft')->count();
    $readyCount = $themeCards->whereNotIn('status', ['active', 'draft'])->count();
    $customizedCount = $themeCards->where('isCustomized', true)->count();
    $categories = $themeCards->pluck('visual.category')->unique()->sort()->values();

    $stats = [
        ['label' => 'All Themes', 'value' => $themeCount, 'sub' => 'Total available themes', 'iconBg' => 'bg-violet-100', 'iconText' => 'text-violet-600', 'numColor' => 'text-violet-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 2 2 7l10 5 10-5-10-5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m2 17 10 5 10-5"/><path stroke-linecap="round" stroke-linejoin="round" d="m2 12 10 5 10-5"/>'],
        ['label' => 'Active', 'value' => $activeCount, 'sub' => 'Currently in use', 'iconBg' => 'bg-emerald-100', 'iconText' => 'text-emerald-600', 'numColor' => 'text-emerald-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4"/>'],
        ['label' => 'Ready', 'value' => $readyCount, 'sub' => 'Ready to publish', 'iconBg' => 'bg-blue-100', 'iconText' => 'text-blue-600', 'numColor' => 'text-blue-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15 9 12c.74-1.97 1.95-3.78 3.5-5 1.97-1.61 4.5-2.42 6.5-2 .42 2-.39 4.54-2 6.5-1.22 1.55-3.03 2.76-5 3.5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>'],
        ['label' => 'Draft', 'value' => $draftCount, 'sub' => 'Work in progress', 'iconBg' => 'bg-amber-100', 'iconText' => 'text-amber-600', 'numColor' => 'text-amber-600', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 13H8M16 17H8M10 9H8"/>'],
    ];
@endphp

<style>
    [data-themes-toolbar] {
        display: grid;
        gap: 12px;
        align-items: center;
    }

    .themes-toolbar__filters {
        display: flex;
        min-width: 0;
        gap: 4px;
        overflow-x: auto;
    }

    .themes-toolbar__controls {
        display: grid;
        min-width: 0;
        grid-template-columns: minmax(0, 1fr);
        gap: 12px;
        align-items: center;
    }

    .themes-toolbar__view {
        justify-self: start;
    }

    .theme-card-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 18px;
    }

    @media (min-width: 900px) {
        .themes-toolbar__controls {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .themes-toolbar__search {
            grid-column: 1 / -1;
        }

        .theme-card-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1180px) {
        .themes-toolbar__controls {
            grid-template-columns: minmax(210px, 1fr) minmax(160px, 170px) minmax(170px, 190px) auto;
        }

        .themes-toolbar__search {
            grid-column: auto;
        }

        .themes-toolbar__view {
            justify-self: end;
        }
    }

    @media (min-width: 1500px) {
        [data-themes-toolbar] {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .theme-card-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        [data-themes-page] .themes-density {
            max-width: 1790px !important;
            zoom: 0.81;
        }
    }

    @supports not (zoom: 1) {
        @media (min-width: 1280px) {
            [data-themes-page] .themes-density {
                transform: scale(0.81);
                transform-origin: top center;
                width: 123.456%;
            }
        }
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>

<div class="-m-6 min-h-[calc(100%+3rem)] overflow-x-hidden bg-[#f8fafc] px-4 py-6 text-slate-950 sm:px-6 lg:px-[30px] lg:py-[32px]" data-themes-page>
    <div class="themes-density mx-auto max-w-[1450px]">
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-100">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
                </span>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 flex items-center gap-3 rounded-[10px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-rose-100">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                </span>
                {{ $errors->first() }}
            </div>
        @endif

        <header class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-[28px] font-black leading-[1.05] tracking-[0] text-[#0c1533]">Themes</h1>
                <p class="mt-[12px] max-w-[610px] text-[15px] font-medium leading-[1.55] text-[#344364]">
                    Choose a visual theme to style your website. You can customize colors, typography and components anytime.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 lg:pt-[5px]">
                @if($currentTheme)
                    <a href="{{ route('themes.customize') }}" class="inline-flex h-[44px] items-center justify-center gap-3 rounded-[8px] bg-violet-100 px-[24px] text-[13px] font-black text-violet-600 shadow-[0_8px_24px_-20px_rgba(124,58,237,0.5)] transition hover:bg-violet-200">
                        Customize current theme
                        <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </a>
                @endif
                <button type="button" class="inline-flex h-[44px] items-center justify-center gap-3 rounded-[8px] border border-[#dbe4f0] bg-white px-[23px] text-[13px] font-black text-[#0c1533] shadow-[0_8px_26px_-22px_rgba(15,23,42,0.35)] transition hover:border-violet-200 hover:text-violet-600" data-scroll-to-themes>
                    Browse presets
                    <svg class="h-[16px] w-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                </button>
            </div>
        </header>

        <section class="mt-[22px] grid gap-[18px] sm:grid-cols-2 xl:grid-cols-4">
            @foreach($stats as $stat)
                <article class="flex min-h-[122px] items-center gap-[24px] rounded-[10px] border border-[#e0e7f2] bg-white px-[28px] shadow-[0_8px_26px_-22px_rgba(15,23,42,0.45)]">
                    <span class="grid h-[64px] w-[64px] shrink-0 place-items-center rounded-full {{ $stat['iconBg'] }} {{ $stat['iconText'] }}">
                        <svg class="h-[31px] w-[31px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05">{!! $stat['svg'] !!}</svg>
                    </span>
                    <div class="min-w-0">
                        <div class="text-[30px] font-black leading-none tracking-[0] {{ $stat['numColor'] }}">{{ $stat['value'] }}</div>
                        <div class="mt-[7px] text-[14px] font-black leading-none text-[#111b39]">{{ $stat['label'] }}</div>
                        <div class="mt-[8px] text-[12px] font-medium leading-none text-[#647390]">{{ $stat['sub'] }}</div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="mt-[18px] min-h-[66px] rounded-[10px] border border-[#e0e7f2] bg-white p-[12px] shadow-[0_8px_26px_-20px_rgba(15,23,42,0.35)]" data-themes-toolbar>
            <div class="themes-toolbar__filters no-scrollbar" aria-label="Theme filters">
                @foreach([
                    ['key' => 'all', 'label' => 'All', 'count' => $themeCount],
                    ['key' => 'active', 'label' => 'Active', 'count' => $activeCount],
                    ['key' => 'ready', 'label' => 'Ready', 'count' => $readyCount],
                    ['key' => 'customized', 'label' => 'Customized', 'count' => $customizedCount],
                    ['key' => 'draft', 'label' => 'Draft', 'count' => $draftCount],
                ] as $index => $filter)
                    <button type="button"
                            class="inline-flex h-[42px] shrink-0 items-center gap-[10px] rounded-[8px] border px-[17px] text-[13px] font-black transition {{ $index === 0 ? 'border-violet-300 bg-violet-600 text-white shadow-[0_8px_18px_-14px_rgba(124,58,237,0.8)]' : 'border-transparent bg-[#f8fafc] text-[#182646] hover:border-violet-200 hover:bg-violet-50 hover:text-violet-600' }}"
                            data-theme-filter="{{ $filter['key'] }}">
                        {{ $filter['label'] }}
                        <span class="inline-flex min-w-[22px] justify-center rounded-full {{ $index === 0 ? 'bg-white/15 text-white' : 'bg-[#edf2f8] text-[#7f8ca3]' }} px-[7px] py-[3px] text-[11px] leading-none" data-filter-count>{{ $filter['count'] }}</span>
                    </button>
                @endforeach
            </div>

            <div class="themes-toolbar__controls">
                <label class="themes-toolbar__search flex h-[42px] min-w-0 items-center gap-3 rounded-[8px] border border-[#dbe4f0] bg-white px-[14px] text-[#8ba0bf] focus-within:border-violet-300 focus-within:ring-4 focus-within:ring-violet-100">
                    <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="search" class="w-full min-w-0 border-0 bg-transparent p-0 text-[13px] font-semibold text-[#1a2848] outline-none placeholder:text-[#8ba0bf] focus:border-transparent focus:outline-none focus:[outline:0] focus:ring-0" placeholder="Search themes..." data-theme-search>
                </label>

                <label class="flex h-[42px] min-w-0 items-center gap-2 rounded-[8px] border border-[#dbe4f0] bg-white px-[15px] text-[13px] font-black text-[#5e6f8c]">
                    <select class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] font-black text-[#0e1834] outline-none focus:border-transparent focus:outline-none focus:[outline:0] focus:ring-0" data-theme-category>
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="flex h-[42px] min-w-0 items-center gap-2 rounded-[8px] border border-[#dbe4f0] bg-white px-[15px] text-[13px] font-black text-[#5e6f8c]">
                    Sort:
                    <select class="min-w-0 flex-1 border-0 bg-transparent p-0 text-[13px] font-black text-[#0e1834] outline-none focus:border-transparent focus:outline-none focus:[outline:0] focus:ring-0" data-theme-sort>
                        <option value="recommended">Recommended</option>
                        <option value="name">Name</option>
                        <option value="active">Active first</option>
                        <option value="draft">Draft first</option>
                    </select>
                </label>

                <div class="themes-toolbar__view inline-flex h-[42px] overflow-hidden rounded-[8px] border border-[#dbe4f0] bg-white">
                    <button type="button" class="grid h-[42px] w-[49px] place-items-center bg-violet-50 text-violet-600" data-theme-view="grid" aria-label="Grid view">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    </button>
                    <button type="button" class="grid h-[42px] w-[49px] place-items-center border-l border-[#dbe4f0] text-[#8796ad] transition hover:bg-slate-50 hover:text-[#0e1834]" data-theme-view="list" aria-label="List view">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg>
                    </button>
                </div>
            </div>
        </section>

        <section class="mt-[22px]" data-theme-results-shell>
            <div id="theme-presets" class="theme-card-grid" data-theme-grid>
                @forelse($themeCards as $themeCard)
                    @php
                        $theme = $themeCard['model'];
                        $variables = $themeCard['variables'];
                        $visual = $themeCard['visual'];
                        $status = $themeCard['status'];
                        $meta = $statusMeta[$status];
                        $isApplied = $themeCard['isApplied'];
                        $swatches = collect(['primary', 'secondary', 'accent', 'surface', 'background'])->map(fn ($token) => $variables[$token] ?? '#e2e8f0');
                        $previewPayload = [
                            'name' => $theme->name,
                            'slug' => $theme->slug,
                            'brand' => $visual['brand'],
                            'heading' => $visual['heading'],
                            'description' => $visual['description'],
                            'image' => asset($themeCard['preview']),
                            'category' => $visual['category'],
                            'primary' => $variables['primary'] ?? '#2563eb',
                            'secondary' => $variables['secondary'] ?? '#0f172a',
                            'accent' => $variables['accent'] ?? '#38bdf8',
                            'surface' => $variables['surface'] ?? '#f8fafc',
                            'background' => $variables['background'] ?? '#ffffff',
                            'text' => $variables['text'] ?? '#0f172a',
                            'muted' => $variables['muted'] ?? '#64748b',
                            'border' => $variables['border'] ?? '#e2e8f0',
                        ];
                    @endphp

                    <article class="theme-card group flex min-w-0 flex-col overflow-hidden rounded-[10px] border bg-white shadow-[0_10px_28px_-22px_rgba(15,23,42,0.5)] transition hover:-translate-y-0.5 hover:shadow-[0_22px_42px_-28px_rgba(15,23,42,0.5)] {{ $isApplied ? 'border-violet-400 ring-1 ring-violet-200' : 'border-[#e0e7f2]' }}"
                             data-theme-card
                             data-status="{{ $status }}"
                             data-applied="{{ $isApplied ? '1' : '0' }}"
                             data-customized="{{ $themeCard['isCustomized'] ? '1' : '0' }}"
                             data-category="{{ \Illuminate\Support\Str::slug($visual['category']) }}"
                             data-name="{{ \Illuminate\Support\Str::lower($theme->name) }}"
                             data-search="{{ $themeCard['search'] }}">

                        <div class="relative h-[246px] overflow-hidden rounded-t-[9px] bg-slate-950">
                            <img src="{{ asset($themeCard['preview']) }}" alt="{{ $theme->name }} preview" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/82 via-slate-950/32 to-slate-950/8"></div>
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-slate-950/62 to-transparent"></div>

                            <div class="absolute left-[16px] top-[14px] z-10 inline-flex h-[23px] items-center gap-[6px] rounded-full px-[12px] text-[10px] font-black uppercase tracking-[0] {{ $meta['pill'] }}">
                                <span class="h-[6px] w-[6px] rounded-full {{ $meta['dot'] }}"></span>
                                {{ $meta['label'] }}
                            </div>

                            <button type="button" class="absolute right-[16px] top-[14px] z-10 grid h-[28px] w-[28px] place-items-center rounded-full border border-blue-200 bg-white text-blue-500 shadow-sm" aria-label="Favorite theme">
                                <svg class="h-[16px] w-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="m12 2.5 2.9 5.88 6.5.94-4.7 4.58 1.1 6.47L12 17.32l-5.8 3.05 1.1-6.47-4.7-4.58 6.5-.94L12 2.5Z"/></svg>
                            </button>

                            <div class="relative z-10 flex items-center justify-end gap-[20px] px-[42px] pt-[23px] text-[8px] font-black uppercase tracking-[0] text-white/94">
                                @foreach(collect($visual['nav'])->take(4) as $navItem)
                                    <span>{{ \Illuminate\Support\Str::upper($navItem) }}</span>
                                @endforeach
                            </div>

                            <div class="absolute bottom-[26px] left-[22px] z-10 max-w-[315px] text-white">
                                <div class="text-[10px] font-black uppercase tracking-[0]">{{ \Illuminate\Support\Str::upper($visual['brand']) }}</div>
                                <h2 class="mt-[12px] max-w-[300px] text-[25px] font-black leading-[1.02] tracking-[0] text-white">{{ $visual['heading'] }}</h2>
                                <p class="mt-[12px] max-w-[350px] text-[12px] font-medium leading-[1.35] text-white/90">{{ $visual['description'] }}</p>
                                <span class="mt-[16px] inline-flex h-[29px] items-center rounded-full bg-white px-[18px] text-[10.5px] font-black text-slate-950 shadow-sm">Explore -></span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col px-[20px] pb-[16px] pt-[12px]">
                            <div class="flex items-center gap-[9px]">
                                @foreach($swatches as $swatch)
                                    <span class="h-[19px] w-[28px] rounded-[4px] border border-slate-200 shadow-sm" style="background: {{ $swatch }};"></span>
                                @endforeach
                            </div>

                            <div class="mt-[21px] grid grid-cols-[minmax(0,1fr)_auto] items-start gap-4">
                                <div class="min-w-0">
                                    <h3 class="truncate text-[17px] font-black leading-none tracking-[0] text-[#101a38]">{{ $theme->name }}</h3>
                                    <p class="mt-[9px] truncate text-[12px] font-bold leading-none text-[#77839c]">/{{ $theme->slug }}</p>
                                </div>

                                <div class="flex items-center gap-[28px]">
                                    <div class="flex items-center gap-[8px]">
                                        <svg class="h-[17px] w-[17px] text-[#8b99b3]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/></svg>
                                        <div class="leading-none">
                                            <div class="text-[12px] font-black text-[#b15d20] tabular-nums">{{ $visual['pages'] }}</div>
                                            <div class="mt-[4px] text-[10px] font-medium text-[#6c7890]">Pages</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-[8px]">
                                        <svg class="h-[17px] w-[17px] text-[#8b99b3]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                                        <div class="leading-none">
                                            <div class="text-[12px] font-black text-[#b15d20] tabular-nums">{{ $visual['sections'] }}</div>
                                            <div class="mt-[4px] text-[10px] font-medium text-[#6c7890]">Sections</div>
                                        </div>
                                    </div>
                                    <button type="button" class="grid h-[36px] w-[36px] place-items-center rounded-[8px] border border-[#dfe7f2] bg-white text-[#254366] transition hover:bg-slate-50" aria-label="More options">
                                        <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="19" cy="12" r="1.7"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-[29px] grid grid-cols-[minmax(0,1fr)_44px_44px] gap-[12px]">
                                @if($status === 'draft')
                                    <a href="{{ route('themes.customize') }}" class="inline-flex h-[40px] items-center justify-center gap-2 rounded-[7px] bg-emerald-50 text-[13px] font-black text-emerald-600 transition hover:bg-emerald-100">
                                        <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        Continue editing
                                    </a>
                                @elseif($isApplied)
                                    <button type="button" class="inline-flex h-[40px] cursor-default items-center justify-center gap-2 rounded-[7px] bg-emerald-50 text-[13px] font-black text-emerald-600">
                                        <span class="h-[7px] w-[7px] rounded-full bg-emerald-500"></span>
                                        Active
                                        <svg class="h-[14px] w-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                @else
                                    @can('apply', $theme)
                                        <form method="POST"
                                              action="{{ route('themes.apply', $theme) }}"
                                              data-theme-apply-form
                                              data-requires-reset="{{ $hasOverrides ? '1' : '0' }}"
                                              data-theme-name="{{ $theme->name }}">
                                            @csrf
                                            <input type="hidden" name="confirm_reset" value="0">
                                            <button type="submit" class="inline-flex h-[40px] w-full items-center justify-center rounded-[7px] bg-violet-600 px-4 text-[13px] font-black text-white shadow-[0_14px_24px_-18px_rgba(124,58,237,0.8)] transition hover:bg-violet-700">
                                                Apply theme
                                            </button>
                                        </form>
                                    @else
                                        <div class="inline-flex h-[40px] items-center justify-center rounded-[7px] bg-slate-100 text-[13px] font-black text-slate-400">View only</div>
                                    @endcan
                                @endif

                                <button type="button" class="grid h-[40px] w-[44px] place-items-center rounded-[7px] border border-[#dfe7f2] bg-[#f8fafc] text-[#5b48ff] transition hover:bg-violet-50" data-theme-preview-button data-theme-preview-id="{{ $theme->id }}" aria-label="Preview {{ $theme->name }}">
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>

                                <a href="{{ route('themes.customize') }}" class="grid h-[40px] w-[44px] place-items-center rounded-[7px] border border-[#dfe7f2] bg-[#f8fafc] text-[#6c7890] transition hover:bg-violet-50 hover:text-violet-600" aria-label="Customize {{ $theme->name }}">
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </a>
                            </div>
                        </div>

                        <script type="application/json" id="theme-preview-{{ $theme->id }}">@json($previewPayload)</script>
                    </article>
                @empty
                    <div class="rounded-[10px] border border-dashed border-slate-300 bg-white p-10 text-center text-sm font-semibold text-slate-400">
                        No themes found.
                    </div>
                @endforelse
            </div>

            <div class="hidden rounded-[10px] border border-dashed border-slate-300 bg-white p-10 text-center text-sm font-semibold text-slate-400" data-theme-no-results>
                No themes match your filters.
            </div>
        </section>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm" data-theme-preview-modal aria-hidden="true">
        <div class="w-full max-w-[860px] overflow-hidden rounded-[10px] bg-white shadow-2xl shadow-slate-950/30">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
                <div>
                    <div class="text-[11px] font-black uppercase tracking-[0] text-violet-600" data-preview-slug>Theme preview</div>
                    <h3 class="mt-1 text-xl font-black tracking-[0] text-slate-950" data-preview-title>Theme preview</h3>
                </div>
                <button type="button" class="grid h-9 w-9 place-items-center rounded-[8px] border border-slate-200 text-slate-500 transition hover:bg-slate-50 hover:text-slate-900" data-theme-preview-close aria-label="Close preview">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="grid max-h-[calc(90vh-72px)] overflow-y-auto bg-slate-50 lg:grid-cols-[minmax(0,1.15fr)_280px]">
                <div class="p-4">
                    <div class="overflow-hidden rounded-[10px] bg-slate-950 text-white" data-preview-shell>
                        <div class="relative h-[300px] overflow-hidden">
                            <img src="" alt="" class="absolute inset-0 h-full w-full object-cover" data-preview-image>
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/78 via-slate-950/30 to-transparent"></div>
                            <div class="absolute left-8 top-8 text-[10px] font-black uppercase tracking-[0]" data-preview-brand>Theme</div>
                            <div class="absolute bottom-8 left-8 max-w-[430px]">
                                <h2 class="text-[32px] font-black leading-[1.02] tracking-[0]" data-preview-heading>Theme heading</h2>
                                <p class="mt-3 text-sm font-medium leading-6 text-white/85" data-preview-description>Theme description</p>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="border-t border-slate-200 bg-white p-5 lg:border-l lg:border-t-0">
                    <div class="text-sm font-black text-slate-950">Theme tokens</div>
                    <div class="mt-4 grid grid-cols-2 gap-2" data-preview-swatches></div>
                    <div class="mt-5 rounded-[8px] border border-slate-200 bg-slate-50 p-3">
                        <div class="text-[10px] font-black uppercase tracking-[0] text-slate-400">Category</div>
                        <div class="mt-2 text-xs font-bold text-slate-700" data-preview-category>Travel</div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const root = document.querySelector('[data-themes-page]');
        if (!root) return;

        const grid = root.querySelector('[data-theme-grid]');
        const cards = Array.from(root.querySelectorAll('[data-theme-card]'));
        const noResults = root.querySelector('[data-theme-no-results]');
        const searchInput = root.querySelector('[data-theme-search]');
        const categorySelect = root.querySelector('[data-theme-category]');
        const sortSelect = root.querySelector('[data-theme-sort]');
        const filterButtons = Array.from(root.querySelectorAll('[data-theme-filter]'));
        const viewButtons = Array.from(root.querySelectorAll('[data-theme-view]'));
        const browseButton = root.querySelector('[data-scroll-to-themes]');
        let activeFilter = 'all';

        const normalize = value => String(value || '').toLowerCase().trim();

        const updateFilterButtons = activeButton => {
            filterButtons.forEach(button => {
                const active = button === activeButton;
                const count = button.querySelector('[data-filter-count]');

                button.classList.toggle('border-violet-300', active);
                button.classList.toggle('bg-violet-600', active);
                button.classList.toggle('text-white', active);
                button.classList.toggle('shadow-[0_8px_18px_-14px_rgba(124,58,237,0.8)]', active);
                button.classList.toggle('border-transparent', !active);
                button.classList.toggle('bg-[#f8fafc]', !active);
                button.classList.toggle('text-[#182646]', !active);

                count?.classList.toggle('bg-white/15', active);
                count?.classList.toggle('text-white', active);
                count?.classList.toggle('bg-[#edf2f8]', !active);
                count?.classList.toggle('text-[#7f8ca3]', !active);
            });
        };

        const matchesFilter = card => {
            if (activeFilter === 'all') return true;
            if (activeFilter === 'customized') return card.dataset.customized === '1';
            if (activeFilter === 'ready') return card.dataset.status === 'ready' || card.dataset.status === 'customized';
            return card.dataset.status === activeFilter;
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const category = categorySelect?.value || 'all';
            let visibleCount = 0;

            cards.forEach(card => {
                const visible = matchesFilter(card)
                    && (category === 'all' || card.dataset.category === category)
                    && (!query || normalize(card.dataset.search).includes(query));

                card.classList.toggle('hidden', !visible);
                if (visible) visibleCount++;
            });

            noResults?.classList.toggle('hidden', visibleCount !== 0 || cards.length === 0);
        };

        const sortCards = () => {
            const statusWeight = { active: 0, ready: 1, customized: 2, draft: 3 };
            const sort = sortSelect?.value || 'recommended';
            const sorted = [...cards].sort((a, b) => {
                if (sort === 'name') return normalize(a.dataset.name).localeCompare(normalize(b.dataset.name));
                if (sort === 'active') return Number(b.dataset.applied || 0) - Number(a.dataset.applied || 0)
                    || (statusWeight[a.dataset.status] ?? 9) - (statusWeight[b.dataset.status] ?? 9)
                    || normalize(a.dataset.name).localeCompare(normalize(b.dataset.name));
                if (sort === 'draft') return Number(b.dataset.status === 'draft') - Number(a.dataset.status === 'draft')
                    || normalize(a.dataset.name).localeCompare(normalize(b.dataset.name));
                return Number(b.dataset.applied || 0) - Number(a.dataset.applied || 0)
                    || (statusWeight[a.dataset.status] ?? 9) - (statusWeight[b.dataset.status] ?? 9)
                    || normalize(a.dataset.name).localeCompare(normalize(b.dataset.name));
            });

            sorted.forEach(card => grid?.appendChild(card));
        };

        const setView = view => {
            const list = view === 'list';
            grid?.classList.toggle('theme-card-grid', !list);
            grid?.classList.toggle('grid', list);
            grid?.classList.toggle('grid-cols-1', list);
            grid?.classList.toggle('gap-[18px]', list);

            cards.forEach(card => {
                card.classList.toggle('lg:grid', list);
                card.classList.toggle('lg:grid-cols-[420px_minmax(0,1fr)]', list);
            });

            viewButtons.forEach(button => {
                const active = button.dataset.themeView === view;
                button.classList.toggle('bg-violet-50', active);
                button.classList.toggle('text-violet-600', active);
                button.classList.toggle('text-[#8796ad]', !active);
            });
        };

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                activeFilter = button.dataset.themeFilter || 'all';
                updateFilterButtons(button);
                applyFilters();
            });
        });

        searchInput?.addEventListener('input', applyFilters);
        categorySelect?.addEventListener('change', applyFilters);
        sortSelect?.addEventListener('change', () => {
            sortCards();
            applyFilters();
        });
        viewButtons.forEach(button => button.addEventListener('click', () => setView(button.dataset.themeView)));
        browseButton?.addEventListener('click', () => document.getElementById('theme-presets')?.scrollIntoView({ behavior: 'smooth', block: 'start' }));

        root.querySelectorAll('[data-theme-apply-form]').forEach(form => {
            form.addEventListener('submit', event => {
                if (form.dataset.requiresReset !== '1') return;
                const accepted = window.confirm(`Changing to ${form.dataset.themeName} will reset current theme customizations. Continue?`);
                if (!accepted) {
                    event.preventDefault();
                    return;
                }

                const input = form.querySelector('[name="confirm_reset"]');
                if (input) input.value = '1';
            });
        });

        const modal = root.querySelector('[data-theme-preview-modal]');
        const closePreview = () => {
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
            modal?.setAttribute('aria-hidden', 'true');
        };

        root.querySelectorAll('[data-theme-preview-button]').forEach(button => {
            button.addEventListener('click', () => {
                const payloadElement = document.getElementById(`theme-preview-${button.dataset.themePreviewId}`);
                if (!payloadElement || !modal) return;
                const payload = JSON.parse(payloadElement.textContent || '{}');

                modal.querySelector('[data-preview-title]').textContent = payload.name || 'Theme preview';
                modal.querySelector('[data-preview-slug]').textContent = `/${payload.slug || 'theme'}`;
                modal.querySelector('[data-preview-brand]').textContent = payload.brand || payload.name || 'Theme';
                modal.querySelector('[data-preview-heading]').textContent = payload.heading || payload.name || 'Theme preview';
                modal.querySelector('[data-preview-description]').textContent = payload.description || '';
                modal.querySelector('[data-preview-category]').textContent = payload.category || 'General';

                const image = modal.querySelector('[data-preview-image]');
                if (image) {
                    image.src = payload.image || '';
                    image.alt = `${payload.name || 'Theme'} preview`;
                }

                const shell = modal.querySelector('[data-preview-shell]');
                if (shell) shell.style.background = payload.secondary || '#0f172a';

                const swatches = modal.querySelector('[data-preview-swatches]');
                if (swatches) {
                    swatches.textContent = '';
                    ['primary', 'secondary', 'accent', 'surface', 'background', 'text'].forEach(token => {
                        const item = document.createElement('div');
                        const color = document.createElement('span');
                        const label = document.createElement('span');

                        item.className = 'rounded-[8px] border border-slate-200 bg-slate-50 p-2';
                        color.className = 'block h-8 rounded-[6px] ring-1 ring-slate-200';
                        color.style.background = payload[token] || '#e2e8f0';
                        label.className = 'mt-2 block text-[9px] font-black uppercase tracking-[0] text-slate-400';
                        label.textContent = token;
                        item.appendChild(color);
                        item.appendChild(label);
                        swatches.appendChild(item);
                    });
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
            });
        });

        root.querySelector('[data-theme-preview-close]')?.addEventListener('click', closePreview);
        modal?.addEventListener('click', event => {
            if (event.target === modal) closePreview();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closePreview();
        });

        sortCards();
        applyFilters();
    })();
</script>
@endsection
