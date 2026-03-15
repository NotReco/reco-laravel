{{-- ╔══════════════════════════════════════════════════════════════════╗
║ NAVBAR — Clean White Theme with Custom Logo                      ║
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
        ? 'bg-white/95 shadow-lg shadow-black/5 border-b border-gray-200'
        : 'bg-white border-b border-gray-100'"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 backdrop-blur-xl">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">

            {{-- ══ LOGO ══════════════════════════════════════ --}}
            <a href="{{ route('home') }}" class="flex items-center shrink-0 mr-8 group">
                <img src="{{ asset('storage/images/logo.svg') }}" alt="RecoDB" class="h-10 w-auto group-hover:opacity-80 transition-opacity">
            </a>

            {{-- ══ NAV LINKS (desktop) ═══════════════════════ --}}
            <div class="hidden md:flex flex-1 items-center gap-1">
                @php
                    $links = [
                        ['label' => 'Trang chủ', 'route' => 'home', 'match' => 'home'],
                        ['label' => 'Khám phá',  'route' => 'explore', 'match' => 'explore'],
                        ['label' => 'Diễn đàn',  'route' => 'forum.index', 'match' => 'forum.*'],
                    ];
                @endphp

                @foreach($links as $link)
                    @php
                        $active = $link['match'] && request()->routeIs($link['match']);
                        $href = $link['route'] ? route($link['route']) : '#';
                    @endphp
                    <a href="{{ $href }}" class="relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                        {{ $active
                            ? 'text-rose-600 bg-rose-50'
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
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
                <div class="hidden md:flex items-center relative" x-data="{
                    searchQuery: '{{ request('q') }}',
                    results: [],
                    loading: false,
                    showResults: false,
                    async performSearch() {
                        if (this.searchQuery.length < 2) {
                            this.results = [];
                            this.showResults = false;
                            return;
                        }
                        this.loading = true;
                        try {
                            const res = await fetch('/api/search?q=' + encodeURIComponent(this.searchQuery));
                            this.results = await res.json();
                            this.showResults = true;
                        } finally {
                            this.loading = false;
                        }
                    }
                }" @click.outside="showResults = false">
                    <div x-show="searchOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="absolute right-10 w-72 origin-right z-50"
                        style="display:none">
                        
                        <form action="{{ route('explore') }}" method="GET" class="relative">
                            <input type="text" name="q" id="nav-search-input" x-model="searchQuery"
                                @input.debounce.300ms="performSearch()"
                                @focus="if(results.length > 0) showResults = true"
                                @keydown.escape="closeSearch(); showResults = false"
                                placeholder="Tìm kiếm phim..." autocomplete="off"
                                class="w-full pl-4 pr-10 py-2 text-sm rounded-xl
                                      bg-gray-50 border border-gray-200 text-gray-900
                                      placeholder-gray-400 focus:border-rose-400 focus:outline-none
                                      focus:ring-2 focus:ring-rose-100 transition-colors shadow-sm">
                            
                            {{-- Loading spinner --}}
                            <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </form>

                        {{-- Live Search Results Dropdown --}}
                        <div x-show="showResults && results.length > 0" x-transition
                            class="absolute top-12 left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50">
                            <template x-for="movie in results" :key="movie.id">
                                <a :href="'/movies/' + movie.id" class="flex items-center gap-3 p-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                    <div class="w-10 h-14 bg-gray-100 rounded-lg bg-cover bg-center shrink-0" :style="movie.poster ? `background-image: url('${movie.poster}')` : ''">
                                        <template x-if="!movie.poster">
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate" x-text="movie.title"></p>
                                        <p class="text-xs text-gray-400" x-text="movie.release_date ? movie.release_date.substring(0, 4) : ''"></p>
                                    </div>
                                </a>
                            </template>
                            <a :href="'/explore?q=' + encodeURIComponent(searchQuery)" class="block text-center p-2.5 text-xs font-medium text-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                Xem tất cả kết quả →
                            </a>
                        </div>
                    </div>

                    <button @click="searchOpen ? closeSearch() : openSearch()"
                        :class="searchOpen ? 'text-rose-600 bg-rose-50 border-rose-200' : 'text-gray-400 bg-white border-gray-200 hover:text-gray-600 hover:bg-gray-50'"
                        class="relative z-10 w-9 h-9 rounded-xl border flex items-center justify-center transition-all duration-150" title="Tìm kiếm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>

                @auth
                    {{-- Notification Dropdown --}}
                    <div class="relative hidden sm:block" x-data="{
                        open: false,
                        notifications: [],
                        unreadCount: 0,
                        async fetchNotifications() {
                            try {
                                const res = await fetch('{{ route('notifications.index') }}');
                                const data = await res.json();
                                this.notifications = data.notifications;
                                this.unreadCount = data.unread_count;
                            } catch (e) { console.error('Error fetching notifications:', e); }
                        },
                        async markAsRead(id) {
                            try {
                                await fetch(`/api/notifications/${id}/read`, {
                                    method: 'POST',
                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                                });
                                this.fetchNotifications();
                            } catch (e) { console.error(e); }
                        },
                        async markAllAsRead() {
                            try {
                                await fetch('{{ route('notifications.markAllAsRead') }}', {
                                    method: 'POST',
                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                                });
                                this.fetchNotifications();
                            } catch (e) { console.error(e); }
                        },
                        init() { this.fetchNotifications(); }
                    }" @click.outside="open = false">
                        
                        <button @click="open = !open; if(open) fetchNotifications()" title="Thông báo" 
                            class="relative w-9 h-9 rounded-xl border flex items-center justify-center transition-all duration-150"
                            :class="open ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-white border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" style="display: none;" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white"></span>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                             class="absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50">
                            <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 text-sm">Thông báo</h3>
                                <button x-show="unreadCount > 0" @click="markAllAsRead()" class="text-xs text-rose-500 hover:text-rose-600 font-medium">Đánh dấu đã đọc</button>
                            </div>
                            
                            <div class="max-h-[300px] overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="p-6 text-center text-gray-400 text-sm">
                                        Không có thông báo nào cả.
                                    </div>
                                </template>
                                <template x-for="item in notifications" :key="item.id">
                                    <div @click="if(!item.read_at) markAsRead(item.id); if(item.data.url) window.location.href = item.data.url;" 
                                         class="p-3 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer flex gap-3"
                                         :class="!item.read_at ? 'bg-rose-50/30' : 'bg-transparent'">
                                        <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-700 leading-snug break-words" x-text="item.data.message"></p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="item.created_at"></p>
                                        </div>
                                        <div x-show="!item.read_at" class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-1.5"></div>
                                    </div>
                                </template>
                            </div>

                            <a href="{{ route('notifications.all') }}" class="block p-2.5 text-center text-xs font-medium text-gray-500 hover:text-rose-600 hover:bg-gray-50 transition border-t border-gray-100">
                                Xem tất cả thông báo
                            </a>
                        </div>
                    </div>

                    {{-- Avatar Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-1.5 rounded-xl px-2 py-1.5
                                           hover:bg-gray-50 transition-all duration-150 group">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-400 to-rose-600
                                            flex items-center justify-center overflow-hidden
                                            ring-2 ring-gray-100 group-hover:ring-rose-200 transition-all">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <svg class="w-3 h-3 text-gray-400 hidden sm:block transition-transform duration-150"
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
                            class="absolute right-0 mt-2 w-56 rounded-xl bg-white border border-gray-200
                                        shadow-xl p-1.5 z-50" style="display:none">

                            {{-- Header user info --}}
                            <div class="px-3 py-2.5 mb-1 border-b border-gray-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rose-400 to-rose-600
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
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Menu items --}}
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600
                                          hover:text-gray-900 hover:bg-gray-50 transition-colors group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-rose-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Hồ sơ cá nhân
                            </a>

                            <a href="{{ route('messages.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600
                                          hover:text-gray-900 hover:bg-gray-50 transition-colors group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-rose-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Tin nhắn
                            </a>

                            <a href="{{ route('mylist') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600
                                          hover:text-gray-900 hover:bg-gray-50 transition-colors group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-rose-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                                Danh sách của tôi
                            </a>

                            <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600
                                          hover:text-gray-900 hover:bg-gray-50 transition-colors group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-rose-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Cài đặt
                            </a>

                            @if(Auth::user()->isStaff())
                                <div class="mt-1 pt-1 border-t border-gray-100">
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                                                  text-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Admin Panel
                                    </a>
                                </div>
                            @endif

                            <div class="mt-1 pt-1 border-t border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-sm text-left
                                                       text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors group">
                                        <svg class="w-4 h-4 shrink-0 group-hover:text-red-500 transition-colors" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-gray-600 hover:text-gray-900
                                  transition-colors px-3 py-2">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold px-5 py-2 rounded-xl text-white
                                  bg-rose-500 hover:bg-rose-600 transition-all duration-150
                                  shadow-md shadow-rose-200">
                        Đăng ký
                    </a>
                @endauth

                {{-- Hamburger (mobile) --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden w-9 h-9 rounded-xl bg-white border border-gray-200
                               text-gray-500 hover:text-gray-900 hover:bg-gray-50 flex items-center
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
        x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-gray-100 bg-white"
        style="display:none">

        <div class="px-4 py-4 space-y-1">
            {{-- Mobile search --}}
            <form action="{{ route('explore') }}" method="GET" class="mb-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm phim..." class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl
                                  bg-gray-50 border border-gray-200 text-gray-900
                                  placeholder-gray-400 focus:border-rose-400 focus:outline-none
                                  focus:ring-2 focus:ring-rose-100 transition-colors">
                </div>
            </form>

            {{-- Mobile nav links --}}
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('home') ? 'bg-rose-50 text-rose-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Trang chủ
            </a>
            <a href="{{ route('explore') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('explore') ? 'bg-rose-50 text-rose-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Khám phá
            </a>
            <a href="{{ route('forum.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('forum.*') ? 'bg-rose-50 text-rose-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Diễn đàn
            </a>

            {{-- Mobile auth --}}
            <div class="pt-3 border-t border-gray-100 space-y-1">
                @auth
                    <div class="flex items-center gap-3 px-3 py-3 mb-2 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-rose-600
                                        flex items-center justify-center overflow-hidden ring-2 ring-white shrink-0">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Hồ sơ cá nhân</a>
                    <a href="{{ route('messages.index') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Tin nhắn</a>
                    <a href="{{ route('mylist') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Danh sách của tôi</a>
                    <a href="{{ route('settings.index') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Cài đặt</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-3 py-2.5 rounded-xl text-sm text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Đăng nhập</a>
                    <a href="{{ route('register') }}"
                        class="block text-center px-3 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 transition-colors">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</nav>