<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Configuration de votre agence')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 text-slate-900">
    @php
        $steps = [
            'profile' => 'Profil',
            'template' => 'Template',
            'theme' => 'Thème',
            'review' => 'Vérification',
        ];
        $stepKeys = array_keys($steps);
        $currentIndex = array_search($currentStep ?? 'profile', $stepKeys, true);
    @endphp

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-sm font-black text-white">VB</span>
                <span>
                    <span class="block text-sm font-bold text-slate-950">Website Builder</span>
                    <span class="block text-xs text-slate-400">Configuration de l’agence</span>
                </span>
            </a>

            <form method="POST" action="{{ route('onboarding.dismiss') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-slate-500 transition hover:text-slate-900">
                    Reporter
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-8 lg:py-12">
        <nav aria-label="Progression" class="mb-8">
            <ol class="grid grid-cols-2 gap-2 md:grid-cols-4">
                @foreach($steps as $key => $label)
                    @php($index = array_search($key, $stepKeys, true))
                    <li class="flex items-center gap-3 border-b-2 px-2 py-3 {{ $index <= $currentIndex ? 'border-indigo-600 text-indigo-700' : 'border-slate-200 text-slate-400' }}">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $index <= $currentIndex ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                            {{ $index + 1 }}
                        </span>
                        <span class="text-sm font-semibold">{{ $label }}</span>
                    </li>
                @endforeach
            </ol>
        </nav>

        @if(session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <div class="font-semibold">Vérifiez les informations suivantes :</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
