@extends('layouts.admin')

@section('topbar')

<x-layout.topbar searchPlaceholder="Search pages...">

    {{-- LEFT SIDE --}}
    <x-slot name="left">
        <a href="{{ url('/') }}" target="_blank" rel="noopener">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="11" cy="12" r="7"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h14M11 5c2 2 3 4.3 3 7s-1 5-3 7M11 5c-2 2-3 4.3-3 7s1 5 3 7M16 5h3v3M19 5l-5 5"/>
            </svg>
            Visit site
        </a>
    </x-slot>

    {{-- RIGHT SIDE --}}
    <x-slot name="right">
        <button type="button" onclick="openModal('createPageModal')">
            + New Page
        </button>
    </x-slot>

</x-layout.topbar>

@endsection

@section('content')

{{-- ================= STATS ================= --}}
<x-ui.stats-cards :stats="$stats" />


{{-- ================= TABLE ================= --}}
<x-ui.table-layout>

    {{-- TITLE --}}
    <x-slot name="title">
        <span class="text-sm font-semibold text-gray-800">All Pages</span>
        <span id="record-count" class="text-xs text-gray-400">
            ({{ $pages->total() }} records)
        </span>
    </x-slot>

    {{-- ACTIONS --}}
    <x-slot name="actions">

        <x-ui.filter-tabs :filters="[
            'all' => 'All',
            'published' => 'Published',
            'draft' => 'Draft'
        ]" />

        <button class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export
        </button>

        <button type="button"
                onclick="openModal('createPageModal')"
                class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition flex items-center gap-1.5">
            + New Page
        </button>

    </x-slot>

    {{-- TABLE --}}
    <x-slot name="table">

        <x-ui.data-table>

            <x-slot name="head">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-16">ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Page Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Theme</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Plan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">URL Slug</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Created</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </x-slot>

            <x-slot name="body">
                @foreach($pages as $page)
                @php
                    $type = strtolower($page->type ?? 'custom');
                    $typeBg = match($type) {
                        'default' => 'bg-amber-100 text-amber-600',
                        'modal'   => 'bg-purple-100 text-purple-600',
                        default   => 'bg-indigo-100 text-indigo-600',
                    };
                    $plan = strtolower($page->plan ?? 'free');
                    $planBg = match($plan) {
                        'paid' => 'bg-orange-50 border-orange-200 text-orange-600',
                        default => 'bg-emerald-50 border-emerald-200 text-emerald-600',
                    };
                    $planLabel = strtoupper($plan);
                    $initial = strtoupper(substr($page->title, 0, 1));
                    $avatarColors = ['bg-indigo-500','bg-violet-500','bg-emerald-500','bg-sky-500','bg-rose-500','bg-amber-500','bg-teal-500','bg-pink-500'];
                    $avatarColor = $avatarColors[crc32($page->title) % count($avatarColors)];
                @endphp
                <tr class="hover:bg-gray-50 transition-colors page-row"
                    data-filter-value="{{ $page->status }}">

                    {{-- ID --}}
                    <td class="px-5 py-3.5 text-gray-400 font-mono text-xs">{{ $page->id }}</td>

                    {{-- Title with avatar --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 {{ $avatarColor }} rounded-md flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ $initial }}
                            </div>
                            <span class="font-semibold text-gray-800 text-sm">{{ $page->title }}</span>
                        </div>
                    </td>

                    {{-- Theme --}}
                    <td class="px-5 py-3.5 text-xs font-medium text-gray-500 uppercase tracking-wide">
                        {{ $page->theme ?? 'EVENTIONS' }}
                    </td>

                    {{-- Type badge --}}
                    <td class="px-5 py-3.5">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-md uppercase tracking-wide {{ $typeBg }}">
                            {{ strtoupper($type) }}
                        </span>
                    </td>

                    {{-- Menu --}}
                    <td class="px-5 py-3.5 text-xs text-gray-500">
                        {{ $page->menuItems->first()?->menu?->name ?? 'No menu' }}
                    </td>

                    {{-- Plan --}}
                    <td class="px-5 py-3.5">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $planBg }}">
                            {{ $planLabel }}
                        </span>
                    </td>

                    {{-- Slug --}}
                    <td class="px-5 py-3.5">
                        <span class="text-xs text-indigo-500 font-mono hover:text-indigo-700 cursor-pointer">/{{ $page->slug }}</span>
                    </td>

                    {{-- Created --}}
                    <td class="px-5 py-3.5 text-xs text-gray-400 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($page->created_at)->format('Y-m-d H:i') }}
                    </td>

                    {{-- ACTIONS USING GLOBAL COMPONENT --}}
                    <td class="px-5 py-3.5">
                        <x-ui.actions
                            :builder="route('pages.builder', $page)"
                            :edit="route('pages.edit', $page)"
                            :delete="route('pages.destroy', $page)"
                            :preview="$page->isPublished() ? app(\App\Services\PublicSiteUrl::class)->page($page->agency, $page) : null"
                            confirm="Delete this page?"
                        />
                    </td>

                </tr>
                @endforeach
            </x-slot>

        </x-ui.data-table>

    </x-slot>

    {{-- FOOTER --}}
    <x-slot name="footer">
        <x-ui.pagination :paginator="$pages" />
    </x-slot>

</x-ui.table-layout>

{{-- ================= MODAL ================= --}}
@include('pages.partials.create')

@endsection

@if(!empty($openCreateModal) || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal('createPageModal');
    });
</script>
@endif
