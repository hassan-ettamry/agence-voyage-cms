@extends('layouts.onboarding')

@section('title', 'Choisir un template de site')

@section('content')
<section>
    <div class="mb-6">
        <div class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Étape 2</div>
        <h1 class="mt-2 text-2xl font-bold text-slate-950">Choisissez la base de votre site</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">La sélection est sauvegardée maintenant. Le site ne sera appliqué qu’après votre confirmation finale.</p>
    </div>

    <form method="POST" action="{{ route('onboarding.template.store') }}">
        @csrf

        <div class="grid gap-5 lg:grid-cols-2">
            @forelse($templates as $template)
                <label class="group cursor-pointer">
                    <input type="radio" name="template_id" value="{{ $template->id }}" class="peer sr-only" {{ old('template_id', $selectedTemplateId) === $template->id ? 'checked' : '' }}>
                    <article class="h-full overflow-hidden rounded-xl border-2 border-slate-200 bg-white shadow-sm transition group-hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:ring-4 peer-checked:ring-indigo-100">
                        <div class="flex min-h-[150px] items-center justify-center bg-slate-950 p-6 text-center text-white">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.25em] text-white/50">{{ $template->theme?->name ?? 'Sans thème' }}</div>
                                <div class="mt-3 text-2xl font-black">{{ $template->name }}</div>
                            </div>
                        </div>
                        <div class="space-y-4 p-5">
                            <p class="text-sm leading-6 text-slate-500">{{ $template->description }}</p>
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-widest text-slate-400">Pages incluses</div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($template->pages ?? [] as $page)
                                        <span class="rounded-full bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ $page['title'] ?? 'Page' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </article>
                </label>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-400 lg:col-span-2">Aucun template actif disponible.</div>
            @endforelse
        </div>

        <div class="mt-7 flex items-center justify-between border-t border-slate-200 pt-5">
            <a href="{{ route('onboarding.profile') }}" class="text-sm font-semibold text-slate-500 transition hover:text-slate-900">Retour</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Continuer</button>
        </div>
    </form>
</section>
@endsection
