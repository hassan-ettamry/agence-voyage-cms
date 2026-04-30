@php
$menu = config('menu');
@endphp

<aside class="w-64 h-screen bg-[#1a2744] text-white flex flex-col">

    <!-- LOGO -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="w-8 h-8 flex items-center justify-center">
            <svg viewBox="0 0 32 32" fill="none" class="w-8 h-8">
                <path d="M4 8L14 4L14 14L4 18Z" fill="#4f8ef7"/>
                <path d="M14 4L28 8L20 14L14 14Z" fill="#6ea8fe"/>
                <path d="M4 18L14 14L20 20L10 24Z" fill="#3b6fd4"/>
            </svg>
        </div>
        <span class="text-lg font-bold tracking-tight">Website Builder</span>
    </div>

    <!-- MENU -->
    <nav class="flex-1 px-3 py-4 space-y-1 text-[13.5px] overflow-y-auto">

        @foreach($menu as $item)

            @php
                $isActive = isset($item['active']) && request()->routeIs($item['active']);
            @endphp

            {{-- SIMPLE LINK --}}
            @if(!isset($item['children']))
                <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                   {{ request()->routeIs($item['route'])
                        ? 'bg-white/15 text-white font-medium'
                        : 'text-white/70 hover:bg-white/10 hover:text-white' }}">

                    <x-layout.icon :name="$item['icon']" />
                    {{ $item['label'] }}
                </a>
            @endif


            {{-- DROPDOWN --}}
            @if(isset($item['children']))
                <div>
                    <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg
                        {{ $isActive ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">

                        <div class="flex items-center gap-3">
                            <x-layout.icon :name="$item['icon']" />
                            <span class="{{ $isActive ? 'font-semibold' : '' }}">
                                {{ $item['label'] }}
                            </span>
                        </div>

                        <span class="chevron transition-transform {{ $isActive ? 'rotate-180' : '' }}">
                            ▾
                        </span>
                    </button>

                    <!-- SUBMENU -->
                    <div class="submenu {{ $isActive ? '' : 'hidden' }} mt-1 ml-5 space-y-1">

                        @foreach($item['children'] as $child)

                            @php
                                $childActive = $child['route'] !== '#' && request()->routeIs($child['route']);
                            @endphp

                            <a href="{{ $child['route'] === '#' ? '#' : route($child['route']) }}"
                               class="block px-3 py-1.5 rounded-lg
                               {{ $childActive
                                    ? 'bg-white/15 text-white font-medium'
                                    : 'text-white/60 hover:text-white hover:bg-white/10' }}">

                                {{ $child['label'] }}
                            </a>

                        @endforeach

                    </div>
                </div>
            @endif

        @endforeach

    </nav>

    <!-- USER -->
    <div class="flex items-center gap-3 px-5 py-4 border-t border-white/10">
        <div class="w-9 h-9 bg-indigo-500 rounded-full flex items-center justify-center font-semibold text-sm">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>

        <div>
            <div class="text-sm font-semibold">
                {{ auth()->user()->name ?? 'Admin User' }}
            </div>
            <div class="text-xs text-white/50">
                {{ auth()->user()->email ?? 'admin@agency.com' }}
            </div>
        </div>
    </div>

</aside>