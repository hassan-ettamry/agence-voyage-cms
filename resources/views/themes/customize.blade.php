@extends('layouts.admin')

@section('content')
@php
    $colorLabels = [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
        'accent' => 'Accent',
        'background' => 'Background',
        'surface' => 'Surface',
        'text' => 'Text',
        'muted' => 'Muted text',
        'border' => 'Border',
    ];
@endphp

<div class="space-y-5" data-theme-editor>
    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <header class="flex flex-wrap items-start justify-between gap-4 border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <div class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Global design</div>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Customize {{ $theme->name }}</h1>
            <p class="mt-2 text-sm text-slate-500">Changes apply to components that use theme defaults. Local component styles remain unchanged.</p>
        </div>
        <a href="{{ route('themes.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Theme presets</a>
    </header>

    <form method="POST" action="{{ route('themes.customize.update') }}" data-theme-form class="grid min-h-[680px] gap-5 xl:grid-cols-[340px_minmax(0,1fr)]">
        @csrf
        @method('PUT')

        <aside class="space-y-4 border border-slate-200 bg-white p-4 shadow-sm">
            @cannot('update', \App\Models\Theme::class)
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500">
                    Preview only. You do not have permission to save theme customizations.
                </div>
            @endcannot
            <section>
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-800">Colors</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($colorLabels as $key => $label)
                        <label class="block text-xs font-semibold text-slate-600">
                            <span class="mb-1.5 block">{{ $label }}</span>
                            <span class="flex h-10 items-center gap-2 rounded-md border border-slate-200 px-2">
                                <input type="color" name="{{ $key }}" value="{{ old($key, $effectiveVariables[$key]) }}" data-theme-token="{{ $key }}" class="h-7 w-7 cursor-pointer border-0 bg-transparent p-0">
                                <span data-color-value class="truncate font-mono text-[11px]">{{ old($key, $effectiveVariables[$key]) }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="border-t border-slate-100 pt-4">
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-800">Typography</h2>
                <div class="space-y-3">
                    @foreach(['bodyFont' => 'Body font', 'headingFont' => 'Heading font'] as $key => $label)
                        <label class="block text-xs font-semibold text-slate-600">
                            <span class="mb-1.5 block">{{ $label }}</span>
                            <select name="{{ $key }}" data-theme-token="{{ $key }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
                                @foreach($fontOptions as $name => $value)
                                    <option value="{{ $value }}" @selected(old($key, $effectiveVariables[$key]) === $value)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="border-t border-slate-100 pt-4">
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-800">Shape</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block text-xs font-semibold text-slate-600">
                        <span class="mb-1.5 block">Radius</span>
                        <select name="radius" data-theme-token="radius" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
                            @foreach($radiusOptions as $value)
                                <option value="{{ $value }}" @selected(old('radius', $effectiveVariables['radius']) === $value)>{{ $value }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-xs font-semibold text-slate-600">
                        <span class="mb-1.5 block">Shadow</span>
                        <select name="shadow" data-theme-token="shadow" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
                            @foreach($shadowOptions as $name => $value)
                                <option value="{{ $name }}" @selected(old('shadow', $effectiveVariables['shadow']) === $name)>{{ ucfirst($name) }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </section>

            @can('update', \App\Models\Theme::class)
                <div class="flex gap-2 border-t border-slate-100 pt-4">
                    <button type="submit" class="flex-1 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save changes</button>
                    <button type="submit"
                            form="reset-theme-form"
                            onclick="return confirm('Reset all agency customizations to the active preset?')"
                            class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        Reset
                    </button>
                </div>
            @endcan
        </aside>

        <section class="overflow-hidden border border-slate-200 bg-slate-100 p-5 shadow-sm">
            <div data-theme-preview class="site-shell mx-auto min-h-full max-w-5xl overflow-hidden border" style="{{ $previewThemeCss }} border-color: var(--site-border);">
                <nav class="flex items-center justify-between border-b px-6 py-4" style="border-color: var(--site-border); background: var(--site-surface);">
                    <strong style="font-family: var(--site-heading-font);">Voyage Studio</strong>
                    <div class="flex gap-5 text-sm" style="color: var(--site-muted);"><span>Destinations</span><span>Offers</span><span>Contact</span></div>
                </nav>
                <div class="px-8 py-14" style="background: var(--site-secondary); color: var(--site-background);">
                    <div class="max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-widest" style="color: var(--site-accent);">Discover more</span>
                        <h2 class="mt-3 text-4xl font-bold" style="font-family: var(--site-heading-font);">Travel made personal</h2>
                        <p class="mt-4 leading-7 opacity-80">A representative preview of global colors, typography, surfaces, radius and shadow.</p>
                        <button type="button" class="mt-6 px-5 py-3 text-sm font-bold text-white" style="background: var(--site-primary); border-radius: var(--site-radius);">Explore destinations</button>
                    </div>
                </div>
                <div class="p-8">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div><h3 class="text-2xl font-bold" style="font-family: var(--site-heading-font);">Featured journeys</h3><p class="mt-1 text-sm" style="color: var(--site-muted);">Cards inherit the agency theme when local styles are absent.</p></div>
                        <a href="#" onclick="return false" class="text-sm font-bold" style="color: var(--site-primary);">View all</a>
                    </div>
                    <div class="grid gap-5 md:grid-cols-3">
                        @foreach(['Coastal escape', 'City discovery', 'Mountain retreat'] as $index => $title)
                            <article class="overflow-hidden border p-5" style="background: var(--site-surface); border-color: var(--site-border); border-radius: var(--site-radius); box-shadow: var(--site-shadow);">
                                <div class="mb-5 aspect-video" style="background: {{ $index === 0 ? 'var(--site-accent)' : ($index === 1 ? 'var(--site-primary)' : 'var(--site-secondary)') }}; border-radius: calc(var(--site-radius) / 1.4); opacity: .78;"></div>
                                <h4 class="font-bold" style="font-family: var(--site-heading-font);">{{ $title }}</h4>
                                <p class="mt-2 text-sm leading-6" style="color: var(--site-muted);">Curated travel experiences for modern explorers.</p>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8 grid gap-5 border-t pt-8 md:grid-cols-[1.2fr_.8fr]" style="border-color: var(--site-border);">
                        <div>
                            <span class="site-eyebrow">Design system preview</span>
                            <h3 class="mt-3 text-3xl font-bold">Made for meaningful journeys</h3>
                            <p class="site-lead mt-3">Typography, spacing, surfaces, buttons and accessible focus states share one consistent visual language.</p>
                        </div>
                        <div class="site-card p-5">
                            <div class="text-sm font-bold">Plan your next escape</div>
                            <p class="mt-2 text-sm" style="color: var(--site-muted);">A reusable call-to-action card for every public page.</p>
                            <div class="mt-5 flex flex-wrap gap-3">
                                <button type="button" class="site-button">Start planning</button>
                                <button type="button" class="site-button site-button--secondary">Learn more</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>

    @can('update', \App\Models\Theme::class)
        <form id="reset-theme-form" method="POST" action="{{ route('themes.customize.reset') }}">
            @csrf
            @method('DELETE')
        </form>
    @endcan
</div>

<script>
(() => {
    const editor = document.querySelector('[data-theme-editor]');
    const form = editor?.querySelector('[data-theme-form]');
    const preview = editor?.querySelector('[data-theme-preview]');
    if (!form || !preview) return;

    const shadowMap = @json($shadowOptions);
    let dirty = false;

    const update = (input) => {
        const token = input.dataset.themeToken;
        let value = input.value;
        if (token === 'shadow') value = shadowMap[value] || 'none';
        preview.style.setProperty(`--site-${token.replace(/[A-Z]/g, value => `-${value.toLowerCase()}`)}`, value);
        input.closest('label')?.querySelector('[data-color-value]')?.replaceChildren(input.value);
        dirty = true;
    };

    form.querySelectorAll('[data-theme-token]').forEach(input => {
        input.addEventListener('input', () => update(input));
        input.addEventListener('change', () => update(input));
    });

    form.addEventListener('submit', () => { dirty = false; });
    document.getElementById('reset-theme-form')?.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', event => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
})();
</script>
@endsection
