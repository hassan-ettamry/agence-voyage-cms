@props(['paginator'])

@php
    $perPageOptions = [8, 16, 32, 64];
    $currentPerPage = (int) request('per_page', 8);
@endphp

<div class="flex items-center justify-between w-full">

    {{-- LEFT: INFO --}}
    <span class="text-xs text-gray-500">
        Showing
        <span class="font-semibold text-gray-700">
            {{ $paginator->firstItem() ?? 0 }}&ndash;{{ $paginator->lastItem() ?? 0 }}
        </span>
        of
        <span class="font-semibold text-gray-700">
            {{ $paginator->total() }}
        </span>
        results
    </span>

    {{-- RIGHT --}}
    <div class="flex items-center gap-2">

        {{-- Rows per page --}}
        <div class="flex items-center gap-1 text-xs text-gray-500">
            <span>Rows:</span>
            <select
                class="border border-gray-200 rounded-md px-2 py-1 text-xs bg-white"
                onchange="
                    const url = new URL(window.location.href);
                    url.searchParams.set('per_page', this.value);
                    url.searchParams.set('page', '1');
                    window.location.href = url.toString();
                ">

                @foreach($perPageOptions as $size)
                    <option value="{{ $size }}"
                        {{ $currentPerPage === $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="h-4 w-px bg-gray-200"></div>

        {{-- Pagination --}}
        <div class="flex items-center gap-1">

            {{-- Prev --}}
            @if($paginator->onFirstPage())
                <span class="w-7 h-7 flex items-center justify-center text-gray-300">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md hover:bg-gray-100">
                    &lsaquo;
                </a>
            @endif

            {{-- Numbers --}}
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)

                @if($page == $paginator->currentPage())
                    <span class="w-7 h-7 flex items-center justify-center bg-indigo-600 text-white text-xs rounded-md font-semibold">
                        {{ $page }}
                    </span>

                @elseif($page <= 3 || $page == $paginator->lastPage() || abs($page - $paginator->currentPage()) <= 1)
                    <a href="{{ $url }}"
                       class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md text-xs hover:bg-gray-100">
                        {{ $page }}
                    </a>

                @elseif($page == 4)
                    <span class="w-7 h-7 flex items-center justify-center text-gray-400 text-xs">&hellip;</span>
                @endif

            @endforeach

            {{-- Next --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md hover:bg-gray-100">
                    &rsaquo;
                </a>
            @else
                <span class="w-7 h-7 flex items-center justify-center text-gray-300">&rsaquo;</span>
            @endif

        </div>

    </div>

</div>
