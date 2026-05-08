<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($stats as $stat)
        <x-ui.stat-card
            :label="$stat['label']"
            :value="$stat['value']"
            :note="$stat['note'] ?? null"
            :color="$stat['color'] ?? 'text-gray-400'"
        />
    @endforeach
</div>