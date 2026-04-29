<aside class="w-64 h-screen bg-[#1a2744] text-white flex flex-col">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="w-8 h-8 flex items-center justify-center">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8">
                <path d="M4 8L14 4L14 14L4 18Z" fill="#4f8ef7"/>
                <path d="M14 4L28 8L20 14L14 14Z" fill="#6ea8fe"/>
                <path d="M4 18L14 14L20 20L10 24Z" fill="#3b6fd4"/>
            </svg>
        </div>
        <span class="text-lg font-bold tracking-tight">Website Builder</span>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 text-[13.5px] overflow-y-auto no-scrollbar">

        <!-- Dashboard -->
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="4" rx="1"/>
                <rect x="14" y="11" width="7" height="9" rx="1"/>
                <rect x="3" y="14" width="7" height="6" rx="1"/>
            </svg>
            Dashboard
        </a>

        <!-- Analytics -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path d="M3 18l4-6 4 3 4-7 4 4"/>
                        <path d="M3 20h18"/>
                    </svg>
                    Analytics
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Website Builder -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg
                {{ request()->routeIs('pages.*') ? 'text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }} transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M7 8h10M7 12h6"/>
                    </svg>
                    <span class="{{ request()->routeIs('pages.*') ? 'font-semibold text-white' : '' }}">Website Builder</span>
                </div>
                <svg class="chevron w-4 h-4 transition-transform {{ request()->routeIs('pages.*') ? 'rotate-180' : '' }}"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- TREE SUBMENU -->
            <div class="submenu {{ request()->routeIs('pages.*') ? '' : 'hidden' }} mt-3 ml-5">

                @php
                $subs = [
                    'Pages'             => route('pages.index'),
                    'Menus'             => '#',
                    'Color Scheme'      => '#',
                    'Font Settings'     => '#',
                    'Language Settings' => '#',
                ];
                @endphp

                <div class="relative">
                    <!-- Vertical line -->
                    <div class="absolute left-0 top-0 w-px bg-white/20" style="bottom: 1.25rem;"></div>

                    <div class="space-y-0.5">
                        @foreach($subs as $name => $link)
                        <div class="relative flex items-center">
                            <div class="relative flex-shrink-0 w-4 h-8 flex items-center">
                                <div class="absolute left-0 bottom-1/2 w-3.5 h-1/2 border-l border-b border-white/25 rounded-bl-[6px]"></div>
                                @if($loop->last)
                                <div class="absolute left-0 top-1/2 w-px h-1/2 bg-[#1a2744]"></div>
                                @endif
                            </div>
                            <a href="{{ $link }}"
                               class="flex-1 px-3 py-1.5 rounded-lg text-[13px] transition-colors
                               {{ request()->routeIs('pages.*') && $name === 'Pages'
                                    ? 'bg-white/15 text-white font-medium'
                                    : 'text-white/55 hover:text-white hover:bg-white/10' }}">
                                {{ $name }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <circle cx="9" cy="7" r="2.5"/>
                        <circle cx="17" cy="5" r="2"/>
                        <path d="M3 20c0-3 2.5-5 6-5s6 2 6 5"/>
                        <path d="M17 7v8M14 12h6"/>
                    </svg>
                    Courses
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Products & Services -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                    Products &amp; Services
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Users -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <circle cx="8" cy="8" r="3"/>
                        <circle cx="16" cy="8" r="3"/>
                        <path d="M2 20c0-3 2.5-5 6-5s6 2 6 5"/>
                        <path d="M16 15c2 0 4 1.5 4 4"/>
                    </svg>
                    Users
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Deals & Sales -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path d="M12 2a8 8 0 100 16A8 8 0 0012 2z"/>
                        <path d="M8.5 8.5C8.5 7.1 9.6 6 11 6h2a2 2 0 010 4H11a2 2 0 000 4h2a2 2 0 100-4"/>
                        <path d="M12 6V4m0 14v-2"/>
                    </svg>
                    Deals &amp; Sales
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Marketing & Contacts -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path d="M22 12c0 5.5-4.5 9-10 9a11 11 0 01-6.4-2L2 21l2.1-3.5A9 9 0 012 12C2 6.5 6.5 3 12 3s10 3.5 10 9z"/>
                        <path d="M8 12h.01M12 12h.01M16 12h.01"/>
                    </svg>
                    Marketing &amp; Contacts
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Site Settings -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                    Site Settings
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Addons -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                    Addons
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Blogs -->
        <div>
            <button class="menu-toggle flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <path d="M7 8h10M7 12h7M7 16h5"/>
                    </svg>
                    Blogs
                </div>
                <svg class="chevron w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Earn money -->
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 6v.5M12 17v.5M9 9.5C9 8.1 10.3 7 12 7s3 1.1 3 2.5c0 2-3 2.5-3 4.5m0 0c0 1.5 1.3 2 3 2"/>
            </svg>
            Earn money
        </a>

    </nav>

    <!-- User -->
    <div class="flex items-center gap-3 px-5 py-4 border-t border-white/10">
        <div class="w-9 h-9 bg-indigo-500 rounded-full flex items-center justify-center font-semibold text-sm">
            A
        </div>
        <div>
            <div class="text-sm font-semibold">Admin User</div>
            <div class="text-xs text-white/50">admin@agency.com</div>
        </div>
    </div>

</aside>
