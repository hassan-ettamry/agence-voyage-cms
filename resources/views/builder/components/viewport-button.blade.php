<button id="{{ $id }}"
        data-action="set-viewport"
        data-viewport="{{ $viewport }}"
        class="viewport-btn flex flex-col items-center justify-center px-4 py-2 gap-0.5 border-b-2
               {{ $active ? 'active bg-blue-50 text-blue-600 border-b-blue-600' : 'bg-white text-gray-500 border-b-transparent hover:bg-gray-50 hover:text-gray-700' }}
               {{ $borderRight ? 'border-r border-gray-200' : '' }}
               transition-colors"
        title="{{ $title }}">
    {!! $icon !!}
    <span class="text-[10px] font-medium">
        {{ $label }}
    </span>
</button>
