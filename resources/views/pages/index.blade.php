@extends('layouts.admin')

@section('content')

{{-- ================= STATS ================= --}}
<x-ui.stats-cards>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Total Pages</p>
        <p class="text-3xl font-bold text-gray-900">{{ $totalPages ?? $pages->total() }}</p>
        <p class="mt-1.5 text-xs text-emerald-500 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
            4 this month
        </p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Published</p>
        <p class="text-3xl font-bold text-gray-900">{{ $publishedCount ?? '—' }}</p>
        <p class="mt-1.5 text-xs text-gray-400">92% of total</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Custom Pages</p>
        <p class="text-3xl font-bold text-gray-900">{{ $customCount ?? '—' }}</p>
        <p class="mt-1.5 text-xs text-gray-400">Template based</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Default Pages</p>
        <p class="text-3xl font-bold text-gray-900">{{ $defaultCount ?? '—' }}</p>
        <p class="mt-1.5 text-xs text-gray-400">System generated</p>
    </div>

</x-ui.stats-cards>


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
            'custom' => 'Custom',
            'default' => 'Default',
            'modal' => 'Modal'
        ]" />

        <button class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export
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
                <tr class="hover:bg-gray-50 transition-colors page-row" data-type="{{ $type }}">

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
                        {{ $page->menu ?? 'Primary Menu' }}
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

                    {{-- Actions — toujours visibles --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1">

                            {{-- Edit --}}
                            <a href="{{ route('pages.edit', $page) }}" title="Edit"
                               class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-md text-gray-400 hover:text-indigo-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Preview --}}
                            <a href="#" title="Preview"
                               class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-md text-gray-400 hover:text-indigo-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('pages.destroy', $page) }}" class="inline"
                                  onsubmit="return confirm('Delete this page?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete"
                                        class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-red-300 hover:bg-red-50 rounded-md text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
                @endforeach
            </x-slot>

        </x-ui.data-table>

    </x-slot>

    {{-- FOOTER --}}
    <x-slot name="footer">

        <div class="flex items-center justify-between w-full">

            {{-- LEFT: INFO --}}
            <span class="text-xs text-gray-500" id="pagination-info">
                Showing
                <span class="font-semibold text-gray-700">
                    {{ $pages->firstItem() ?? 0 }}–{{ $pages->lastItem() ?? 0 }}
                </span>
                of
                <span class="font-semibold text-gray-700">
                    {{ $pages->total() }}
                </span>
                pages
            </span>

            {{-- RIGHT: CONTROLS --}}
            <div class="flex items-center gap-2">

                {{-- Rows per page --}}
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <span>Rows:</span>
                    <select
                        class="border border-gray-200 rounded-md px-2 py-1 text-xs bg-white"
                        onchange="window.location.href='?per_page='+this.value+'&page=1'">

                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="h-4 w-px bg-gray-200"></div>

                {{-- Pagination --}}
                <div class="flex items-center gap-1">

                    {{-- Prev --}}
                    @if($pages->onFirstPage())
                        <span class="w-7 h-7 flex items-center justify-center text-gray-300 cursor-not-allowed">
                            ‹
                        </span>
                    @else
                        <a href="{{ $pages->previousPageUrl() }}"
                        class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md hover:bg-gray-100 text-gray-500">
                            ‹
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach($pages->getUrlRange(1, $pages->lastPage()) as $page => $url)

                        @if($page == $pages->currentPage())
                            <span class="w-7 h-7 flex items-center justify-center bg-indigo-600 text-white text-xs rounded-md font-semibold">
                                {{ $page }}
                            </span>

                        @elseif($page <= 3 || $page == $pages->lastPage() || abs($page - $pages->currentPage()) <= 1)
                            <a href="{{ $url }}"
                            class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md text-xs hover:bg-gray-100 text-gray-600">
                                {{ $page }}
                            </a>

                        @elseif($page == 4)
                            <span class="w-7 h-7 flex items-center justify-center text-gray-400 text-xs">
                                …
                            </span>
                        @endif

                    @endforeach

                    {{-- Next --}}
                    @if($pages->hasMorePages())
                        <a href="{{ $pages->nextPageUrl() }}"
                        class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md hover:bg-gray-100 text-gray-500">
                            ›
                        </a>
                    @else
                        <span class="w-7 h-7 flex items-center justify-center text-gray-300 cursor-not-allowed">
                            ›
                        </span>
                    @endif

                </div>

            </div>

        </div>

    </x-slot>

</x-ui.table-layout>

@endsection