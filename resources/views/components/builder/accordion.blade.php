@php
    $props = is_array($props ?? null) ? $props : [];
    $items = collect(preg_split('/\r\n|\r|\n/', (string) ($props['items'] ?? '')))->filter()->map(function ($line) {
        [$question, $answer] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        return compact('question', 'answer');
    });
@endphp
<section @if($isEditor) data-type="accordion" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}"><div class="site-container max-w-4xl"><h2 class="site-heading text-3xl sm:text-5xl">{{ $props['title'] ?? 'Frequently asked questions' }}</h2><div class="mt-8 divide-y divide-slate-200 border-y border-slate-200">@foreach($items as $item)<details class="group py-5" @if($loop->first && ($props['openFirst'] ?? true)) open @endif><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-bold text-[var(--site-secondary)]">{{ $item['question'] }}<span aria-hidden="true" class="text-2xl transition group-open:rotate-45">+</span></summary><p class="site-copy mt-3 pr-10">{{ $item['answer'] }}</p></details>@endforeach</div></div></section>
