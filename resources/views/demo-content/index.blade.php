@extends('layouts.admin')

@section('topbar')
<x-layout.topbar>
    <x-slot name="left">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </x-slot>
</x-layout.topbar>
@endsection

@section('content')
@php
    $state = $preview['state'];
    $minimums = $preview['minimums'];
    $result = session('demo_result');
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    <section class="rounded-xl border border-sky-100 bg-sky-50 p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <div class="text-xs font-semibold uppercase tracking-widest text-sky-600">Assistant contenu voyage</div>
                <h1 class="mt-2 text-2xl font-bold text-slate-950">Ajouter des donnees demo voyage</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-sky-900">
                    Cette action ajoute des destinations, offres et medias de demonstration publies. Elle est manuelle, non destructive et idempotente: les slugs demo existants ne sont jamais dupliques ni remplaces.
                </p>
            </div>

            <form method="POST" action="{{ route('demo-content.apply') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                    Ajouter le contenu demo
                </button>
            </form>
        </div>
    </section>

    @if(is_array($result))
        <section class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Import termine</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ $result['destinations_created'] }} destinations creees</div>
                <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ $result['offers_created'] }} offres creees</div>
                <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ $result['media_created'] }} medias crees</div>
                <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ $result['destinations_skipped'] }} destinations conservees</div>
                <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ $result['offers_skipped'] }} offres conservees</div>
                <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">{{ $result['media_reused'] }} medias reutilises</div>
            </div>
        </section>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Destinations publiees" :value="$state['published_destinations']" :note="'Minimum '.$minimums['published_destinations']" />
        <x-ui.stat-card label="Destinations vedettes" :value="$state['featured_destinations']" :note="'Minimum '.$minimums['featured_destinations']" color="text-indigo-500" />
        <x-ui.stat-card label="Offres publiees" :value="$state['published_offers']" :note="'Minimum '.$minimums['published_offers']" />
        <x-ui.stat-card label="Offres speciales" :value="$state['special_offers']" :note="'Minimum '.$minimums['special_offers']" color="text-rose-500" />
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-bold text-slate-900">Destinations demo</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($preview['destinations'] as $destination)
                    <div class="flex items-start justify-between gap-4 px-5 py-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">{{ $destination['name'] }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $destination['country'] }} / {{ $destination['slug'] }}</div>
                            @if($destination['is_featured'])
                                <div class="mt-1 text-xs font-medium text-indigo-500">Featured</div>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $destination['exists'] ? 'bg-slate-100 text-slate-500' : 'bg-emerald-50 text-emerald-600' }}">
                            {{ $destination['exists'] ? 'Existe deja' : 'A ajouter' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-bold text-slate-900">Offres demo</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($preview['offers'] as $offer)
                    <div class="flex items-start justify-between gap-4 px-5 py-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">{{ $offer['title'] }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $offer['duration_days'] }} jours / {{ number_format((float) $offer['price'], 2) }}</div>
                            @if($offer['is_special'])
                                <div class="mt-1 text-xs font-medium text-rose-500">Special offer</div>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $offer['exists'] ? 'bg-slate-100 text-slate-500' : 'bg-emerald-50 text-emerald-600' }}">
                            {{ $offer['exists'] ? 'Existe deja' : 'A ajouter' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
