@extends('layouts.admin')

@section('content')
@php
    $userName = auth()->user()->name ?? 'Admin1';
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

    $kpis = [
        [
            'label' => 'Total Pages',  'value' => $summaryMetrics[0]['value'] ?? 4,
            'icon' => 'document-text', 'bg' => 'bg-violet-100',  'text' => 'text-violet-600',
            'border' => 'border-violet-200',  'pillBg' => 'bg-violet-50',  'pillText' => 'text-violet-600',
            'stroke' => '#8b5cf6', 'fill' => '#ede9fe',
            'path' => 'M2,28 C12,26 22,22 32,20 C42,18 52,22 62,18 C72,14 82,10 92,7',
        ],
        [
            'label' => 'Published',    'value' => $summaryMetrics[1]['value'] ?? 1,
            'icon' => 'check-circle',  'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600',
            'border' => 'border-emerald-200', 'pillBg' => 'bg-emerald-50', 'pillText' => 'text-emerald-600',
            'stroke' => '#10b981', 'fill' => '#d1fae5',
            'path' => 'M2,30 C14,28 24,24 34,22 C44,20 54,18 64,14 C74,10 84,8 92,6',
        ],
        [
            'label' => 'Drafts',       'value' => $summaryMetrics[2]['value'] ?? 3,
            'icon' => 'pencil-square', 'bg' => 'bg-amber-100',   'text' => 'text-amber-600',
            'border' => 'border-amber-200',   'pillBg' => 'bg-emerald-50', 'pillText' => 'text-emerald-600',
            'stroke' => '#f59e0b', 'fill' => '#fef3c7',
            'path' => 'M2,26 C12,18 20,30 32,22 C42,16 52,28 62,14 C72,6 82,18 92,10',
        ],
        [
            'label' => 'Active Users', 'value' => $summaryMetrics[3]['value'] ?? 2,
            'icon' => 'user-group',    'bg' => 'bg-blue-100',    'text' => 'text-blue-600',
            'border' => 'border-blue-200',    'pillBg' => 'bg-emerald-50', 'pillText' => 'text-emerald-600',
            'stroke' => '#3b82f6', 'fill' => '#dbeafe',
            'path' => 'M2,28 C12,24 22,18 32,20 C42,22 52,10 62,8 C72,6 82,14 92,10',
        ],
        [
            'label' => 'Media Assets', 'value' => 7,
            'icon' => 'photo',         'bg' => 'bg-pink-100',    'text' => 'text-pink-600',
            'border' => 'border-pink-200',    'pillBg' => 'bg-emerald-50', 'pillText' => 'text-emerald-600',
            'stroke' => '#ec4899', 'fill' => '#fce7f3',
            'path' => 'M2,26 C12,28 22,22 32,24 C42,26 52,18 62,20 C72,22 82,12 92,14',
        ],
    ];
@endphp

<div class="relative mx-auto w-full max-w-[1480px] pb-8 text-slate-900 antialiased">

    {{-- ============ TOP BAR ============ --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        {{-- Search input removed, invisible spacer kept to preserve the exact top bar layout --}}
        <div aria-hidden="true" class="h-10 w-full max-w-md"></div>

        <div class="flex items-center gap-2">
            <button type="button" class="inline-flex h-10 items-center gap-2 rounded-full border border-slate-200
bg-white px-4 text-[12.5px] font-semibold text-slate-900 transition hover:border-slate-300">
                <x-dynamic-component component="heroicon-o-calendar-days" class="h-[15px] w-[15px] text-slate-500" />
                <span>{{ $dashboardContext['dateRange'] ?? 'May 17 – Jun 15' }}</span>
                <x-dynamic-component component="heroicon-o-chevron-down" class="h-3 w-3 text-slate-400" />
            </button>
            <a href="{{ route('pages.create') ?? '#' }}" class="inline-flex h-10 items-center gap-1.5 rounded-full
bg-slate-900 px-5 text-[13px] font-semibold text-white shadow-[0_8px_24px_-8px_rgba(15,23,42,0.5)] transition
hover:bg-slate-800">
                <x-dynamic-component component="heroicon-o-plus" class="h-[15px] w-[15px]" />
                Create page
            </a>
        </div>
    </div>

    @if(!empty($showOnboardingCta) || !empty($showDemoContentCta))
        <section class="mb-5 grid gap-3 {{ !empty($showOnboardingCta) && !empty($showDemoContentCta) ? 'lg:grid-cols-2' : '' }}">
            @if(!empty($showOnboardingCta))
                <article class="flex flex-col gap-4 rounded-2xl border border-violet-200 bg-violet-50/80 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-violet-600">Setup</div>
                        <h2 class="mt-1 text-base font-bold text-slate-950">Complete the agency workspace</h2>
                        <p class="mt-1 text-sm text-slate-600">Finish the guided setup to configure your profile, template and theme.</p>
                    </div>
                    <a href="{{ route('onboarding.index') }}"
                       class="inline-flex shrink-0 items-center justify-center rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700">
                        Continue setup
                    </a>
                </article>
            @endif

            @if(!empty($showDemoContentCta))
                <article class="flex flex-col gap-4 rounded-2xl border border-sky-200 bg-sky-50/80 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-sky-600">Travel content</div>
                        <h2 class="mt-1 text-base font-bold text-slate-950">Add demonstration content</h2>
                        <p class="mt-1 text-sm text-slate-600">Populate destinations, offers and media with safe reusable demo data.</p>
                    </div>
                    <a href="{{ route('demo-content.index') }}"
                       class="inline-flex shrink-0 items-center justify-center rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                        Review demo content
                    </a>
                </article>
            @endif
        </section>
    @endif

    {{-- ============ HERO ============ --}}
    <section class="relative mb-5 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <svg class="absolute inset-x-0 top-0 h-full w-full" viewBox="0 0 1400 180" preserveAspectRatio="none"
fill="none">
                <path d="M0,120 C200,80 500,160 800,100 C1100,40 1300,120 1400,90 L1400,180 L0,180 Z" fill="#f5f3ff"
opacity="0.6"/>
                <path d="M0,150 C300,110 600,180 900,130 C1200,80 1300,150 1400,130 L1400,180 L0,180 Z" fill="#ede9fe"
opacity="0.4"/>
            </svg>
        </div>

        <div class="relative flex items-center justify-between gap-6 px-2 py-3">
            {{-- Text removed, spacer kept to preserve the exact hero height and layout --}}
            <div aria-hidden="true" class="min-h-[68px] w-full max-w-[520px]"></div>

            {{-- 3D decorative illustration --}}
            <div class="relative hidden h-[140px] w-[260px] shrink-0 lg:block">
                <div class="absolute right-14 top-1 h-[80px] w-[68px] rotate-[-12deg] rounded-2xl bg-gradient-to-br
from-violet-300/70 to-purple-400/50 shadow-[0_15px_30px_-12px_rgba(139,92,246,0.5)] ring-1 ring-white/40"></div>
                <div class="absolute right-2 top-4 h-[90px] w-[64px] rotate-[8deg] rounded-2xl bg-gradient-to-br
from-violet-500/80 to-indigo-600/70 shadow-[0_15px_30px_-12px_rgba(99,102,241,0.5)] ring-1 ring-white/40"></div>
                <div class="absolute right-28 top-14 h-[60px] w-[54px] rotate-[15deg] rounded-2xl bg-gradient-to-br
from-pink-300/60 to-violet-400/50 shadow-[0_15px_30px_-12px_rgba(236,72,153,0.4)] ring-1 ring-white/40"></div>
                <div class="absolute bottom-1 left-10 h-9 w-9 rounded-full bg-gradient-to-br from-violet-400
to-purple-700 shadow-[0_8px_20px_-4px_rgba(139,92,246,0.6),inset_0_3px_6px_rgba(255,255,255,0.3)]"></div>
                <div class="absolute bottom-0 right-16 h-[45px] w-[22px] rounded-full bg-gradient-to-b from-violet-300
to-violet-500 shadow-md"></div>
                <div class="absolute bottom-0 right-3 h-[60px] w-[22px] rounded-full bg-gradient-to-b from-blue-300
to-indigo-500 shadow-md"></div>
                <span class="absolute right-40 top-3 text-violet-400">✦</span>
                <span class="absolute right-0 bottom-10 text-sm text-pink-300">✧</span>
            </div>
        </div>
    </section>

    {{-- ============ KPI CARDS — COLORED BORDER + SPARKLINE ============ --}}
    <section class="mb-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach($kpis as $kpi)
            <article class="rounded-2xl border-2 {{ $kpi['border'] }} bg-white px-4 pt-4 pb-3
shadow-[0_1px_2px_rgba(15,23,42,0.04)] transition hover:shadow-[0_10px_24px_-10px_rgba(15,23,42,0.18)]">
                {{-- Top row: icon + (label/value) + sparkline --}}
                <div class="flex items-start gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full {{ $kpi['bg'] }} {{
$kpi['text'] }}">
                        <x-dynamic-component :component="'heroicon-o-' . $kpi['icon']" class="h-[18px] w-[18px]" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="text-[12.5px] font-semibold text-slate-500">{{ $kpi['label'] }}</div>
                        <div class="mt-0.5 text-[26px] font-extrabold leading-none tracking-[-0.03em] text-slate-900
tabular-nums">{{ $kpi['value'] }}</div>
                    </div>
                    {{-- Mini sparkline --}}
                    <svg viewBox="0 0 94 36" class="h-9 w-[70px] shrink-0" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="spark-{{ $loop->index }}" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="{{ $kpi['stroke'] }}" stop-opacity="0.28"/>
                                <stop offset="100%" stop-color="{{ $kpi['stroke'] }}" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="{{ $kpi['path'] }} L92,36 L2,36 Z" fill="url(#spark-{{ $loop->index }})"/>
                        <path d="{{ $kpi['path'] }}" fill="none" stroke="{{ $kpi['stroke'] }}" stroke-width="1.8"
stroke-linecap="round" stroke-linejoin="round"/>
                        @php
                            $segments = explode(' ', $kpi['path']);
                            $last = end($segments);
                            $coords = explode(',', $last);
                            $cx = $coords[0] ?? 92;
                            $cy = $coords[1] ?? 7;
                        @endphp
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="2.2" fill="{{ $kpi['stroke'] }}"/>
                    </svg>
                </div>

                {{-- Bottom row: pill + comparison label --}}
                <div class="mt-3 flex items-center gap-2">
                    <span class="inline-flex items-center gap-0.5 rounded-md {{ $kpi['pillBg'] }} px-1.5 py-0.5
text-[11px] font-bold {{ $kpi['pillText'] }}">
                        <x-dynamic-component component="heroicon-o-arrow-up" class="h-2.5 w-2.5" />
                        100%
                    </span>
                    <span class="truncate text-[11px] font-medium text-slate-500">vs Apr 17 – May 16</span>
                </div>
            </article>
        @endforeach
    </section>

    {{-- ============ MIDDLE ROW: CHART + DONUT + BREAKDOWN ============ --}}
    <section class="mb-5 grid gap-3 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)]">

        {{-- Content Overview --}}
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-[15px] font-bold tracking-[-0.015em] text-slate-900">Content Overview</h2>
                    <p class="mt-0.5 text-[11.5px] font-medium text-slate-500">Pages created over time</p>
                </div>
                <button type="button" class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-200
bg-slate-50 px-2.5 text-[11px] font-semibold text-slate-700">
                    Last 30 days
                    <x-dynamic-component component="heroicon-o-chevron-down" class="h-3 w-3 text-slate-400" />
                </button>
            </div>

            <div class="relative mt-4">
                <svg viewBox="0 0 700 220" class="w-full" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.30"/>
                            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <g font-size="10" fill="#94a3b8" font-family="Inter">
                        <text x="0" y="12">8</text>
                        <text x="0" y="58">6</text>
                        <text x="0" y="104">4</text>
                        <text x="0" y="150">2</text>
                        <text x="0" y="196">0</text>
                    </g>
                    <g stroke="#f1f5f9" stroke-width="1">
                        <line x1="20" y1="8" x2="700" y2="8"/>
                        <line x1="20" y1="54" x2="700" y2="54"/>
                        <line x1="20" y1="100" x2="700" y2="100"/>
                        <line x1="20" y1="146" x2="700" y2="146"/>
                        <line x1="20" y1="192" x2="700" y2="192"/>
                    </g>
                    <path d="M30,165 C70,155 100,128 130,118 C160,108 180,165 210,155 C240,145 260,80 290,72 C320,65
340,128 380,118 C420,108 440,55 480,50 C520,45 540,100 580,90 C620,80 650,35 685,28 L685,192 L30,192 Z"
fill="url(#areaGrad)"/>
                    <path d="M30,165 C70,155 100,128 130,118 C160,108 180,165 210,155 C240,145 260,80 290,72 C320,65
340,128 380,118 C420,108 440,55 480,50 C520,45 540,100 580,90 C620,80 650,35 685,28" fill="none" stroke="#8b5cf6"
stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <g fill="white" stroke="#8b5cf6" stroke-width="2">
                        <circle cx="30" cy="165" r="3.5"/>
                        <circle cx="130" cy="118" r="3.5"/>
                        <circle cx="210" cy="155" r="3.5"/>
                        <circle cx="290" cy="72" r="3.5"/>
                        <circle cx="380" cy="118" r="3.5"/>
                        <circle cx="480" cy="50" r="3.5"/>
                        <circle cx="580" cy="90" r="3.5"/>
                        <circle cx="685" cy="28" r="3.5"/>
                    </g>
                    <line x1="380" y1="18" x2="380" y2="118" stroke="#cbd5e1" stroke-dasharray="3 3"
stroke-width="1"/>
                    <circle cx="380" cy="118" r="5" fill="#8b5cf6" stroke="white" stroke-width="2.5"/>
                    <g font-size="10" fill="#94a3b8" font-family="Inter" text-anchor="middle">
                        <text x="30" y="212">May 17</text>
                        <text x="130" y="212">May 22</text>
                        <text x="210" y="212">May 27</text>
                        <text x="290" y="212">Jun 1</text>
                        <text x="380" y="212">Jun 6</text>
                        <text x="480" y="212">Jun 11</text>
                        <text x="580" y="212">Jun 15</text>
                    </g>
                </svg>
            </div>
        </article>

        {{-- Publishing Status --}}
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div>
                <h2 class="text-[15px] font-bold tracking-[-0.015em] text-slate-900">Publishing Status</h2>
                <p class="mt-0.5 text-[11.5px] font-medium text-slate-500">Overview of content state</p>
            </div>

            @php
                $statusData = [
                    ['name' => 'Published', 'value' => 1, 'color' => '#10b981'],
                    ['name' => 'Draft',     'value' => 3, 'color' => '#f59e0b'],
                    ['name' => 'Archived',  'value' => 0, 'color' => '#cbd5e1'],
                ];
                $statusTotal = array_sum(array_column($statusData, 'value'));
                $offset = 0;
                $r = 40;
                $circ = 2 * pi() * $r;
            @endphp

            <div class="mt-4 flex items-center gap-4">
                <div class="relative h-[110px] w-[110px] shrink-0">
                    <svg class="h-full w-full -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="#f1f5f9" stroke-width="14"/>
                        @foreach($statusData as $segment)
                            @if($segment['value'] > 0)
                                @php
                                    $pct = $statusTotal > 0 ? ($segment['value'] / $statusTotal) : 0;
                                    $dashLen = $pct * $circ;
                                @endphp
                                <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="{{ $segment['color'] }}"
stroke-width="14" stroke-dasharray="{{ $dashLen }} {{ $circ }}" stroke-dashoffset="{{ -$offset }}" />
                                @php $offset += $dashLen; @endphp
                            @endif
                        @endforeach
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[26px] font-extrabold leading-none tracking-[-0.025em] text-slate-900
tabular-nums">{{ $statusTotal }}</span>
                        <span class="mt-1 text-[10.5px] font-medium text-slate-500">Total</span>
                    </div>
                </div>

                <div class="flex-1 space-y-2.5">
                    @foreach($statusData as $segment)
                        @php $pct = $statusTotal > 0 ? round(($segment['value'] / $statusTotal) * 100) : 0; @endphp
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full" style="background:{{ $segment['color']
}}"></span>
                                <span class="text-[11.5px] font-medium text-slate-600">{{ $segment['name'] }}</span>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-[12.5px] font-bold text-slate-900 tabular-nums">{{ $segment['value']
}}</span>
                                <span class="text-[10.5px] font-medium text-slate-400 tabular-nums">({{ $pct
}}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2.5">
                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-white text-slate-500">
                    <x-dynamic-component component="heroicon-o-information-circle" class="h-[16px] w-[16px]" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="text-[11.5px] font-semibold text-slate-900">1 page published this period</div>
                    <div class="text-[10.5px] font-medium text-slate-500">Keep up the momentum!</div>
                </div>
                <span class="text-emerald-500">
                    <x-dynamic-component component="heroicon-o-arrow-trending-up" class="h-4 w-4" />
                </span>
            </div>
        </article>

        {{-- Content Breakdown --}}
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div>
                <h2 class="text-[15px] font-bold tracking-[-0.015em] text-slate-900">Content Breakdown</h2>
                <p class="mt-0.5 text-[11.5px] font-medium text-slate-500">By content type</p>
            </div>

            @php
                $breakdown = [
                    ['name' => 'Pages',        'icon' => 'document-text', 'value' => 4, 'max' => 10, 'color' =>
'bg-violet-500',  'soft' => 'bg-violet-100',  'text' => 'text-violet-600'],
                    ['name' => 'Destinations', 'icon' => 'check-circle',  'value' => 7, 'max' => 10, 'color' =>
'bg-emerald-500', 'soft' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
                    ['name' => 'Offers',       'icon' => 'tag',           'value' => 7, 'max' => 10, 'color' =>
'bg-sky-500',     'soft' => 'bg-sky-100',     'text' => 'text-sky-600'],
                    ['name' => 'Media',        'icon' => 'photo',         'value' => 4, 'max' => 10, 'color' =>
'bg-pink-500',    'soft' => 'bg-pink-100',    'text' => 'text-pink-600'],
                ];
            @endphp

            <div class="mt-4 space-y-3">
                @foreach($breakdown as $row)
                    @php $pct = $row['max'] > 0 ? ($row['value'] / $row['max']) * 100 : 0; @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="grid h-7 w-7 place-items-center rounded-lg {{ $row['soft'] }} {{
$row['text'] }}">
                                    <x-dynamic-component :component="'heroicon-o-' . $row['icon']" class="h-[13px]
w-[13px]" />
                                </span>
                                <span class="text-[12px] font-semibold text-slate-700">{{ $row['name'] }}</span>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-[12.5px] font-extrabold text-slate-900 tabular-nums">{{
$row['value'] }}</span>
                                <span class="text-[11px] font-medium text-slate-400">/ {{ $row['max'] }}</span>
                            </div>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full {{ $row['color'] }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <a href="#" class="mt-4 inline-flex items-center gap-1 text-[11.5px] font-semibold text-violet-600
hover:text-violet-700">
                View full analytics
                <x-dynamic-component component="heroicon-o-arrow-right" class="h-3 w-3" />
            </a>
        </article>
    </section>

    {{-- ============ BOTTOM ROW: RECENT PAGES + ACTIVITY + QUICK ACTIONS ============ --}}
    <section class="grid gap-3 xl:grid-cols-3">

        {{-- Recent Pages --}}
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div class="flex items-center justify-between">
                <h2 class="text-[14.5px] font-bold tracking-[-0.01em] text-slate-900">Recent Pages</h2>
                <a href="{{ route('pages.index') }}" class="inline-flex items-center gap-1 text-[11.5px] font-semibold
text-violet-600 hover:text-violet-700">
                    View all pages
                    <x-dynamic-component component="heroicon-o-arrow-right" class="h-3 w-3" />
                </a>
            </div>

            @php
                $recentPages = [
                    ['name' => 'Homepage Refresh',  'updated' => '2h ago',  'status' => 'Published', 'statusColor' =>
'text-emerald-600', 'dot' => 'bg-emerald-500', 'author' => 'Admin1', 'thumb' => 'from-violet-400 via-purple-500
to-purple-700'],
                    ['name' => 'Summer Campaign',   'updated' => '5h ago',  'status' => 'Draft',     'statusColor' =>
'text-amber-600',   'dot' => 'bg-amber-500',   'author' => 'Admin1', 'thumb' => 'from-amber-300 via-orange-400
to-orange-600'],
                    ['name' => 'About Us Redesign', 'updated' => '1d ago',  'status' => 'Draft',     'statusColor' =>
'text-amber-600',   'dot' => 'bg-amber-500',   'author' => 'Admin1', 'thumb' => 'from-emerald-300 via-teal-400
to-teal-600'],
                ];
            @endphp

            <div class="mt-3 space-y-1">
                @foreach($recentPages as $page)
                    <div class="group flex items-center gap-3 rounded-xl p-2 transition hover:bg-slate-50">
                        {{-- RECTANGULAR THUMBNAIL like reference (16:10 ratio) --}}
                        <div class="relative h-[42px] w-[68px] shrink-0 overflow-hidden rounded-lg bg-gradient-to-br
{{ $page['thumb'] }} shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-tr from-black/0 to-white/20"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[12.5px] font-bold text-slate-900">{{ $page['name'] }}</div>
                            <div class="mt-0.5 flex items-center gap-1 text-[10.5px] font-medium text-slate-400">
                                <x-dynamic-component component="heroicon-o-clock" class="h-2.5 w-2.5" />
                                Updated {{ $page['updated'] }}
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <div class="inline-flex items-center gap-1 text-[11px] font-semibold {{
$page['statusColor'] }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $page['dot'] }}"></span>
                                {{ $page['status'] }}
                            </div>
                            <div class="mt-0.5 text-[10px] font-medium text-slate-400">by {{ $page['author'] }}</div>
                        </div>
                        <button type="button" class="grid h-7 w-6 place-items-center rounded text-slate-300 transition
hover:text-slate-700">
                            <x-dynamic-component component="heroicon-o-ellipsis-vertical" class="h-3.5 w-3.5" />
                        </button>
                    </div>
                @endforeach
            </div>
        </article>

        {{-- Latest Activity --}}
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div class="flex items-center justify-between">
                <h2 class="text-[14.5px] font-bold tracking-[-0.01em] text-slate-900">Latest Activity</h2>
                <a href="#" class="inline-flex items-center gap-1 text-[11.5px] font-semibold text-violet-600
hover:text-violet-700">
                    View all activity
                    <x-dynamic-component component="heroicon-o-arrow-right" class="h-3 w-3" />
                </a>
            </div>

            @php
                $latestActivity = [
                    ['icon' => 'check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'title' => 'You',
'action' => 'published', 'target' => '"Homepage Refresh"', 'time' => '2 hours ago'],
                    ['icon' => 'pencil-square', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'title' => 'You',
'action' => 'updated', 'target' => '"Summer Campaign"', 'time' => '5 hours ago'],
                    ['icon' => 'user-plus', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'title' => 'John Doe',
'action' => 'invited new user', 'target' => 'Sarah', 'time' => '1 day ago'],
                ];
            @endphp

            <div class="mt-3 space-y-0.5">
                @foreach($latestActivity as $act)
                    <div class="flex items-start gap-2.5 rounded-xl p-2 transition hover:bg-slate-50">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full {{ $act['bg'] }} {{
$act['text'] }}">
                            <x-dynamic-component :component="'heroicon-o-' . $act['icon']" class="h-3.5 w-3.5" />
                        </span>
                        <div class="min-w-0 flex-1 pt-0.5">
                            <div class="text-[12px] leading-snug text-slate-700">
                                <b class="font-bold text-slate-900">{{ $act['title'] }}</b>
                                {{ $act['action'] }}
                                <b class="font-bold text-slate-900">{{ $act['target'] }}</b>
                            </div>
                            <div class="mt-0.5 text-[10.5px] font-medium text-slate-400">{{ $act['time'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        {{-- Quick Actions --}}
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <h2 class="text-[14.5px] font-bold tracking-[-0.01em] text-slate-900">Quick Actions</h2>

            @php
                $quickGrid = [
                    ['icon' => 'document-plus', 'label' => 'Create New Page', 'sub' => 'Start building', 'bg' =>
'bg-violet-50',  'text' => 'text-violet-600'],
                    ['icon' => 'arrow-up-tray', 'label' => 'Upload Media',    'sub' => 'Add files',      'bg' =>
'bg-blue-50',    'text' => 'text-blue-600'],
                    ['icon' => 'user-plus',     'label' => 'Invite User',     'sub' => 'Add to team',    'bg' =>
'bg-emerald-50', 'text' => 'text-emerald-600'],
                    ['icon' => 'chart-bar',     'label' => 'View Analytics',  'sub' => 'Deep insights',  'bg' =>
'bg-pink-50',    'text' => 'text-pink-600'],
                ];
            @endphp

            <div class="mt-3 grid grid-cols-2 gap-2.5">
                @foreach($quickGrid as $qa)
                    <a href="#" class="group flex items-center gap-2.5 rounded-xl {{ $qa['bg'] }} p-3 transition
hover:scale-[1.02]">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white {{ $qa['text'] }}
shadow-sm">
                            <x-dynamic-component :component="'heroicon-o-' . $qa['icon']" class="h-[15px] w-[15px]" />
                        </span>
                        <div class="min-w-0">
                            <div class="truncate text-[11.5px] font-bold leading-tight text-slate-900">{{ $qa['label']
}}</div>
                            <div class="mt-0.5 truncate text-[10px] font-medium text-slate-500">{{ $qa['sub'] }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </article>
    </section>

</div>
@endsection
