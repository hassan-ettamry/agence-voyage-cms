@extends('layouts.admin')

@section('topbar')
<x-layout.topbar />
@endsection

@section('content')
<div class="mx-auto w-full max-w-4xl py-6">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('account.settings.edit') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Account settings</a>
            <h1 class="mt-3 text-3xl font-black text-slate-900">Active sessions</h1>
            <p class="mt-2 text-sm text-slate-600">Review devices currently connected to your account.</p>
        </div>

        @if($managementAvailable && count($sessions) > 1)
            <form method="POST" action="{{ route('account.security.sessions.destroy-others') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50">Sign out other sessions</button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @unless($managementAvailable)
        <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Detailed session management requires <code>SESSION_DRIVER=database</code>. Your current session remains active.
        </div>
    @endunless

    <div class="space-y-3">
        @foreach($sessions as $session)
            <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-bold text-slate-900">{{ $session['user_agent'] }}</h2>
                        @if($session['is_current'])
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Current</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ $session['ip_address'] }} · Active {{ $session['last_activity']->diffForHumans() }}</p>
                </div>

                @if($managementAvailable && ! $session['is_current'])
                    <form method="POST" action="{{ route('account.security.sessions.destroy', $session['fingerprint']) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Sign out</button>
                    </form>
                @endif
            </article>
        @endforeach
    </div>
</div>
@endsection
