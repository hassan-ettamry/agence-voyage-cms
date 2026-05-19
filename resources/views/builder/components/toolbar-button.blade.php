<button
        data-action="switch-tab"
        data-tab="{{ $tab }}"
        class="left-tab flex items-center gap-1 px-3 py-2.5 whitespace-nowrap border-b-2 transition-colors
               {{ $active ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800' }}"
>
    {!! $icon !!}
    {{ $label }}
</button>
