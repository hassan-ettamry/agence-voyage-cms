<div
    id="left-panel"
    class="bg-white border-r border-gray-200 flex flex-col shrink-0 overflow-hidden"
    style="width: 288px; min-width: 288px; max-width: 288px;"
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

        <div class="sticky top-0 z-10 -mx-1 mb-3 space-y-2 bg-white px-1 pb-3">
            <label class="relative block">
                <span class="sr-only">Search components</span>
                <svg class="pointer-events-none absolute left-3 top-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3-3"></path></svg>
                <input type="search" data-component-search placeholder="Search components..." class="min-h-10 w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs outline-none focus:border-blue-400 focus:bg-white">
            </label>
            <label class="block"><span class="sr-only">Component category</span><select data-component-category-filter class="min-h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700"><option value="all">All categories</option>@foreach($widgets->pluck('category')->filter()->unique()->sort()->values() as $category)<option value="{{ strtolower($category) }}">{{ \Illuminate\Support\Str::headline($category) }}</option>@endforeach</select></label>
            <div class="flex items-center justify-between text-[11px] text-slate-400"><span data-component-result-count>{{ $widgets->count() }} components</span><span>Press / to search</span></div>
        </div>

        <div
            class="grid grid-cols-2 gap-2"
            data-component-grid
        >

            @foreach($widgets as $w)

                @include('builder.components.widget-card', [

                    'type'  => $w->type,
                    'label' => $w->name,
                    'icon'  => $w->icon,
                    'category' => $w->category

                ])

            @endforeach

        </div>

        <div data-component-empty class="hidden rounded-lg border border-dashed border-slate-300 p-6 text-center text-xs leading-5 text-slate-500">No components match your search.<br>Try another word or category.</div>

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
