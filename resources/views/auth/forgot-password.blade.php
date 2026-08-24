@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
<section class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10" aria-labelledby="forgot-password-title">
    <h1 id="forgot-password-title" class="mb-3 text-2xl font-semibold text-slate-900">Mot de passe oublié</h1>
    <p class="mb-6 text-sm leading-6 text-slate-600 sm:text-base">
        Indiquez votre adresse email. Si un compte existe, vous recevrez un lien sécurisé.
    </p>

    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Adresse email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="email"
                required
                autofocus
                placeholder="vous@agence.com"
                @class([
                    'w-full rounded-lg border bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4',
                    'border-red-300 focus:border-red-500 focus:ring-red-100' => $errors->has('email'),
                    'border-slate-300 focus:border-slate-700 focus:ring-slate-100' => ! $errors->has('email'),
                ])
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
            >
            @error('email')
                <p id="email-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded-lg bg-slate-900 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">
            Envoyer le lien
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500 sm:text-base">
        <a href="{{ route('login') }}" class="font-medium text-slate-900 hover:underline">Retour à la connexion</a>
    </p>
</section>
@endsection
