<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">

        <div class="flex items-center gap-2">
            {{ $title }}
        </div>

        @isset($actions)
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endisset

    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        {{ $table }}
    </div>

    {{-- Footer --}}
    @isset($footer)
        <div class="flex items-center justify-between px-6 py-3.5 border-t border-gray-100 bg-gray-50/70">
            {{ $footer }}
        </div>
    @endisset

</div>