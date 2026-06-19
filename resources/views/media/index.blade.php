@extends('layouts.admin')

@section('topbar')
<x-layout.topbar>
    <x-slot name="right">
        <button type="button"
                onclick="openModal('uploadMediaModal')"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
            </svg>
            Upload Image
        </button>
    </x-slot>
</x-layout.topbar>
@endsection

@section('content')
@php
    $filters = $filters ?? [
        'search' => request('search', ''),
        'type' => request('type', 'all'),
        'sort' => request('sort', 'latest'),
        'per_page' => (int) request('per_page', 8),
    ];
    $viewMode = request('view', 'grid') === 'list' ? 'list' : 'grid';
    $tabs = [
        ['key' => 'all', 'label' => 'All', 'count' => $typeCounts['all'] ?? 0],
        ['key' => 'images', 'label' => 'Images', 'count' => $typeCounts['images'] ?? 0],
        ['key' => 'videos', 'label' => 'Videos', 'count' => $typeCounts['videos'] ?? 0],
        ['key' => 'documents', 'label' => 'Documents', 'count' => $typeCounts['documents'] ?? 0],
        ['key' => 'unused', 'label' => 'Unused', 'count' => $typeCounts['unused'] ?? 0],
        ['key' => 'used', 'label' => 'Used', 'count' => $typeCounts['used'] ?? 0],
    ];
    $statStyles = [
        'indigo' => 'from-indigo-50 to-white text-indigo-600 ring-indigo-100',
        'emerald' => 'from-emerald-50 to-white text-emerald-600 ring-emerald-100',
        'amber' => 'from-amber-50 to-white text-amber-600 ring-amber-100',
        'sky' => 'from-sky-50 to-white text-sky-600 ring-sky-100',
    ];
    $queryWithoutPage = request()->except('page');
@endphp

<style>
    @media (min-width: 1280px) {
        [data-media-page] .media-density {
            max-width: 1611px !important;
            zoom: 0.9;
        }
    }

    @supports not (zoom: 1) {
        @media (min-width: 1280px) {
            [data-media-page] .media-density {
                transform: scale(0.9);
                transform-origin: top center;
                width: 111.111%;
            }
        }
    }
</style>

<div class="-m-6 min-h-[calc(100%+3rem)] overflow-x-hidden bg-[#f8fafc] px-4 py-6 text-slate-950 sm:px-6 lg:px-[30px] lg:py-[32px]" data-media-page>
    <div class="media-density mx-auto max-w-[1450px] space-y-6 pb-6">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="overflow-hidden rounded-[28px] border border-white/70 bg-white shadow-sm">
        <div class="relative bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 px-6 py-7 text-white sm:px-8">
            <div class="absolute right-0 top-0 h-36 w-36 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-indigo-200">Asset manager</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Media Library</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                        Upload, organize, preview and reuse your agency images across destinations, offers and builder pages.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="button"
                            data-media-focus-search
                            class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4" />
                        </svg>
                        Filters
                    </button>
                    <button type="button"
                            onclick="openModal('uploadMediaModal')"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-950 shadow-lg shadow-black/20 transition hover:-translate-y-0.5">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                        </svg>
                        Upload Image
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($stats as $stat)
                @php
                    $tone = $statStyles[$stat['tone'] ?? 'indigo'] ?? $statStyles['indigo'];
                @endphp
                <article class="rounded-2xl bg-gradient-to-br {{ $tone }} p-5 ring-1 transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.22em] text-slate-400">{{ $stat['label'] }}</p>
                            <p class="mt-3 text-3xl font-black text-slate-950">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-sm font-medium text-slate-500">{{ $stat['note'] }}</p>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white shadow-sm">
                            @switch($stat['label'])
                                @case('Used')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 1 0 9 9" />
                                    </svg>
                                    @break
                                @case('Storage')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                                    </svg>
                                    @break
                                @case('This Month')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v4m8-4v4M3 10h18" />
                                        <rect width="18" height="18" x="3" y="4" rx="2" />
                                    </svg>
                                    @break
                                @default
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect width="18" height="18" x="3" y="3" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8 14 2.5-3 2 2.5L15 10l3 4" />
                                    </svg>
                            @endswitch
                        </div>
                    </div>
                    <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-slate-200/80">
                        <div class="h-full w-2/3 rounded-full bg-current opacity-70"></div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <form method="POST"
          action="{{ route('media.store') }}"
          enctype="multipart/form-data"
          data-media-quick-upload
          class="rounded-[24px] border-2 border-dashed border-indigo-200 bg-indigo-50/50 p-6 text-center transition hover:border-indigo-400 hover:bg-indigo-50">
        @csrf
        <label for="quick-media-upload" class="block cursor-pointer">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-indigo-600 shadow-sm">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5" />
                </svg>
            </span>
            <span class="mt-4 block text-base font-black text-slate-950">Drop your image here or click to upload</span>
            <span class="mt-1 block text-sm text-slate-500">JPG, PNG or WEBP up to 5 MB. Use the upload button if you want to add title and alt text immediately.</span>
        </label>
        <input id="quick-media-upload"
               type="file"
               name="file"
               accept="image/jpeg,image/png,image/webp"
               class="sr-only"
               onchange="if (this.files.length) this.form.submit()">
    </form>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-4">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <nav class="flex flex-wrap gap-2">
                    @foreach($tabs as $tab)
                        @php
                            $activeTab = $filters['type'] === $tab['key'];
                        @endphp
                        <a href="{{ route('media.index', array_merge(request()->except(['type', 'page']), ['type' => $tab['key']])) }}"
                           class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-bold transition {{ $activeTab ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $tab['label'] }}
                            <span class="rounded-full {{ $activeTab ? 'bg-white/20 text-white' : 'bg-white text-slate-500' }} px-2 py-0.5 text-xs">{{ $tab['count'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <form method="GET" action="{{ route('media.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input type="hidden" name="type" value="{{ $filters['type'] }}">
                    <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <label class="relative block sm:w-72">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3" />
                            </svg>
                        </span>
                        <input name="search"
                               value="{{ $filters['search'] }}"
                               data-media-search
                               placeholder="Search media..."
                               class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm font-medium text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                    </label>
                    <select name="sort"
                            onchange="this.form.submit()"
                            class="h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                        <option value="latest" @selected($filters['sort'] === 'latest')>Newest first</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest first</option>
                        <option value="name" @selected($filters['sort'] === 'name')>Name A-Z</option>
                        <option value="size" @selected($filters['sort'] === 'size')>Largest file</option>
                    </select>
                    <button type="submit" class="sr-only">Apply filters</button>
                </form>

                <div class="inline-flex w-fit rounded-xl border border-slate-200 bg-slate-50 p-1">
                    <a href="{{ route('media.index', array_merge(request()->except(['view', 'page']), ['view' => 'grid'])) }}"
                       class="rounded-lg px-3 py-2 text-sm font-bold transition {{ $viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}"
                       aria-label="Grid view">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="7" height="7" x="3" y="3" rx="1" />
                            <rect width="7" height="7" x="14" y="3" rx="1" />
                            <rect width="7" height="7" x="3" y="14" rx="1" />
                            <rect width="7" height="7" x="14" y="14" rx="1" />
                        </svg>
                    </a>
                    <a href="{{ route('media.index', array_merge(request()->except(['view', 'page']), ['view' => 'list'])) }}"
                       class="rounded-lg px-3 py-2 text-sm font-bold transition {{ $viewMode === 'list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}"
                       aria-label="List view">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-5">
            @if($mediaAssets->count())
                <div class="{{ $viewMode === 'grid' ? 'grid gap-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4' : 'space-y-4' }}">
                    @foreach($mediaAssets as $media)
                        @php
                            $title = $media->title ?: pathinfo($media->original_name, PATHINFO_FILENAME);
                            $isUsed = (($media->destinations_count ?? 0) + ($media->offers_count ?? 0)) > 0;
                            $extension = \Illuminate\Support\Str::upper(pathinfo($media->original_name, PATHINFO_EXTENSION) ?: 'IMG');
                            $size = $media->size >= 1048576 ? number_format($media->size / 1048576, 1).' MB' : number_format($media->size / 1024, 1).' KB';
                        @endphp
                        <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-xl hover:shadow-slate-200/70 {{ $viewMode === 'list' ? 'flex flex-col sm:flex-row' : '' }}">
                            <div class="relative {{ $viewMode === 'list' ? 'sm:w-64 sm:shrink-0' : '' }}">
                                <div class="{{ $viewMode === 'list' ? 'h-48 sm:h-full' : 'aspect-[4/3]' }} bg-slate-100">
                                    <img src="{{ $media->url }}"
                                         alt="{{ $media->alt_text ?? $title }}"
                                         class="h-full w-full object-cover">
                                </div>
                                <div class="absolute left-3 top-3 flex items-center gap-2">
                                    <span class="rounded-full bg-white/90 px-2.5 py-1 text-xs font-black text-slate-700 shadow-sm">{{ $extension }}</span>
                                    @if($isUsed)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500 px-2.5 py-1 text-xs font-black text-white shadow-sm">
                                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                            Linked
                                        </span>
                                    @endif
                                </div>
                                <div class="absolute inset-x-3 bottom-3 flex translate-y-2 items-center justify-end gap-2 opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100">
                                    <button type="button"
                                            data-media-preview
                                            data-preview-url="{{ $media->url }}"
                                            data-preview-title="{{ $title }}"
                                            data-preview-name="{{ $media->original_name }}"
                                            data-preview-size="{{ $size }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-slate-700 shadow-lg transition hover:bg-indigo-600 hover:text-white"
                                            aria-label="Preview {{ $title }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </button>
                                    <a href="{{ route('media.edit', $media) }}"
                                       class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-slate-700 shadow-lg transition hover:bg-indigo-600 hover:text-white"
                                       aria-label="Edit {{ $title }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                    </a>
                                    <a href="{{ $media->url }}"
                                       download
                                       class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-slate-700 shadow-lg transition hover:bg-indigo-600 hover:text-white"
                                       aria-label="Download {{ $title }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 21h14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="flex min-w-0 flex-1 flex-col p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h2 class="truncate text-base font-black text-slate-950">{{ $title }}</h2>
                                        <p class="mt-1 truncate text-sm text-slate-500">{{ $media->original_name }}</p>
                                    </div>
                                    <form method="POST"
                                          action="{{ route('media.destroy', $media) }}"
                                          onsubmit="return confirm('Delete this media?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl border border-red-100 bg-red-50 text-red-500 transition hover:bg-red-500 hover:text-white"
                                                aria-label="Delete {{ $title }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18m-2 0-.8 14.2A2 2 0 0 1 16.2 22H7.8a2 2 0 0 1-2-1.8L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-500">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1">{{ $size }}</span>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1">{{ $media->created_at?->format('M d, Y') }}</span>
                                    <span class="rounded-full {{ $isUsed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1">
                                        {{ $isUsed ? 'In use' : 'Unused' }}
                                    </span>
                                </div>
                                @if($media->alt_text)
                                    <p class="mt-4 line-clamp-2 text-sm leading-6 text-slate-500">{{ $media->alt_text }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8 14 2.5-3 2 2.5L15 10l3 4" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-black text-slate-950">No media found</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Upload your first travel image or adjust the filters to find existing assets.
                    </p>
                    <button type="button"
                            onclick="openModal('uploadMediaModal')"
                            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">
                        Upload Image
                    </button>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-4 border-t border-slate-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <p class="text-sm font-medium text-slate-500">
                @if($mediaAssets->total())
                    Showing {{ $mediaAssets->firstItem() }}-{{ $mediaAssets->lastItem() }} of {{ $mediaAssets->total() }} results
                @else
                    Showing 0 results
                @endif
            </p>

            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('media.index') }}" class="flex items-center gap-2">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        @if(is_scalar($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label class="text-sm font-semibold text-slate-500" for="media-per-page">Rows</label>
                    <select id="media-per-page"
                            name="per_page"
                            onchange="this.form.submit()"
                            class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($filters['per_page'] === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>

                @if($mediaAssets->hasPages())
                    <nav class="flex items-center gap-1">
                        @if($mediaAssets->onFirstPage())
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-300">
                                <span class="sr-only">Previous</span>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $mediaAssets->previousPageUrl() }}" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                                <span class="sr-only">Previous</span>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                                </svg>
                            </a>
                        @endif

                        @foreach($mediaAssets->getUrlRange(1, $mediaAssets->lastPage()) as $page => $url)
                            @if($page === $mediaAssets->currentPage())
                                <span class="flex h-10 min-w-10 items-center justify-center rounded-xl bg-indigo-600 px-3 text-sm font-black text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="flex h-10 min-w-10 items-center justify-center rounded-xl px-3 text-sm font-bold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($mediaAssets->hasMorePages())
                            <a href="{{ $mediaAssets->nextPageUrl() }}" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                                <span class="sr-only">Next</span>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                </svg>
                            </a>
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-300">
                                <span class="sr-only">Next</span>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                </svg>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </section>
    </div>

<div id="mediaPreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4">
    <div class="max-h-[92vh] w-full max-w-5xl overflow-hidden rounded-[28px] bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div class="min-w-0">
                <h2 id="mediaPreviewTitle" class="truncate text-lg font-black text-slate-950">Preview</h2>
                <p id="mediaPreviewMeta" class="truncate text-sm text-slate-500"></p>
            </div>
            <button type="button"
                    data-media-preview-close
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-900"
                    aria-label="Close preview">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
        <div class="bg-slate-100 p-4">
            <img id="mediaPreviewImage" src="" alt="" class="mx-auto max-h-[70vh] rounded-2xl object-contain">
        </div>
    </div>
</div>

@include('media.partials.create')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchFocusButton = document.querySelector('[data-media-focus-search]');
        const searchInput = document.querySelector('[data-media-search]');

        if (searchFocusButton && searchInput) {
            searchFocusButton.addEventListener('click', function () {
                searchInput.focus();
            });
        }

        const previewModal = document.getElementById('mediaPreviewModal');
        const previewImage = document.getElementById('mediaPreviewImage');
        const previewTitle = document.getElementById('mediaPreviewTitle');
        const previewMeta = document.getElementById('mediaPreviewMeta');
        const closePreview = function () {
            previewModal.classList.add('hidden');
            previewModal.classList.remove('flex');
            previewImage.src = '';
        };

        document.querySelectorAll('[data-media-preview]').forEach(function (button) {
            button.addEventListener('click', function () {
                previewImage.src = button.dataset.previewUrl;
                previewImage.alt = button.dataset.previewTitle || 'Media preview';
                previewTitle.textContent = button.dataset.previewTitle || 'Preview';
                previewMeta.textContent = [button.dataset.previewName, button.dataset.previewSize].filter(Boolean).join(' - ');
                previewModal.classList.remove('hidden');
                previewModal.classList.add('flex');
            });
        });

        document.querySelectorAll('[data-media-preview-close]').forEach(function (button) {
            button.addEventListener('click', closePreview);
        });

        if (previewModal) {
            previewModal.addEventListener('click', function (event) {
                if (event.target === previewModal) {
                    closePreview();
                }
            });
        }

        const quickUploadForm = document.querySelector('[data-media-quick-upload]');
        const quickUploadInput = document.getElementById('quick-media-upload');

        if (quickUploadForm && quickUploadInput) {
            ['dragenter', 'dragover'].forEach(function (eventName) {
                quickUploadForm.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    quickUploadForm.classList.add('border-indigo-500', 'bg-indigo-100');
                });
            });

            ['dragleave', 'drop'].forEach(function (eventName) {
                quickUploadForm.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    quickUploadForm.classList.remove('border-indigo-500', 'bg-indigo-100');
                });
            });

            quickUploadForm.addEventListener('drop', function (event) {
                if (event.dataTransfer.files.length) {
                    quickUploadInput.files = event.dataTransfer.files;
                    quickUploadForm.submit();
                }
            });
        }
    });
</script>

@if(!empty($openCreateModal) || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal('uploadMediaModal');
    });
</script>
@endif
@endsection
