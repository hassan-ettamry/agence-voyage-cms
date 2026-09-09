@php
    $statsCount = count($stats);
    $desktopColumns = $statsCount === 3 ? 'xl:grid-cols-3' : 'xl:grid-cols-4';
@endphp

<div class="mb-6 grid grid-cols-1 gap-[18px] sm:grid-cols-2 {{ $desktopColumns }}">
    @foreach($stats as $stat)
        <x-ui.stat-card
            :label="$stat['label']"
            :value="$stat['value']"
            :note="$stat['note'] ?? null"
            :color="$stat['color'] ?? 'text-gray-400'"
            :tone="$stat['tone'] ?? null"
            :icon="$stat['icon'] ?? null"
        />
    @endforeach
</div>
