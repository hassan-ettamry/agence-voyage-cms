@php
$menu = config('menu');
@endphp

<aside id="sidebar"
    class="
    fixed md:static
    top-0 left-0
    z-50
    w-64
    h-full md:h-screen
    bg-[#1a2744]
    text-white
    flex flex-col
    transform -translate-x-full md:translate-x-0
    transition-transform duration-200
">

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
    <nav class="flex-1 px-3 py-4 space-y-1 text-[13.5px] overflow-y-auto no-scrollbar">

        @foreach($menu as $item)

            @php
                $isActive = isset($item['active']) && request()->routeIs($item['active']);
                $hasActiveChild = false;

                if (isset($item['children'])) {
                    foreach ($item['children'] as $child) {
                        if (!empty($child['route']) && $child['route'] !== '#' && request()->routeIs($child['route'])) {
                            $hasActiveChild = true;
                            break;
                        }
                    }

                    $isActive = $isActive || $hasActiveChild;
                }
            @endphp

            {{-- SIMPLE LINK --}}
            @if(!isset($item['children']))
                <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg
                   {{ $isActive || request()->routeIs($item['route'])
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
                               class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg
                               {{ $childActive
                                    ? 'bg-white/15 text-white font-medium'
                                    : 'text-white/60 hover:text-white hover:bg-white/10' }}">

                                @if(!empty($child['icon']))
                                    <x-layout.icon :name="$child['icon']" />
                                @endif

                                {{ $child['label'] }}
                            </a>

                        @endforeach

                    </div>
                </div>
            @endif

        @endforeach

    </nav>

    @php
        $isAccountArea = request()->routeIs('account.*');
        $isProfileActive = request()->routeIs('account.profile.*');
        $isSettingsActive = request()->routeIs('account.settings.*');
    @endphp

    <!-- USER -->
    <div class="user-menu-anchor relative flex items-center gap-3 px-5 py-4 border-t border-white/10">
        <div class="user-menu absolute bottom-[calc(100%+14px)] left-5 right-5 z-20 {{ $isAccountArea ? '' : 'hidden' }} overflow-visible rounded-[18px] bg-white px-[18px] py-[18px] text-[#111b31] shadow-[0_22px_48px_-24px_rgba(0,0,0,0.5)] ring-1 ring-slate-900/5">
            <div class="pointer-events-none absolute -bottom-[8px] right-[30px] h-[17px] w-[17px] rotate-45 rounded-[3px] bg-white"></div>

            <div class="mb-[12px] pl-[2px] text-[12px] font-bold uppercase leading-none tracking-[0.18em] text-[#778397]">
                Account
            </div>

            <div>
                <a href="{{ route('account.profile.edit') }}"
                   class="flex h-[48px] items-center gap-[16px] rounded-[10px] px-[10px] text-[15px] font-medium transition {{ $isProfileActive ? 'bg-[#f1edff] text-[#5b3ff1]' : 'text-[#111b31] hover:bg-slate-50' }}">
                    <svg class="h-[24px] w-[24px] shrink-0 {{ $isProfileActive ? 'text-[#5b3ff1]' : 'text-[#66758d]' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="3.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 20a6.5 6.5 0 0 1 13 0"/>
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                    <span>Edit profile</span>
                </a>

                <div class="h-px bg-[#d9e0eb]"></div>

                <a href="{{ route('account.settings.edit') }}"
                   class="flex h-[52px] items-center gap-[16px] rounded-[10px] px-[10px] text-[15px] font-medium transition {{ $isSettingsActive ? 'bg-[#f1edff] text-[#5b3ff1]' : 'text-[#111b31] hover:bg-slate-50' }}">
                    <svg class="h-[25px] w-[25px] shrink-0 {{ $isSettingsActive ? 'text-[#5b3ff1]' : 'text-[#66758d]' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.2a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.1 15a1.7 1.7 0 0 0 .34 1.88l.04.04a2 2 0 0 1-2.83 2.83l-.04-.04A1.7 1.7 0 0 0 14.7 19a1.7 1.7 0 0 0-1.03 1.56V20.6a2 2 0 0 1-4 0v-.04A1.7 1.7 0 0 0 8.7 19a1.7 1.7 0 0 0-1.88.34l-.04.04a2 2 0 0 1-2.83-2.83l.04-.04A1.7 1.7 0 0 0 4.3 15a1.7 1.7 0 0 0-1.56-1.03H2.7a2 2 0 0 1 0-4h.04A1.7 1.7 0 0 0 4.3 8.9a1.7 1.7 0 0 0-.34-1.88l-.04-.04a2 2 0 0 1 2.83-2.83l.04.04A1.7 1.7 0 0 0 8.7 4.5a1.7 1.7 0 0 0 1.03-1.56V2.9a2 2 0 0 1 4 0v.04a1.7 1.7 0 0 0 1.03 1.56 1.7 1.7 0 0 0 1.88-.34l.04-.04a2 2 0 0 1 2.83 2.83l-.04.04A1.7 1.7 0 0 0 19.1 8.9a1.7 1.7 0 0 0 1.56 1.03h.04a2 2 0 0 1 0 4h-.04A1.7 1.7 0 0 0 19.1 15Z"/>
                    </svg>
                    <span>Account settings</span>
                </a>

                <div class="h-px bg-[#d9e0eb]"></div>

                <a href="{{ route('logout') }}"
                   class="flex h-[52px] items-center gap-[16px] rounded-[10px] px-[10px] text-[15px] font-medium text-[#111b31] transition hover:bg-slate-50"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg class="h-[24px] w-[24px] shrink-0 text-[#d24b4b]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H6.5A2.5 2.5 0 0 0 4 7.5v9A2.5 2.5 0 0 0 6.5 19H9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 8l4 4-4 4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H9"/>
                    </svg>
                    <span>Sign out</span>
                </a>
            </div>
        </div>

        <div class="w-9 h-9 bg-indigo-500 rounded-full flex items-center justify-center font-semibold text-sm shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>

        <div class="min-w-0 flex-1">
            <div class="text-sm font-semibold truncate">
                {{ auth()->user()->name ?? 'Admin User' }}
            </div>
            <div class="text-xs text-white/50 truncate">
                {{ auth()->user()->email ?? 'admin@agency.com' }}
            </div>
        </div>

        <button type="button"
            class="user-menu-toggle w-8 h-8 shrink-0 flex items-center justify-center rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition"
            aria-label="User menu"
            aria-expanded="{{ $isAccountArea ? 'true' : 'false' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="1"/>
                <circle cx="19" cy="12" r="1"/>
                <circle cx="5" cy="12" r="1"/>
            </svg>
        </button>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

</aside>
