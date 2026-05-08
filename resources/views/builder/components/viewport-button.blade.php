<button id="{{ $id }}"
        onclick="{{ $onclick }}"
        class="viewport-btn flex flex-col items-center justify-center px-4 py-2 gap-0.5
               {{ $active ? 'bg-gray-50 border-b-2 border-b-blue-600' : 'bg-white hover:bg-gray-50' }}
               {{ $borderRight ? 'border-r border-gray-200' : '' }}
               transition-colors"
        title="{{ $title }}">
    {!! $icon !!}
    <span class="text-[10px] {{ $active ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
        {{ $label }}
    </span>
</button>