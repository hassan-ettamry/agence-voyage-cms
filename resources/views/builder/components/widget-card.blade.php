<div
    data-action="add-component"

    data-component="{{ $type }}"
    data-component-label="{{ strtolower($label) }}"
    data-component-category="{{ strtolower($category ?? 'other') }}"

    draggable="true"

    data-drag-action="component"

    class="group
           flex flex-col items-center justify-center
           gap-1
           h-[80px]
           w-full
           p-2
           rounded-md
           border border-gray-200
           cursor-pointer
           transition-all
           hover:border-blue-400
           hover:bg-blue-50
           hover:shadow-sm
           select-none"
>

    {{-- ICON --}}
    <div class="w-6 h-6 text-gray-500 group-hover:text-blue-600 transition-colors">

        @php
            $icon = is_string($icon ?? null) && preg_match('/^[a-z0-9-]+$/', $icon)
                ? $icon
                : 'square-3-stack-3d';
        @endphp

        <x-dynamic-component :component="'heroicon-o-' . $icon" />

    </div>

    {{-- LABEL --}}
    <span class="text-[12px]
                 text-center
                 text-gray-600
                 group-hover:text-blue-700
                 leading-tight
                 font-medium">

        {{ $label }}

    </span>

</div>
