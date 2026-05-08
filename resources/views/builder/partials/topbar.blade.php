<div class="h-14 bg-white border-b border-gray-200 text-gray-800 flex items-center px-4 gap-4 z-50 shadow-sm" style="min-width:0;">

    {{-- Logo / Brand --}}
    <div class="flex items-center gap-2 w-56 shrink-0">
        <button class="text-gray-400 hover:text-gray-600 transition-colors mr-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div>
            <div class="font-bold text-sm leading-tight text-gray-900">Site Builder</div>
            <div class="text-[10px] text-gray-400 leading-tight">Vision</div>
        </div>
    </div>

    {{-- Page Selector --}}
    <div class="flex items-center gap-2">
        <span class="text-xs text-gray-500 whitespace-nowrap">Page Selected:</span>
        <button class="flex items-center gap-1.5 border border-gray-200 rounded px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-gray-300 bg-white shadow-sm transition-colors">
            {{ $page->title ?? 'My test page' }}
            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    {{-- Viewport Switcher --}}
    @php
    $viewports = [
        [
            'id' => 'viewport-desktop',
            'onclick' => "BuilderViewport.set('desktop')",
            'icon' => '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
            'label' => 'Desktop',
            'title' => 'Desktop',
            'active' => true,
        ],
        [
            'id' => 'viewport-tab',
            'onclick' => "BuilderViewport.set('tab')",
            'icon' => '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2" stroke-width="1.5" stroke="currentColor" fill="none"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2" stroke-linecap="round"/></svg>',
            'label' => 'Tab',
            'title' => 'Tab',
            'active' => false,
        ],
        [
            'id' => 'viewport-mobile',
            'onclick' => "BuilderViewport.set('mobile')",
            'icon' => '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="7" y="2" width="10" height="20" rx="2" ry="2" stroke-width="1.5" stroke="currentColor" fill="none"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2" stroke-linecap="round"/></svg>',
            'label' => 'Mobile',
            'title' => 'Mobile',
            'active' => false,
        ],
        [
            'id' => 'viewport-fullscreen',
            'onclick' => "BuilderViewport.set('fullscreen')",
            'icon' => '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>',
            'label' => 'Fullscreen',
            'title' => 'Fullscreen',
            'active' => false,
        ],
    ];
    @endphp

    <div class="flex items-center border border-gray-200 rounded overflow-hidden ml-auto shadow-sm">
        @foreach($viewports as $vp)
            @include('builder.components.viewport-button', array_merge($vp, ['borderRight' => !$loop->last]))
        @endforeach
    </div>

    {{-- Right Actions --}}
    <div class="flex items-center gap-2 ml-4 shrink-0">
        <button class="flex items-center gap-1.5 border border-gray-200 rounded px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-gray-300 bg-white shadow-sm transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            Save as template
        </button>
        <button onclick="BuilderStorage.save()" class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded px-4 py-1.5 text-xs font-semibold shadow-sm transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            Save
        </button>
        <button class="flex items-center gap-1.5 border border-blue-600 text-blue-600 hover:bg-blue-50 rounded px-4 py-1.5 text-xs font-semibold transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Preview
        </button>
    </div>
</div>