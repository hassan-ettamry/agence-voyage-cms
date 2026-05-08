<div id="left-panel" class="w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 overflow-hidden">

    {{-- Panel Title --}}
    @component('builder.components.panel-title')
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-widest">
            {{ $page->title ?? 'My test page' }}
        </span>
    @endcomponent

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 text-xs font-medium overflow-x-auto shrink-0">
        @php
            $icons = require base_path('resources/views/builder/data/icons.php');
            $tabs = [
                ['onclick' => "BuilderSidebar.switchTab('page')",     'tab' => 'page',     'label' => 'Page',     'active' => false],
                ['onclick' => "BuilderSidebar.switchTab('controls')", 'tab' => 'controls', 'label' => 'Controls', 'active' => false],
                ['onclick' => "BuilderSidebar.switchTab('widgets')",  'tab' => 'widgets',  'label' => 'Widgets',  'active' => true],
            ];
        @endphp

        @foreach($tabs as $tab)
            @include('builder.components.toolbar-button', [
                'onclick' => $tab['onclick'],
                'tab'     => $tab['tab'],
                'icon'    => $icons[$tab['tab']],
                'label'   => $tab['label'],
                'active'  => $tab['active'],
            ])
        @endforeach
    </div>

    {{-- Widgets Tab --}}
    <div id="tab-widgets" class="left-tab-content flex-1 overflow-y-auto p-3">

        <div class="grid grid-cols-2 gap-2">

            @foreach($widgets as $w)

                @include('builder.components.widget-card', [

                    'type'  => $w->type,
                    'label' => $w->name,
                    'icon'  => $w->icon

                ])

            @endforeach

        </div>

    </div>

    {{-- Controls Tab --}}
    <div id="tab-controls" class="left-tab-content hidden flex-1 overflow-y-auto p-4">
        <p class="text-xs text-gray-400">Select an element to see its controls.</p>
    </div>

    {{-- Page Tab --}}
    <div id="tab-page" class="left-tab-content hidden flex-1 overflow-y-auto p-4 space-y-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Page Title</label>
            <input type="text" value="{{ $page->title ?? 'My test page' }}" class="w-full border border-gray-200 rounded px-2.5 py-1.5 text-xs focus:outline-none focus:border-blue-400"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Slug</label>
            <input type="text" value="{{ $page->slug ?? 'my-test-page' }}" class="w-full border border-gray-200 rounded px-2.5 py-1.5 text-xs focus:outline-none focus:border-blue-400"/>
        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-200 p-2">
        <button onclick="BuilderSidebar.toggle()" class="w-full flex items-center justify-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 py-1.5 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            Hide Sidebar
        </button>
    </div>
</div>