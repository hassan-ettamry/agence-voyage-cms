@extends('layouts.admin')

@section('title', 'Edit Page')

@section('topbar')

<x-layout.topbar>

    <x-slot name="left">
        <a href="{{ url('/') }}" target="_blank" rel="noopener">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="11" cy="12" r="7"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h14M11 5c2 2 3 4.3 3 7s-1 5-3 7M11 5c-2 2-3 4.3-3 7s1 5 3 7M16 5h3v3M19 5l-5 5"/>
            </svg>
            Visit site
        </a>

        @if($page->isPublished())
            <a href="{{ app(\App\Services\PublicSiteUrl::class)->page($page->agency, $page) }}"
               target="_blank"
               rel="noopener">
                View live
            </a>
        @endif
    </x-slot>

</x-layout.topbar>

@endsection

@section('content')

@php
    $metaTitle = old('meta_title', data_get($page->meta, 'title', ''));
    $metaDescription = old('meta_description', data_get($page->meta, 'description', ''));
    $status = old('status', $page->status);
@endphp

<div class="mx-auto max-w-7xl space-y-5">

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please check the highlighted fields.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-end">
        <div class="flex items-center gap-2">
            <a href="{{ route('pages.index') }}"
               class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    form="pageSettingsForm"
                    class="inline-flex items-center justify-center rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                Save
            </button>
        </div>
    </div>

    <form id="pageSettingsForm"
          method="POST"
          action="{{ route('pages.update', $page) }}"
          class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_380px]">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-950">Page information</h2>
                </div>

                <div class="space-y-6 px-5 py-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-400">Basic info</p>
                        <p class="mt-2 text-sm text-gray-500">Name, address, and publishing state for this page.</p>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
                        <div>
                            <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700">Page title</label>
                            <input id="title"
                                   name="title"
                                   type="text"
                                   value="{{ old('title', $page->title) }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-950 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            @error('title')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">Status</label>
                            <select id="status"
                                    name="status"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-950 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <option value="draft" @selected($status === 'draft')>Draft</option>
                                <option value="published" @selected($status === 'published')>Published</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700">Page address</label>
                        <div class="flex rounded-lg border border-gray-300 bg-white shadow-sm transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
                            <span class="inline-flex items-center border-r border-gray-200 px-3 text-sm text-gray-400">/</span>
                            <input id="slug"
                                   name="slug"
                                   type="text"
                                   value="{{ old('slug', $page->slug) }}"
                                   class="min-w-0 flex-1 rounded-r-lg border-0 bg-transparent px-3 py-2.5 text-sm text-gray-950 outline-none focus:ring-0">
                        </div>
                        @error('slug')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-950">Page content</h2>
                </div>

                <div class="grid gap-5 px-5 py-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                    <div>
                        <p class="text-sm font-semibold text-gray-950">Edit the page visitors will see.</p>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                            Open the page editor to update text, images, sections, and layout in one focused workspace.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Text</span>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Images</span>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Layout</span>
                        </div>
                    </div>

                    <a href="{{ route('pages.builder', $page) }}"
                       class="inline-flex items-center justify-center rounded-full bg-gray-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800">
                        Edit page content
                    </a>
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-950">Navigation</h2>
                </div>

                <div class="space-y-4 px-5 py-5">
                    <div>
                        <label for="menu_selection" class="mb-1.5 block text-sm font-medium text-gray-700">Menu</label>
                        <select id="menu_selection"
                                name="menu_selection"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-950 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="none" @selected(old('menu_selection', $menuSelection ?? 'none') === 'none')>No menu</option>
                            <option value="default" @selected(old('menu_selection') === 'default')>Default menu</option>
                            @foreach($menus ?? [] as $menu)
                                <option value="{{ $menu['id'] }}" @selected(old('menu_selection', $menuSelection ?? 'none') === $menu['id'])>
                                    {{ $menu['name'] }}{{ $menu['is_default'] ? ' (default)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs leading-5 text-gray-400">
                            Choose where this page appears in site navigation.
                        </p>
                        @error('menu_selection')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-950">Marketing & SEO</h2>
                </div>

                <div class="space-y-4 px-5 py-5">
                    <div>
                        <label for="meta_title" class="mb-1.5 block text-sm font-medium text-gray-700">SEO title</label>
                        <input id="meta_title"
                               name="meta_title"
                               type="text"
                               maxlength="60"
                               value="{{ $metaTitle }}"
                               placeholder="Search result title"
                               class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-950 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        @error('meta_title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="mb-1.5 block text-sm font-medium text-gray-700">SEO description</label>
                        <textarea id="meta_description"
                                  name="meta_description"
                                  rows="4"
                                  maxlength="160"
                                  placeholder="Short summary for search engines"
                                  class="w-full resize-none rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-950 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ $metaDescription }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Recommended length: 140-160 characters.</p>
                        @error('meta_description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

        </aside>
    </form>
</div>

@endsection
