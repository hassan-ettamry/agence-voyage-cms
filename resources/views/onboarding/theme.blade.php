@extends('layouts.onboarding')

@section('title', 'Choisir un thème')

@section('content')
<section>
    <div class="mb-6">
        <div class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Étape 3</div>
        <h1 class="mt-2 text-2xl font-bold text-slate-950">Choisissez votre ambiance visuelle</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Le thème recommandé pour <strong>{{ $template->name }}</strong> est présélectionné, mais vous pouvez en choisir un autre.</p>
    </div>

    <form method="POST" action="{{ route('onboarding.theme.store') }}">
        @csrf

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($themes as $theme)
                @php($variables = $theme->variables ?? [])
                <label class="group cursor-pointer">
                    <input type="radio" name="theme_id" value="{{ $theme->id }}" class="peer sr-only" {{ old('theme_id', $selectedThemeId) === $theme->id ? 'checked' : '' }}>
                    <article class="h-full overflow-hidden rounded-xl border-2 border-slate-200 bg-white shadow-sm transition group-hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:ring-4 peer-checked:ring-indigo-100">
                        <div class="p-5" style="background-color: {{ $variables['secondary'] ?? '#111827' }}">
                            <div class="rounded-lg bg-white/10 p-4">
                                <div class="h-2 w-16 rounded-full" style="background-color: {{ $variables['primary'] ?? '#2563eb' }}"></div>
                                <div class="mt-6 h-4 w-2/3 rounded-full bg-white/80"></div>
                                <div class="mt-3 h-3 w-1/2 rounded-full bg-white/40"></div>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="font-bold text-slate-950">{{ $theme->name }}</h2>
                                @if($template->theme_id === $theme->id)
                                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-600">Recommandé</span>
                                @endif
                            </div>
                            <div class="mt-4 flex gap-2">
                                @foreach(['primary', 'secondary', 'accent', 'surface'] as $key)
                                    <span class="h-5 w-5 rounded-full ring-1 ring-slate-200" style="background-color: {{ $variables[$key] ?? '#e5e7eb' }}"></span>
                                @endforeach
                            </div>
                        </div>
                    </article>
                </label>
            @endforeach
        </div>

        <div class="mt-7 flex items-center justify-between border-t border-slate-200 pt-5">
            <a href="{{ route('onboarding.template') }}" class="text-sm font-semibold text-slate-500 transition hover:text-slate-900">Retour</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Continuer</button>
        </div>
    </form>
</section>
@endsection
