<div id="right-panel" class="w-72 bg-white border-l border-gray-200 flex flex-col shrink-0">

    {{-- Header: Layers --}}
    @component('builder.components.panel-title')
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Layers</h3>
        </div>
    @endcomponent

    {{-- Layers Tree --}}
    <div class="flex-1 overflow-y-auto" id="layers-panel">
        <div id="layers-tree" class="py-1 text-xs text-gray-700 select-none" style="font-size: 11px; line-height: 1.4;">
            {{-- Dynamically populated by JS --}}
        </div>
        <div id="layers-empty" class="text-center text-gray-400 text-xs py-10 hidden">No elements on canvas</div>
    </div>

    {{-- Bottom: Hide Layers button --}}
    <div class="border-t border-gray-200 p-2">
        <button onclick="BuilderRightSidebar.toggle()" class="w-full flex items-center justify-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 py-1.5 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
            Hide Layers
        </button>
    </div>
</div>