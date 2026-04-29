<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex items-center justify-center">
    @yield('content')
</div>
</body>
</html>