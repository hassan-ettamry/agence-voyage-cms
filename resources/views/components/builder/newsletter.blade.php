@php $props = is_array($props ?? null) ? $props : []; @endphp
<section @if($isEditor) data-type="newsletter" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}" style="background: var(--site-surface);">
    <div class="site-container grid items-center gap-8 lg:grid-cols-[1fr_.9fr]">
        <div><div class="site-eyebrow">TRAVEL INSPIRATION</div><h2 class="site-heading mt-3 text-3xl sm:text-5xl">{{ $props['title'] ?? 'Stories worth travelling for' }}</h2><p class="site-lead mt-4">{{ $props['text'] ?? 'Receive destination ideas and special journeys in your inbox.' }}</p></div>
        <form action="#" method="post" onsubmit="return false" class="flex flex-col gap-3 sm:flex-row" aria-label="Newsletter signup"><label for="newsletter-email-{{ $nodeId }}" class="sr-only">Email address</label><input id="newsletter-email-{{ $nodeId }}" type="email" autocomplete="email" placeholder="{{ $props['placeholder'] ?? 'Your email address' }}" class="min-h-12 flex-1 rounded-[var(--site-radius)] border border-slate-300 bg-white px-4 text-slate-950 outline-none focus:border-[var(--site-primary)]"><button type="submit" class="site-button">{{ $props['buttonText'] ?? 'Subscribe' }}</button></form>
    </div>
</section>
