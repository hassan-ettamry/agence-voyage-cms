@php
    $props = is_array($props ?? null) ? $props : [];
    $items = collect(preg_split('/\r\n|\r|\n/', (string) ($props['items'] ?? '')))->filter()->map(function ($line) {
        [$value, $label] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        return compact('value', 'label');
    });
@endphp
<section @if($isEditor) data-type="stats-counter" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}" style="background: {{ ($props['variant'] ?? 'dark') === 'light' ? 'var(--site-surface)' : 'var(--site-secondary)' }}; color: {{ ($props['variant'] ?? 'dark') === 'light' ? 'var(--site-text)' : '#fff' }};">
    <div class="site-container grid gap-8 text-center sm:grid-cols-2 lg:grid-cols-4">
        @foreach($items as $item)
            <div><div class="text-4xl font-bold tracking-tight sm:text-5xl" style="color: var(--site-accent);">{{ $item['value'] }}</div><div class="mt-2 text-sm font-semibold uppercase tracking-[0.14em] opacity-80">{{ $item['label'] }}</div></div>
        @endforeach
    </div>
</section>
