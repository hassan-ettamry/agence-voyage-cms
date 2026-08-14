@php
    $targetDate = trim((string) ($props['targetDate'] ?? ''));
    $safeTarget = preg_match('/^\d{4}-\d{2}-\d{2}(?:T\d{2}:\d{2}(?::\d{2})?)?$/', $targetDate) === 1
        ? $targetDate
        : '';
@endphp

<div
    @if($isEditor)
        data-type="countdown"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} p-4 transition-all duration-150"
>
    <div
        data-countdown-widget
        data-countdown-target="{{ $safeTarget }}"
        data-countdown-expired="{{ e($props['expiredText'] ?? 'Offer expired') }}"
        class="p-6 text-center sm:p-8"
        style="
            background-color: {{ $props['backgroundColor'] ?? 'var(--site-secondary, #111827)' }};
            color: {{ $props['textColor'] ?? 'var(--site-background, #ffffff)' }};
            border-radius: var(--site-radius, 14px);
            box-shadow: var(--site-shadow, none);
        "
    >
        <p
            @if($isEditor)
                contenteditable="true"
                data-field="label"
            @endif
            class="{{ $isEditor ? 'outline-none' : '' }} mb-4 text-sm font-semibold uppercase tracking-wide"
            style="color: {{ $props['accentColor'] ?? 'var(--site-accent, #38bdf8)' }};"
        >
            {{ $props['label'] ?? 'Offer ends in' }}
        </p>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach(['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes', 'seconds' => 'Seconds'] as $unit => $label)
                <div class="rounded-lg bg-white/10 p-3">
                    <div
                        data-countdown-unit="{{ $unit }}"
                        class="text-3xl font-bold"
                    >
                        00
                    </div>
                    <div class="mt-1 text-xs uppercase tracking-wide opacity-75">
                        {{ $label }}
                    </div>
                </div>
            @endforeach
        </div>

        <p data-countdown-message class="mt-4 hidden text-sm font-semibold"></p>
    </div>
</div>
