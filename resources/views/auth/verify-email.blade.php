@extends('layouts.auth')

@section('title', 'Vérifier votre adresse email')

@section('content')
<section class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10" aria-labelledby="verify-email-title">
    <h1 id="verify-email-title" class="mb-3 text-2xl font-semibold text-slate-900">Vérifiez votre adresse email</h1>
    <p class="text-sm leading-6 text-slate-600 sm:text-base">
        Nous avons envoyé un lien sécurisé à <strong class="font-semibold text-slate-900">{{ request()->user()->email }}</strong>.
        Ouvrez ce lien pour accéder à votre espace administrateur.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            Un nouveau lien de vérification a été envoyé.
        </div>
    @endif

    @if (app()->isLocal())
        <p class="mt-5 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            En local, le lien est disponible dans <code>storage/logs/laravel.log</code>.
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
        @csrf
        <button type="submit" class="w-full rounded-lg bg-slate-900 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">
            Renvoyer le lien
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-slate-700 hover:text-slate-950 hover:underline sm:text-base">
            Se déconnecter
        </button>
    </form>
</section>
@endsection
