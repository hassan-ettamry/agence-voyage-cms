<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>

    {{-- Charset --}}
    <meta charset="UTF-8">

    {{-- Responsive --}}
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    {{-- Title --}}
    <title>
        @yield('title', 'Site Builder — Vision')
    </title>

    {{-- CSRF --}}
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    {{-- Builder Dynamic Data --}}
    @yield('builder-data')

    {{-- Vite --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Extra Head --}}
    @stack('head')

</head>

<body class="h-full overflow-hidden bg-gray-50">

    <div id="app" class="h-full">

        @yield('content')

    </div>

    {{-- Extra Scripts --}}
    @stack('scripts')

</body>

</html>