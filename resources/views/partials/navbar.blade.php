{{-- ╔══════════════════════════════════════════════════════════════════╗
║ NAVBAR PARTIAL — resources/views/partials/navbar.blade.php ║
╚══════════════════════════════════════════════════════════════════╝ --}}

<nav x-data="{
        mobileOpen: false,
        scrolled: false,
        searchOpen: false,
        init() {
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 20;
            });
        },
        openSearch() {
            this.searchOpen = true;
            this.$nextTick(() => {
                const inp = document.getElementById('nav-search-input');
                if (inp) inp.focus();
            });
        },
        closeSearch() {
            this.searchOpen = false;
        }
    }" x-bind:class="scrolled
        ? 'bg-slate-900 shadow-xl shadow-black/30 border-b border-slate-700'
        : 'bg-slate-900/95 border-b border-slate-800'"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 backdrop-blur-xl">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">

            {{-- ══ LOGO (trái) ══════════════════════════════════════ --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group mr-6">
                <div class="w-8 h-8 bg-rose-600 rounded-lg flex items-center justify-center
                            group-hover:bg-rose-500 transition-colors shadow-md shadow-rose-900/50">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4
                               M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">Reco</span>
            </a>

            {{-- ══ NAV LINKS (giữa, desktop) ═══════════════════════ --}}
            <div class="hidden md:flex flex-1 items-center justify-center gap-1">
                @php
                    $links = [
                        ['label' => 'Trang chủ', 'route' => 'home', 'match' => 'home'],
                        ['label' => 'Khám phá', 'route' => 'explore', 'match' => 'movies.*'],
                        ['label' => 'Tin tức', 'route' => null, 'match' => null],
                        ['label' => 'Diễn đàn', 'route' => null, 'match' => null],
                    ];
                @endphp

                @foreach($links as $link)
                            @php
                                $active = $link['match'] && request()->routeIs($link['match']);
                                $href = $link['route'] ? route($link['route']) : '#';
                            @endphp
                            <a href="{{ $href }}" class="relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150
                                          {{ $active
                    ? 'text-white bg-white/10'
                    : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                                {{ $link['label'] }}
                                @if($active)
                                    <span class="absolute bottom-0.5 left-3 right-3 h-0.5 bg-rose-500 rounded-full"></span>
                                @endif
                            </a>
                @endforeach
            </div>

            {{-- ══ RIGHT SIDE ══════════════════════════════════════ --}}
            <div class="flex items-center gap-2 ml-auto">

                {{-- Search expandable (desktop) --}}
                <div class="hidden md:flex items-center relative">
                    <div x-show="searchOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="absolute right-10 w-64 origin-right"
                        style="display:none">
                        <form action="{{ route('explore') }}" method="GET">
                            <input type="text" name="q" id="nav-search-input" value="{{ request('q') }}"
                                @keydown.escape="closeSearch()" placeholder="Tìm kiếm phim..." class="w-full pl-4 pr-4 py-1.5 text-sm rounded-xl
                                          bg-slate-800 border border-slate-600 text-slate-100
                                          placeholder-slate-500 focus:border-rose-500 focus:outline-none
                                          focus:ring-1 focus:ring-rose-500 transition-colors">
                        </form>
                    </div>

                    <button @click="searchOpen ? closeSearch() : openSearch()"
                        :class="searchOpen ? 'text-white bg-slate-700 border-slate-600' : 'text-slate-400 bg-slate-800/80 border-slate-700'"
                        class="relative z-10 w-9 h-9 rounded-xl border flex items-center justify-center
                                   hover:text-white hover:bg-slate-700 transition-all duration-150" title="Tìm kiếm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>

                @auth
                    {{-- Bell --}}
                    <button title="Thông báo" class="hidden sm:flex w-9 h-9 rounded-xl bg-slate-800/80 border border-slate-700
                                       text-slate-400 hover:text-white hover:bg-slate-700 items-center justify-center
                                       transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                                         a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                                         C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436
                                         L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    {{-- Avatar Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-1.5 rounded-xl px-2 py-1.5
                                           hover:bg-white/5 transition-all duration-150 group">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-500 to-rose-700
                                            flex items-center justify-center overflow-hidden
                                            ring-2 ring-slate-700 group-hover:ring-rose-500/50 transition-all">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <svg class="w-3 h-3 text-slate-400 hidden sm:block transition-transform duration-150"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 rounded-xl bg-slate-800 border border-slate-700
                                        shadow-2xl shadow-black/50 p-1.5 z-50" style="display:none">

                            {{-- Header user info --}}
                            <div class="px-3 py-2.5 mb-1 border-b border-slate-700">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rose-500 to-rose-700
                                                    flex items-center justify-center overflow-hidden shrink-0">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm font-bold text-white">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Menu items --}}
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-slate-300
                                          hover:text-white hover:bg-slate-700 transition-colors group">
                                <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-400 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Hồ sơ cá nhân
                            </a>

                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-slate-300
                                          hover:text-white hover:bg-slate-700 transition-colors group">
                                <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-400 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>

                            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-slate-300
                                          hover:text-white hover:bg-slate-700 transition-colors group">
                                <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-400 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                                Danh sách yêu thích
                            </a>

                            <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-slate-300
                                          hover:text-white hover:bg-slate-700 transition-colors group">
                                <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-400 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066
                                                 c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572
                                                 c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573
                                                 c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065
                                                 c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066
                                                 c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572
                                                 c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573
                                                 c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Cài đặt
                            </a>

                            @can('access-admin')
                                <div class="mt-1 pt-1 border-t border-slate-700">
                                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                                                  text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944
                                                         a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9
                                                         c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622
                                                         0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Admin Panel
                                    </a>
                                </div>
                            @endcan

                            <div class="mt-1 pt-1 border-t border-slate-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-sm text-left
                                                       text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors group">
                                        <svg class="w-4 h-4 shrink-0 group-hover:text-red-400 transition-colors" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6
                                                         a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-slate-300 hover:text-white
                                  transition-colors px-3 py-2">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold px-4 py-2 rounded-xl text-white
                                  bg-rose-600 hover:bg-rose-500 transition-all duration-150
                                  shadow-md shadow-rose-900/30">
                        Đăng ký
                    </a>
                @endauth

                {{-- Hamburger (mobile) --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden w-9 h-9 rounded-xl bg-slate-800/80 border border-slate-700
                               text-slate-300 hover:text-white hover:bg-slate-700 flex items-center
                               justify-center transition-all duration-150 ml-1">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" style="display:none" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══ MOBILE MENU ════════════════════════════════════════════════ --}}
    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-slate-700/80 bg-slate-900"
        style="display:none">

        <div class="px-4 py-4 space-y-1">
            {{-- Mobile search --}}
            <form action="{{ route('explore') }}" method="GET" class="mb-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm phim..." class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl
                                  bg-slate-800 border border-slate-600 text-slate-100
                                  placeholder-slate-500 focus:border-rose-500 focus:outline-none
                                  focus:ring-1 focus:ring-rose-500 transition-colors">
                </div>
            </form>

            {{-- Mobile nav links --}}
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('home') ? 'bg-rose-600/20 text-rose-400 border border-rose-500/20' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                Trang chủ
            </a>
            <a href="{{ route('explore') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('movies.*') ? 'bg-rose-600/20 text-rose-400 border border-rose-500/20' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                Khám phá
            </a>
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-colors">
                Tin tức
            </a>
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-colors">
                Diễn đàn
            </a>

            {{-- Mobile auth --}}
            <div class="pt-3 border-t border-slate-700 space-y-1">
                @auth
                    <div class="flex items-center gap-3 px-3 py-3 mb-2 bg-slate-800/60 rounded-xl">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-500 to-rose-700
                                        flex items-center justify-center overflow-hidden ring-2 ring-slate-700 shrink-0">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Hồ
                        sơ cá nhân</a>
                    <a href="{{ route('dashboard') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Dashboard</a>
                    <a href="#"
                        class="block px-3 py-2.5 rounded-xl text-sm text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Danh
                        sách yêu thích</a>
                    <a href="#"
                        class="block px-3 py-2.5 rounded-xl text-sm text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Cài
                        đặt</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-3 py-2.5 rounded-xl text-sm text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-slate-300 hover:text-white hover:bg-white/5 transition-colors">Đăng
                        nhập</a>
                    <a href="{{ route('register') }}"
                        class="block text-center px-3 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-500 transition-colors">Đăng
                        ký</a>
                @endauth
            </div>
        </div>
    </div>
</nav>