<!DOCTYPE html>
<html class="h-full">
<head>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-100 h-full">

<div class="flex h-full flex-col md:flex-row">

    {{-- SIDEBAR --}}
    <x-layout.sidebar />

    <div class="flex-1 flex flex-col min-w-0">

        {{-- TOPBAR --}}
        @if(View::hasSection('topbar'))
            @yield('topbar')
        @else
            <x-layout.topbar />
        @endif

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>
    <div id="sidebar-overlay"
     class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"
     onclick="toggleSidebar()">
    </div>
</div>

</body>
</html>