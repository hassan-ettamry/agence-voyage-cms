@extends('layouts.onboarding')

@section('title', 'Vérifier la configuration')

@section('content')
<section class="mx-auto max-w-4xl">
    <div class="mb-6">
        <div class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Étape 4</div>
        <h1 class="mt-2 text-2xl font-bold text-slate-950">Tout est prêt</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Vérifiez votre sélection avant de configurer le site de l’agence.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-widest text-slate-400">Agence</div>
            <h2 class="mt-3 text-lg font-bold text-slate-950">{{ $agency->name }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-slate-400">Email</dt><dd class="font-medium text-slate-700">{{ $agency->email }}</dd></div>
                <div><dt class="text-slate-400">Téléphone</dt><dd class="font-medium text-slate-700">{{ $agency->phone ?: 'Non renseigné' }}</dd></div>
                <div><dt class="text-slate-400">Adresse</dt><dd class="font-medium text-slate-700">{{ $agency->address ?: 'Non renseignée' }}</dd></div>
            </dl>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-widest text-slate-400">Design choisi</div>
            <h2 class="mt-3 text-lg font-bold text-slate-950">{{ $template->name }}</h2>
            <p class="mt-1 text-sm text-slate-500">Thème : <strong class="text-slate-700">{{ $theme->name }}</strong></p>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($template->pages ?? [] as $page)
                    <span class="rounded-full bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ $page['title'] ?? 'Page' }}</span>
                @endforeach
            </div>
        </article>
    </div>

    <form method="POST" action="{{ route('onboarding.complete') }}" class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        @csrf

        @if($requiresReplacementConfirmation)
            <label class="flex gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <input type="checkbox" name="confirm_replace" value="1" class="mt-1 rounded border-amber-300 text-indigo-600 focus:ring-indigo-500">
                <span>
                    <strong class="block">Remplacer et archiver le site actuel</strong>
                    Les pages et menus actuels seront archivés avant l’application du nouveau template.
                </span>
            </label>
        @elseif($agency->active_site_template_id === $template->id)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                Ce template est déjà actif. Vos pages seront conservées et seul le profil/thème sera mis à jour.
            </div>
        @endif

        @if($requiresThemeResetConfirmation)
            <label class="mt-4 flex gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <input type="checkbox" name="confirm_theme_reset" value="1" class="mt-1 rounded border-amber-300 text-indigo-600 focus:ring-indigo-500">
                <span>
                    <strong class="block">Réinitialiser les personnalisations du thème</strong>
                    Le nouveau template ou thème remplacera les personnalisations globales actuelles.
                </span>
            </label>
        @endif

        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-5">
            <a href="{{ route('onboarding.theme') }}" class="text-sm font-semibold text-slate-500 transition hover:text-slate-900">Retour</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                Configurer mon site
            </button>
        </div>
    </form>
</section>
@endsection
