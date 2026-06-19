<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $metaTitle ?? $page->title ?? 'Website' }}
    </title>
    @if(!empty($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @php
        $themeAgency = $siteAgency ?? (isset($page) ? $page->agency : null);
        $themeCss = app(\App\Services\AgencyThemeService::class)->cssVariables($themeAgency);
    @endphp
    <style>
        :root { {!! $themeCss !!} }
        body {
            background: var(--site-background);
            color: var(--site-text);
            font-family: var(--site-body-font);
        }
        h1, h2, h3, h4, h5, h6 { font-family: var(--site-heading-font); }
    </style>
</head>

<body>

    {{-- NAVBAR --}}
    <header class="border-b" style="border-color: var(--site-border); background: var(--site-surface);">

        <div class="max-w-7xl mx-auto px-6 py-4 flex gap-6">

            @if(isset($menu))

                @foreach($menu as $item)

                    <a
                        href="{{ $item['url'] }}"
                        class="transition hover:opacity-70"
                        style="color: var(--site-text);"
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
    <footer class="border-t mt-10 p-6 text-center text-sm" style="border-color: var(--site-border); color: var(--site-muted); background: var(--site-surface);">

        © {{ date('Y') }}

    </footer>

</body>
</html>
