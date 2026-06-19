@extends('layouts.onboarding')

@section('title', 'Profil de votre agence')

@section('content')
<section class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
    <div class="mb-7">
        <div class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Étape 1</div>
        <h1 class="mt-2 text-2xl font-bold text-slate-950">Présentez votre agence</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Ces informations seront utilisées comme identité de contact de votre site.</p>
    </div>

    <form method="POST" action="{{ route('onboarding.profile.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid gap-5 md:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Nom de l’agence</span>
                <input name="name" value="{{ old('name', $agency->name) }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Email de contact</span>
                <input name="email" type="email" value="{{ old('email', $agency->email) }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Téléphone</span>
                <input name="phone" value="{{ old('phone', $agency->phone) }}" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Logo</span>
                <input name="logo" type="file" accept=".jpg,.jpeg,.png,.webp" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-semibold file:text-indigo-700">
                <span class="mt-1 block text-xs text-slate-400">JPG, PNG ou WebP, 5 MB maximum.</span>
            </label>
        </div>

        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Adresse</span>
            <textarea name="address" rows="3" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">{{ old('address', $agency->address) }}</textarea>
        </label>

        <div class="flex justify-end border-t border-slate-100 pt-5">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                Continuer
            </button>
        </div>
    </form>
</section>
@endsection
