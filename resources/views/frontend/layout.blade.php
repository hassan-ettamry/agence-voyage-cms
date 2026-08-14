<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $metaTitle ?? $page->title ?? 'Website' }}</title>
    @if(!empty($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $themeAgency = $siteAgency ?? (isset($page) ? $page->agency : null);
        $themeCss = app(\App\Services\AgencyThemeService::class)->cssVariables($themeAgency);
    @endphp
    <style>
        :root { {!! $themeCss !!} }
    </style>
</head>

<body class="site-shell">
    <a href="#site-main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-white focus:px-4 focus:py-3 focus:font-bold focus:text-slate-950">
        Skip to content
    </a>

    @include('frontend.partials.site-header')

    <main id="site-main-content" class="min-h-screen">
        @yield('content')
    </main>

    @include('frontend.partials.site-footer')
</body>
</html>
