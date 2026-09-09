@extends('layouts.auth')

@section('title', 'Créer un compte')

@section('content')
<section class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10" aria-labelledby="register-title">
    <h1 id="register-title" class="sr-only">Créer un compte</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div>
            <label for="agency_name" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Nom de l’agence</label>
            <input
                id="agency_name"
                name="agency_name"
                type="text"
                value="{{ old('agency_name') }}"
                autocomplete="organization"
                required
                autofocus
                placeholder="Ex. Atlas Voyages"
                @class([
                    'w-full rounded-lg border bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4',
                    'border-red-300 focus:border-red-500 focus:ring-red-100' => $errors->has('agency_name'),
                    'border-slate-300 focus:border-slate-700 focus:ring-slate-100' => ! $errors->has('agency_name'),
                ])
                @error('agency_name') aria-invalid="true" aria-describedby="agency-name-error" @enderror
            >
            @error('agency_name')
                <p id="agency-name-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Votre nom</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    required
                    placeholder="Prénom et nom"
                    @class([
                        'w-full rounded-lg border bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4',
                        'border-red-300 focus:border-red-500 focus:ring-red-100' => $errors->has('name'),
                        'border-slate-300 focus:border-slate-700 focus:ring-slate-100' => ! $errors->has('name'),
                    ])
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                >
                @error('name')
                    <p id="name-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Adresse email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
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
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Mot de passe</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="8 caractères minimum"
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

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Confirmation</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="Répétez le mot de passe"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-700 focus:ring-4 focus:ring-slate-100"
                >
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-slate-900 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">
            Créer le compte
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500 sm:text-base">
        Vous avez déjà un compte ?
        <a href="{{ route('login') }}" class="font-medium text-slate-900 hover:underline">Se connecter</a>
    </p>
</section>
@endsection
