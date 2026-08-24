@extends('layouts.admin')

@section('topbar')
<x-layout.topbar>
    <x-slot name="left">
        <a href="{{ route('media.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-slate-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
            </svg>
            Back to media
        </a>
    </x-slot>
</x-layout.topbar>
@endsection

@section('content')
<div class="mx-auto max-w-5xl space-y-6 pb-6">
    <div class="overflow-hidden rounded-[28px] border border-white/70 bg-white shadow-sm">
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 px-6 py-7 text-white sm:px-8">
            <p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-200">Media details</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Edit image metadata</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                Keep accessibility, source and licensing details clear so images remain safe to reuse across destinations, offers and pages.
            </p>
        </div>

        <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(360px,0.9fr)]">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-100">
                <img src="{{ $media->url }}"
                     alt="{{ $media->alt_text ?? $media->title }}"
                     class="h-full max-h-[520px] w-full object-contain">
            </div>

            <form method="POST" action="{{ route('media.update', $media) }}" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5">
                @csrf
                @method('PUT')

                @include('media.partials.form', ['media' => $media, 'upload' => false])

                <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">
                    <div class="flex items-center justify-between gap-4">
                        <span class="font-bold text-slate-700">File</span>
                        <span class="truncate">{{ $media->original_name }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-4">
                        <span class="font-bold text-slate-700">Size</span>
                        <span>{{ $media->size >= 1048576 ? number_format($media->size / 1048576, 1).' MB' : number_format($media->size / 1024, 1).' KB' }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-4">
                        <span class="font-bold text-slate-700">Uploaded</span>
                        <span>{{ $media->created_at?->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('media.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-center text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
