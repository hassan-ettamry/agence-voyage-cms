<div
    id="left-panel"
    class="bg-white border-r border-gray-200 flex flex-col shrink-0 overflow-hidden"
    style="width: 260px; min-width: 260px; max-width: 260px;"
>

    {{-- Panel Title --}}
    <div class="flex h-[56px] items-center justify-center border-b border-gray-200 bg-white px-4">
        <span
            id="builder-panel-title"
            class="text-sm font-semibold text-slate-800"
            data-page-title="{{ $page->title ?? 'My test page' }}"
        >
            {{ $page->title ?? 'My test page' }}
        </span>
    </div>

    {{-- Tabs --}}
    <div class="grid h-[39px] grid-cols-3 border-b border-gray-200 bg-white text-[13px] font-medium shrink-0">
        @php
            $icons = require base_path('resources/views/builder/data/icons.php');
            $tabs = [
                ['tab' => 'page',     'label' => 'Page',     'active' => false],
                ['tab' => 'controls', 'label' => 'Controls', 'active' => false],
                ['tab' => 'widgets',  'label' => 'Widgets',  'active' => true],
            ];
        @endphp

        @foreach($tabs as $tab)
            @include('builder.components.toolbar-button', [
                'tab'     => $tab['tab'],
                'icon'    => $icons[$tab['tab']],
                'label'   => $tab['label'],
                'active'  => $tab['active'],
            ])
        @endforeach
    </div>

    {{-- Widgets Tab --}}
    <div id="tab-widgets" class="left-tab-content flex-1 overflow-y-auto px-3 py-3">

        <div
            class="grid justify-center"
            style="grid-template-columns: repeat(2, 114px); gap: 8px;"
        >

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
    <div
        id="tab-controls"
        class="
            left-tab-content
            hidden
            flex-1
            overflow-y-auto
            bg-gray-50
            builder-left-scroll
        "
    >

        <div id="settings-panel">

            <div class="p-4 text-sm text-gray-400">

                Select an element to edit settings

            </div>

        </div>

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
        <button
            data-action="toggle-left-sidebar"
            class="w-full flex items-center justify-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 py-1.5 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            Hide Sidebar
        </button>
    </div>
</div>
