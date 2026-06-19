<button
        data-action="switch-tab"
        data-tab="{{ $tab }}"
        class="left-tab flex h-full items-center justify-center gap-1.5 whitespace-nowrap border-b-2 border-r border-gray-100 transition-colors
               {{ $active ? 'border-b-slate-900 bg-[#eff6ff] text-slate-800 font-medium' : 'border-b-transparent bg-white text-slate-800 hover:bg-slate-50' }}"
>
    {!! $icon !!}
    {{ $label }}
</button>
