@props([
    'searchPlaceholder' => null,
])

@once
    <style>
        .admin-topbar-left > button,
        .admin-topbar-left > a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            height: 40px;
            padding: 0 1.15rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1;
            transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease, box-shadow 160ms ease;
            white-space: nowrap;
        }

        .admin-topbar-left > button:first-child,
        .admin-topbar-left > a:first-child {
            border: 1px solid #6c5ce7;
            background: #6c5ce7;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(108, 92, 231, 0.22);
        }

        .admin-topbar-left > button:first-child:hover,
        .admin-topbar-left > a:first-child:hover {
            background: #5f4fd9;
            border-color: #5f4fd9;
        }

        .admin-topbar-left > button:not(:first-child),
        .admin-topbar-left > a:not(:first-child) {
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #252535;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .admin-topbar-left > button:not(:first-child):hover,
        .admin-topbar-left > a:not(:first-child):hover {
            background: #f8fafc;
            border-color: #bfc4cf;
        }
    </style>
@endonce

<header class="relative z-30 flex h-[62px] items-center justify-between gap-4 border-b border-gray-100 bg-white px-4 md:px-5">
    <div class="flex min-w-0 flex-1 items-center gap-3">
        <button type="button"
                onclick="toggleSidebar()"
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:bg-gray-50 md:hidden"
                aria-label="Open menu">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
        </button>

        <div class="admin-topbar-left flex min-w-0 items-center gap-3">
            <a href="{{ url('/') }}" target="_blank" rel="noopener">
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="11" cy="12" r="7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h14M11 5c2 2 3 4.3 3 7s-1 5-3 7M11 5c-2 2-3 4.3-3 7s1 5 3 7M16 5h3v3M19 5l-5 5"/>
                </svg>
                Visit site
            </a>

        </div>
    </div>

    @if(filled($searchPlaceholder))
        <form method="GET" action="{{ url()->current() }}" role="search" class="hidden w-[260px] shrink-0 md:block lg:w-[360px] xl:w-[420px]">
            <div class="group relative">
                <svg class="absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#6c5ce7] transition group-focus-within:text-[#5f4fd9]"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M21 21l-5.2-5.2m1.2-5.3a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                </svg>

                <input type="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ $searchPlaceholder }}"
                       aria-label="{{ $searchPlaceholder }}"
                       class="h-11 w-full rounded-full border border-gray-100 bg-white pl-11 pr-4 text-sm text-gray-700 shadow-[0_4px_18px_rgba(17,24,39,0.07)] outline-none transition placeholder:text-gray-400 focus:border-[#d9d4ff] focus:ring-4 focus:ring-[#6c5ce7]/10">
            </div>
        </form>
    @endif

    <div class="flex flex-1 items-center justify-end gap-4 lg:gap-5">
        <div class="hidden items-center gap-4 sm:flex lg:gap-5">
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full text-[#5b4fd7] transition hover:bg-[#6c5ce7]/10" aria-label="Messages">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c5 0 9-3.6 9-8s-4-8-9-8-9 3.6-9 8c0 1.7.6 3.2 1.6 4.5L4 21l3.4-1.4A10.2 10.2 0 0012 21z"/>
                </svg>
            </button>

            <button type="button" class="relative flex h-9 w-9 items-center justify-center rounded-full text-[#5b4fd7] transition hover:bg-[#6c5ce7]/10" aria-label="Notifications">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9a6 6 0 10-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path stroke-linecap="round" d="M10 21h4"/>
                </svg>
                <span class="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-[#6c5ce7]"></span>
            </button>
        </div>
    </div>
</header>
