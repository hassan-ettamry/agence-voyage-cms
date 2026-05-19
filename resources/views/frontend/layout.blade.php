<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $page->title ?? 'Website' }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-white text-gray-900">

    {{-- NAVBAR --}}
    <header class="border-b">

        <div class="max-w-7xl mx-auto px-6 py-4 flex gap-6">

            @if(isset($menu))

                @foreach($menu as $item)

                    <a
                        href="{{ $item['url'] }}"
                        class="hover:text-blue-500"
                    >
                        {{ $item['title'] }}
                    </a>

                @endforeach

            @endif

        </div>

    </header>

    {{-- CONTENT --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="border-t mt-10 p-6 text-center text-sm text-gray-500">

        © {{ date('Y') }}

    </footer>

</body>
</html>