@php
    $items = collect(preg_split('/\r\n|\r|\n/', (string) ($props['items'] ?? '')))->map(function ($line) {
        [$name, $image] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        return $name === '' ? null : compact('name', 'image');
    })->filter();
@endphp

<section @if($isEditor) data-type="trust-logos" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="border-y py-10 {{ $isEditor ? 'builder-node' : '' }}" style="border-color: var(--site-border); background: var(--site-surface);">
    <div class="site-container text-center">
        <p class="text-xs font-bold uppercase tracking-[.16em]" style="color: var(--site-muted);">{{ $props['title'] ?? 'Trusted by travellers and partners' }}</p>
        <div class="mt-7 flex flex-wrap items-center justify-center gap-x-10 gap-y-6 opacity-70 grayscale">
            @foreach($items as $item)
                @if($item['image'])<img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy" class="h-9 w-auto max-w-36 object-contain">@else<span class="site-heading text-xl font-bold">{{ $item['name'] }}</span>@endif
            @endforeach
        </div>
    </div>
</section>
