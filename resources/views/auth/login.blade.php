@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<section class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10" aria-labelledby="login-title">
    <h1 id="login-title" class="sr-only">Connexion</h1>

    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
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
                placeholder="admin@votre-agence.com"
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

        <div>
            <div class="mb-2 flex items-center justify-between gap-4">
                <label for="password" class="block text-sm font-medium text-slate-700 sm:text-base">Mot de passe</label>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-slate-700 hover:text-slate-950 hover:underline">
                    Mot de passe oublié ?
                </a>
            </div>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                placeholder="Votre mot de passe"
                @class([
                    'w-full rounded-lg border bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4',
                    'border-red-300 focus:border-red-500 focus:ring-red-100' => $errors->has('password'),
                    'border-slate-300 focus:border-slate-700 focus:ring-slate-100' => ! $errors->has('password'),
                ])
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
            >
            @error('password')
                <p id="password-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex w-fit cursor-pointer items-center gap-2 text-sm text-slate-600 sm:text-base">
            <input
                type="checkbox"
                name="remember"
                value="1"
                @checked(old('remember'))
                class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500"
            >
            Rester connecté
        </label>

        <button type="submit" class="w-full rounded-lg bg-slate-900 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">
            Se connecter
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500 sm:text-base">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-medium text-slate-900 hover:underline">Créer un compte</a>
    </p>
</section>
@endsection
