@php
    $columns = max(1, min(4, (int) ($props['columns'] ?? 3)));
    $rawItems = $props['items'] ?? [];
    $items = is_array($rawItems)
        ? collect($rawItems)->map(fn ($item) => is_array($item) && trim((string) ($item['title'] ?? '')) !== '' ? [
            'icon' => trim((string) ($item['icon'] ?? '')),
            'title' => trim((string) $item['title']),
            'text' => trim((string) ($item['text'] ?? '')),
        ] : null)->filter()
        : collect(preg_split('/\r\n|\r|\n/', (string) $rawItems))->map(function ($line) {
            [$icon, $title, $text] = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
            return $title === '' ? null : compact('icon', 'title', 'text');
        })->filter();
@endphp

<section @if($isEditor) data-type="feature-grid" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}">
    <div class="site-container">
        <div class="max-w-3xl">
            <div class="site-eyebrow">{{ $props['eyebrow'] ?? 'WHY TRAVEL WITH US' }}</div>
            <h2 class="site-heading mt-4 text-[clamp(2.25rem,5vw,4rem)] font-bold">{{ $props['title'] ?? 'Every detail, thoughtfully handled' }}</h2>
        </div>
        <div class="site-card-grid mt-10 grid gap-5" style="--site-card-columns: {{ $columns }};">
            @forelse($items as $item)
                <article class="site-card p-6 sm:p-8">
                    <span class="grid h-12 w-12 place-items-center rounded-full" style="background: color-mix(in srgb, var(--site-primary) 12%, transparent); color: var(--site-primary);">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">@include('components.builder.partials.icon-svg', ['name' => $item['icon'] ?: 'sparkles'])</svg>
                    </span>
                    <h3 class="site-heading mt-6 text-xl font-bold">{{ $item['title'] }}</h3>
                    <p class="mt-3 leading-7" style="color: var(--site-muted);">{{ $item['text'] }}</p>
                </article>
            @empty
                @if($isEditor)<div class="site-card border-dashed p-8 text-center">Add feature items.</div>@endif
            @endforelse
        </div>
    </div>
</section>
