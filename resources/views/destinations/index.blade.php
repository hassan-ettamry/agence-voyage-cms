@extends('layouts.admin')

@section('topbar')
<x-layout.topbar searchPlaceholder="Search destinations...">
    <x-slot name="right">
        <a href="{{ route('destinations.create') }}">+ New Destination</a>
    </x-slot>
</x-layout.topbar>
@endsection

@section('content')
@if(session('success'))
    <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</div>
@endif

@if(!empty($showDemoContentCta))
    <section class="mb-6 rounded-xl border border-sky-100 bg-sky-50 p-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="text-xs font-semibold uppercase tracking-widest text-sky-600">Contenu voyage</div>
                <h2 class="mt-1 text-lg font-bold text-slate-950">Ajouter des destinations demo</h2>
                <p class="mt-1 text-sm text-sky-800">Import non destructif avec destinations, offres et medias pour animer les composants dynamiques.</p>
            </div>
            <a href="{{ route('demo-content.index') }}" class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                Ajouter du contenu demo
            </a>
        </div>
    </section>
@endif

<x-ui.stats-cards :stats="$stats" />

<x-ui.table-layout>
    <x-slot name="title">
        <span class="text-sm font-semibold text-gray-800">All Destinations</span>
        <span class="text-xs text-gray-400">({{ $destinations->total() }} records)</span>
    </x-slot>

    <x-slot name="actions">
        <x-ui.filter-tabs :filters="[
            'all' => 'All',
            'published' => 'Published',
            'draft' => 'Draft',
            'featured' => 'Featured'
        ]" />
        <a href="{{ route('destinations.create') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">+ New Destination</a>
    </x-slot>

    <x-slot name="table">
        <x-ui.data-table>
            <x-slot name="head">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Destination</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Country</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Offers</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Slug</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse($destinations as $destination)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            @php($cover = $destination->media->first())
                            <div class="h-10 w-10 overflow-hidden rounded-md bg-slate-100">
                                @if($cover)
                                    <img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-800">{{ $destination->name }}</div>
                                @if($destination->is_featured)
                                    <div class="text-xs font-medium text-indigo-500">Featured</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $destination->country }}</td>
                    <td class="px-5 py-3.5">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $destination->isPublished() ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                            {{ ucfirst($destination->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-500">{{ $destination->offers_count }}</td>
                    <td class="px-5 py-3.5 text-xs font-mono text-indigo-500">/destinations/{{ $destination->slug }}</td>
                    <td class="px-5 py-3.5">
                        <x-ui.actions :edit="route('destinations.edit', $destination)" :delete="route('destinations.destroy', $destination)" confirm="Delete this destination?" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="mx-auto max-w-md">
                            <h3 class="text-sm font-bold text-slate-900">No destinations yet.</h3>
                            <p class="mt-1 text-sm text-slate-500">Create a destination manually or add the travel demo content to populate your dynamic blocks.</p>
                            <div class="mt-4 flex justify-center gap-2">
                                <a href="{{ route('destinations.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">New Destination</a>
                                @if(!empty($showDemoContentCta))
                                    <a href="{{ route('demo-content.index') }}" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Demo content</a>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </x-slot>
        </x-ui.data-table>
    </x-slot>

    <x-slot name="footer">
        <x-ui.pagination :paginator="$destinations" />
    </x-slot>
</x-ui.table-layout>

@include('destinations.partials.create')
@endsection

@if(!empty($openCreateModal) || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal('createDestinationModal');
    });
</script>
@endif
