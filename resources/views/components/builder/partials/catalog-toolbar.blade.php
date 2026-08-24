@php
    $isDestinationCatalog = $catalogType === 'destinations';
    $filters = $catalog['filters'];
    $items = $catalog['items'];
    $publicUrls = app(\App\Services\PublicSiteUrl::class);
    $publicAgency = request()->attributes->get('publicAgency');
    $action = $publicAgency
        ? ($isDestinationCatalog ? $publicUrls->destinations($publicAgency) : $publicUrls->offers($publicAgency))
        : request()->url();
    $enabled = static fn (string $key): bool => !array_key_exists($key, $props)
        || !in_array($props[$key], ['no', 'false', false, 0, '0'], true);
    $controlClass = 'min-h-12 w-full border bg-white px-3 text-sm outline-none focus:border-[var(--site-primary)]';
    $sortOptions = $isDestinationCatalog
        ? [
            'featured' => 'Featured first',
            'latest' => 'Newest first',
            'name_asc' => 'Name A–Z',
            'name_desc' => 'Name Z–A',
        ]
        : [
            'special' => 'Special first',
            'latest' => 'Newest first',
            'price_asc' => 'Price low to high',
            'price_desc' => 'Price high to low',
            'duration_asc' => 'Shortest first',
            'duration_desc' => 'Longest first',
            'name_asc' => 'Name A–Z',
        ];
    $filterLabels = $isDestinationCatalog
        ? ['q' => 'Search', 'country' => 'Country', 'continent' => 'Continent', 'type' => 'Travel style', 'month' => 'Month', 'featured' => 'Featured']
        : ['q' => 'Search', 'destination' => 'Destination', 'min_price' => 'Minimum price', 'max_price' => 'Maximum price', 'duration' => 'Duration', 'continent' => 'Continent', 'type' => 'Travel style', 'month' => 'Month', 'special' => 'Special'];
    $activeFilters = collect($filterLabels)->map(function ($label, $key) use ($filters, $catalog, $action) {
        $value = $filters[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        $display = match ($key) {
            'continent' => $catalog['continents'][$value] ?? $value,
            'type' => $catalog['travelTypes'][$value] ?? $value,
            'month' => $catalog['months'][(int) $value] ?? $value,
            'destination' => optional($catalog['destinations']->firstWhere('slug', $value))->name ?? $value,
            'featured', 'special' => 'Yes',
            'duration' => "Up to {$value} days",
            default => $value,
        };
        $query = request()->query();
        unset($query[$key], $query['page']);

        return [
            'label' => "{$label}: {$display}",
            'url' => $action.($query ? '?'.http_build_query($query) : ''),
        ];
    })->filter()->values();
    $viewUrl = static function (string $view) use ($action): string {
        $query = request()->query();
        $query['view'] = $view;
        unset($query['page']);

        return $action.'?'.http_build_query($query);
    };
@endphp

<form method="get" action="{{ $isEditor ? '#' : $action }}" @if($isEditor) onsubmit="return false" @endif class="site-card mb-8 p-5 sm:p-6" role="search">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="site-eyebrow">REFINE YOUR SEARCH</div>
            <h3 class="site-heading mt-2 text-2xl">Find the right {{ $isDestinationCatalog ? 'place' : 'journey' }}</h3>
        </div>
        @if($activeFilters->isNotEmpty())
            <a href="{{ $isEditor ? '#' : $action }}" @if($isEditor) onclick="return false" @endif class="text-sm font-bold text-[var(--site-primary)]">Clear all</a>
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        @if($enabled('showSearchFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Search</span><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $isDestinationCatalog ? 'Place or experience' : 'Journey or experience' }}" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);"></label>
        @endif

        @if($isDestinationCatalog && $enabled('showCountryFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Country</span><select name="country" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);"><option value="">All countries</option>@foreach($catalog['countries'] as $country)<option value="{{ $country }}" @selected(($filters['country'] ?? '') === $country)>{{ $country }}</option>@endforeach</select></label>
        @endif

        @if(!$isDestinationCatalog && $enabled('showDestinationFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Destination</span><select name="destination" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);"><option value="">Anywhere</option>@foreach($catalog['destinations'] as $destination)<option value="{{ $destination->slug }}" @selected(($filters['destination'] ?? '') === $destination->slug)>{{ $destination->name }}</option>@endforeach</select></label>
        @endif

        @if($enabled('showContinentFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Continent</span><select name="continent" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);"><option value="">All continents</option>@foreach($catalog['continents'] as $value => $label)<option value="{{ $value }}" @selected(($filters['continent'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
        @endif

        @if($enabled('showTravelTypeFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Travel style</span><select name="type" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);"><option value="">Every style</option>@foreach($catalog['travelTypes'] as $value => $label)<option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
        @endif

        @if($enabled('showMonthFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Best month</span><select name="month" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);"><option value="">Any month</option>@foreach($catalog['months'] as $value => $label)<option value="{{ $value }}" @selected((string) ($filters['month'] ?? '') === (string) $value)>{{ $label }}</option>@endforeach</select></label>
        @endif

        @if(!$isDestinationCatalog && $enabled('showPriceFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Price range</span><span class="flex gap-2"><input type="number" min="0" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min" class="{{ $controlClass }} min-w-0" style="border-color: var(--site-border); border-radius: var(--site-radius);"><input type="number" min="0" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max" class="{{ $controlClass }} min-w-0" style="border-color: var(--site-border); border-radius: var(--site-radius);"></span></label>
        @endif

        @if(!$isDestinationCatalog && $enabled('showDurationFilter'))
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Maximum duration</span><select name="duration" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);"><option value="">Any length</option>@foreach([3, 7, 10, 14, 21] as $days)<option value="{{ $days }}" @selected((string) ($filters['duration'] ?? '') === (string) $days)>Up to {{ $days }} days</option>@endforeach</select></label>
        @endif

        @if(($isDestinationCatalog && $enabled('showFeaturedFilter')) || (!$isDestinationCatalog && $enabled('showSpecialFilter')))
            @php($flag = $isDestinationCatalog ? 'featured' : 'special')
            <label class="flex min-h-12 items-center gap-3 self-end"><input type="checkbox" name="{{ $flag }}" value="1" @checked(($filters[$flag] ?? '') === '1') class="h-4 w-4"><span class="font-semibold">{{ $isDestinationCatalog ? 'Featured only' : 'Special offers only' }}</span></label>
        @endif

        <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Sort</span><select name="sort" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);">@foreach($sortOptions as $value => $label)<option value="{{ $value }}" @selected($catalog['sort'] === $value)>{{ $label }}</option>@endforeach</select></label>
        <label><span class="mb-2 block text-xs font-bold uppercase tracking-wider">Show</span><select name="per_page" class="{{ $controlClass }}" style="border-color: var(--site-border); border-radius: var(--site-radius);">@foreach(\App\Services\PublicCatalogService::PER_PAGE as $size)<option value="{{ $size }}" @selected($catalog['perPage'] === $size)>{{ $size }} per page</option>@endforeach</select></label>
        <input type="hidden" name="view" value="{{ $catalog['view'] }}">
        <div class="flex items-end gap-3"><button type="submit" class="site-button flex-1" @disabled($isEditor)>Apply filters</button></div>
    </div>

    @if($activeFilters->isNotEmpty())
        <div class="mt-5 flex flex-wrap gap-2" aria-label="Active filters">
            @foreach($activeFilters as $filter)
                <a href="{{ $isEditor ? '#' : $filter['url'] }}" @if($isEditor) onclick="return false" @endif class="rounded-full border px-3 py-1.5 text-xs font-bold no-underline" style="border-color: var(--site-border); color: var(--site-secondary);">{{ $filter['label'] }} <span aria-hidden="true">×</span></a>
            @endforeach
        </div>
    @endif
</form>

<div class="site-catalog-toolbar mb-8">
    <div><div class="site-eyebrow">{{ $items->total() }} RESULTS</div><p class="site-copy mt-2 text-sm" style="color: var(--site-muted);">Showing {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} of {{ $items->total() }}</p></div>
    <div class="inline-flex rounded-md border bg-white p-1" role="group" aria-label="Results view" style="border-color: var(--site-border);">
        <a href="{{ $isEditor ? '#' : $viewUrl('grid') }}" @if($isEditor) onclick="return false" @endif aria-label="Grid view" @if($catalog['view'] === 'grid') aria-current="true" @endif class="rounded px-4 py-2 text-sm font-bold no-underline {{ $catalog['view'] === 'grid' ? 'bg-[var(--site-secondary)] text-white' : 'text-[var(--site-secondary)]' }}">Grid</a>
        <a href="{{ $isEditor ? '#' : $viewUrl('list') }}" @if($isEditor) onclick="return false" @endif aria-label="List view" @if($catalog['view'] === 'list') aria-current="true" @endif class="rounded px-4 py-2 text-sm font-bold no-underline {{ $catalog['view'] === 'list' ? 'bg-[var(--site-secondary)] text-white' : 'text-[var(--site-secondary)]' }}">List</a>
    </div>
</div>
