@props([
    'filters' => [],
    'current' => null,
    'query' => 'filter',
    'default' => 'all',
])

@php
    $active = $current ?? request($query, $default);
@endphp

<div class="flex bg-slate-100 rounded-lg p-0.5">
    @foreach($filters as $key => $label)
        @php
            $isActive = $active === $key;
            $url = request()->fullUrlWithQuery([
                $query => $key,
                'page' => 1,
            ]);
        @endphp

        <a
            href="{{ $url }}"
            data-table-filter="{{ $key }}"
            class="filter-btn px-3 py-1.5 text-xs font-medium rounded-md
            {{ $isActive ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
