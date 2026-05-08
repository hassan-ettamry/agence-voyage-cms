<div
    onclick="BuilderComponents.add('{{ $type }}')"

    draggable="true"

    ondragstart="BuilderDragDrop.start(event, '{{ $type }}')"

    class="group
           flex flex-col items-center justify-center
           gap-1.5
           p-3
           rounded-lg
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

        <x-dynamic-component
            :component="'heroicon-o-' . $icon"
        />

    </div>

    {{-- LABEL --}}
    <span class="text-[11px]
                 text-center
                 text-gray-600
                 group-hover:text-blue-700
                 leading-tight
                 font-medium">

        {{ $label }}

    </span>

</div>