@php
    $props = is_array($props ?? null) ? $props : [];
    $items = collect(preg_split('/\r\n|\r|\n/', (string) ($props['items'] ?? '')))->filter()->map(function ($line) {
        [$label, $content] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        return compact('label', 'content');
    })->values();
    $tabsId = 'site-tabs-'.preg_replace('/[^A-Za-z0-9_-]/', '', (string) $nodeId);
@endphp
<section @if($isEditor) data-type="tabs" data-node-id="{{ $nodeId }}" draggable="true" data-drag-action="reorder" @endif class="site-section {{ $isEditor ? 'builder-node' : '' }}"><div class="site-container" data-site-tabs><h2 class="site-heading text-3xl sm:text-5xl">{{ $props['title'] ?? 'Explore your journey' }}</h2><div class="mt-8 flex gap-2 overflow-x-auto border-b border-slate-200" role="tablist" aria-label="{{ $props['title'] ?? 'Content tabs' }}">@foreach($items as $item)<button type="button" id="{{ $tabsId }}-tab-{{ $loop->index }}" class="whitespace-nowrap border-b-2 px-5 py-3 font-bold {{ $loop->first ? 'border-[var(--site-primary)] text-[var(--site-primary)]' : 'border-transparent text-slate-500' }}" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $tabsId }}-panel-{{ $loop->index }}" data-site-tab="{{ $loop->index }}">{{ $item['label'] }}</button>@endforeach</div>@foreach($items as $item)<div id="{{ $tabsId }}-panel-{{ $loop->index }}" class="site-copy py-7" role="tabpanel" aria-labelledby="{{ $tabsId }}-tab-{{ $loop->index }}" data-site-tab-panel="{{ $loop->index }}" @if(!$loop->first) hidden @endif>{{ $item['content'] }}</div>@endforeach</div></section>
