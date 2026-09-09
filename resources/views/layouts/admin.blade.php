<!DOCTYPE html>
<html class="h-full overflow-hidden">
<head>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-100 h-full overflow-hidden">

@php
    $adminFullPage = View::hasSection('admin_full_page');
@endphp

<div class="flex h-full min-h-0 flex-col md:flex-row">

    {{-- SIDEBAR --}}
    @unless($adminFullPage)
        <x-layout.sidebar />
    @endunless

    <div class="flex-1 flex min-h-0 flex-col min-w-0">

        {{-- TOPBAR --}}
        @unless($adminFullPage)
            @if(View::hasSection('topbar'))
                @yield('topbar')
            @else
                <x-layout.topbar />
            @endif
        @endunless

        {{-- CONTENT --}}
        <main class="min-h-0 flex-1 overflow-y-auto {{ $adminFullPage ? 'no-scrollbar p-0' : 'p-6' }}">
            @yield('content')
        </main>

    </div>
    @unless($adminFullPage)
        <div id="sidebar-overlay"
         class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"
         onclick="toggleSidebar()">
        </div>
    @endunless
</div>

</body>
</html>
