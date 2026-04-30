@props(['paginator'])

<div class="flex items-center justify-between w-full">

    {{-- LEFT: INFO --}}
    <span class="text-xs text-gray-500">
        Showing
        <span class="font-semibold text-gray-700">
            {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}
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
                onchange="window.location.href='?per_page='+this.value+'&page=1'">

                @foreach([10,25,50] as $size)
                    <option value="{{ $size }}"
                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>
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
                <span class="w-7 h-7 flex items-center justify-center text-gray-300">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md hover:bg-gray-100">
                    ‹
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
                    <span class="w-7 h-7 flex items-center justify-center text-gray-400 text-xs">…</span>
                @endif

            @endforeach

            {{-- Next --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded-md hover:bg-gray-100">
                    ›
                </a>
            @else
                <span class="w-7 h-7 flex items-center justify-center text-gray-300">›</span>
            @endif

        </div>

    </div>

</div>