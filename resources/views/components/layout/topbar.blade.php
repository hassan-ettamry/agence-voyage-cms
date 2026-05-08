@props([
    'searchPlaceholder' => null,
])

<header class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-6 gap-4">

    {{-- LEFT --}}
    <div class="flex items-center gap-3">

        {{-- MOBILE SIDEBAR TOGGLE --}}
        <button onclick="toggleSidebar()"
            class="md:hidden w-8 h-8 flex items-center justify-center border border-gray-200 rounded-lg">
            ☰
        </button>

        {{ $left ?? '' }}

    </div>

    {{-- SEARCH --}}
    @if($searchPlaceholder)
        <form method="GET" action="{{ url()->current() }}" class="hidden md:block flex-1 max-w-md">
            <div class="relative group">

                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-indigo-500 transition"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full bg-gray-100 hover:bg-gray-200 focus:bg-white
                        border border-transparent focus:border-indigo-500
                        focus:ring-2 focus:ring-indigo-100
                        pl-9 pr-4 py-2 rounded-lg text-sm">
            </div>
        </form>
    @endif

    {{-- RIGHT --}}
    <div class="flex items-center gap-3">

        {{-- NOTIFICATIONS --}}
        <button class="relative w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/>
            </svg>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
        </button>

        {{-- MESSAGES --}}
        <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </button>

        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- MAIN ACTION --}}
        {{ $right ?? '' }}

    </div>

</header>