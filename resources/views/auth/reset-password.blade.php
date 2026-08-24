@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<section class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10" aria-labelledby="reset-password-title">
    <h1 id="reset-password-title" class="mb-6 text-2xl font-semibold text-slate-900">Réinitialiser le mot de passe</h1>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Adresse email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $email) }}"
                autocomplete="email"
                required
                autofocus
                @class([
                    'w-full rounded-lg border bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition focus:ring-4',
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
            <label for="password" class="mb-2 block text-sm font-medium text-slate-700 sm:text-base">Nouveau mot de passe</label>
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
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition focus:border-slate-700 focus:ring-4 focus:ring-slate-100"
            >
        </div>

        <button type="submit" class="w-full rounded-lg bg-slate-900 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">
            Enregistrer le nouveau mot de passe
        </button>
    </form>
</section>
@endsection
