@extends('layouts.admin')

@section('topbar')
<x-layout.topbar />
@endsection

@section('content')
<div class="mx-auto w-full max-w-2xl py-6">
    <div class="mb-6">
        <a href="{{ route('account.settings.edit') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Account settings</a>
        <h1 class="mt-3 text-3xl font-black text-slate-900">Change email address</h1>
        <p class="mt-2 text-sm text-slate-600">The new address must be verified before you can return to the administration area.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('account.security.email.update') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        @csrf
        @method('PUT')

        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">New email address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100">
            @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="current_password" class="mb-2 block text-sm font-semibold text-slate-700">Current password</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100">
            @error('current_password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="w-full rounded-lg bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-800">Change email and verify</button>
    </form>
</div>
@endsection
