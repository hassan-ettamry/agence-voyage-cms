@php
    $items = collect(preg_split('/\r\n|\r|\n/', (string) ($props['items'] ?? '')))
        ->map(function ($line) {
            $parts = array_map('trim', explode('|', $line, 2));

            if (($parts[0] ?? '') === '') {
                return null;
            }

            return [
                'question' => $parts[0],
                'answer' => $parts[1] ?? '',
            ];
        })
        ->filter()
        ->values();

    if ($items->isEmpty()) {
        $items = collect([
            ['question' => 'What is included?', 'answer' => 'Add an answer in the FAQ settings.'],
        ]);
    }

    $exclusiveName = 'faq-'.$nodeId;
    $allowMultiple = ($props['allowMultiple'] ?? 'yes') === 'yes';
@endphp

<div
    @if($isEditor)
        data-type="faq"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} p-4 transition-all duration-150"
>
    <div class="mx-auto max-w-3xl">
        <h3
            @if($isEditor)
                contenteditable="true"
                data-field="title"
            @endif
            class="{{ $isEditor ? 'outline-none' : '' }} mb-5 text-2xl font-bold"
            style="color: var(--site-text, #0f172a);"
        >
            {{ $props['title'] ?? 'Frequently asked questions' }}
        </h3>

        <div class="divide-y border" style="background-color: var(--site-surface, #ffffff); border-color: var(--site-border, #e2e8f0); border-radius: var(--site-radius, 14px);">
            @foreach($items as $index => $item)
                <details
                    class="group p-4"
                    @if(! $allowMultiple)
                        name="{{ $exclusiveName }}"
                    @endif
                    @if($index === 0) open @endif
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold" style="color: var(--site-text, #0f172a);">
                        <span>{{ $item['question'] }}</span>
                        <span class="text-xl leading-none transition-transform group-open:rotate-45" style="color: var(--site-muted, #94a3b8);">+</span>
                    </summary>

                    <p class="mt-3 text-sm leading-6" style="color: var(--site-muted, #475569);">
                        {{ $item['answer'] }}
                    </p>
                </details>
            @endforeach
        </div>
    </div>
</div>
