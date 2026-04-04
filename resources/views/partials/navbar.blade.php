<nav x-data="{
        mobileOpen: false,
        scrolled: false,
        darkHero: false,
        heroDark: false,
        init() {
            // Detect if page has a dark hero section
            const heroEl = document.querySelector('[data-hero-dark]');
            if (heroEl) {
                this.heroDark = true;
                this.darkHero = true;
            }
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 10;
                if (this.heroDark) {
                    const heroEl = document.querySelector('[data-hero-dark]');
                    if (heroEl) {
                        const heroBottom = heroEl.offsetTop + heroEl.offsetHeight;
                        this.darkHero = window.scrollY < (heroBottom - 80);
                    }
                }
            });
        }
    }" x-bind:class="{
        'bg-white/75 shadow-sm border-b border-gray-200': scrolled,
        'bg-white/55 border-b border-gray-100': !scrolled && !darkHero,
        'bg-black/30 border-b border-white/10': !scrolled && darkHero,
    }"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 backdrop-blur-xl backdrop-saturate-150 bg-white/55 border-b border-gray-100">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 items-center h-16">

            <div class="hidden md:flex items-center gap-6">
                @php
                    $links = [
                        ['label' => 'Trang chủ', 'route' => 'home', 'match' => 'home'],
                        ['label' => 'Khám phá',  'route' => 'explore', 'match' => 'explore'],
                        ['label' => 'Tin tức',   'route' => 'news.index', 'match' => 'news.*'],
                        ['label' => 'Diễn đàn',  'route' => 'forum.index', 'match' => 'forum.*'],
                    ];
                @endphp

                @foreach($links as $link)
                    @php
                        $active = $link['match'] && request()->routeIs($link['match']);
                        $href = $link['route'] ? route($link['route']) : '#';
                    @endphp
                    <a href="{{ $href }}" class="text-[13px] uppercase tracking-wider font-medium transition-colors duration-300 {{ $active ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}"
                       :class="{
                           '{{ $active ? "text-white" : "text-white/70 hover:text-white" }}': darkHero,
                           '{{ $active ? "text-gray-900" : "text-gray-500 hover:text-gray-900" }}': !darkHero
                       }">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Mobile: hamburger on the left --}}
            <div class="md:hidden flex items-center">
                <button @click="mobileOpen = !mobileOpen" class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300"
                        :class="darkHero ? 'text-white/80 hover:text-white hover:bg-white/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" style="display:none" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- CENTER: LOGO --}}
            <div class="flex items-center justify-center">
                <a href="{{ route('home') }}" class="flex items-center shrink-0">
                    <img x-show="!darkHero" src="{{ asset('storage/images/logo.svg') }}" alt="RecoDB" height="28" style="height: 28px; width: auto;">
                    <img x-cloak x-show="darkHero" src="{{ asset('storage/images/logo-dark.svg') }}" alt="RecoDB" height="28" style="height: 28px; width: auto; display:none">
                </a>
            </div>

            {{-- RIGHT: SEARCH · NOTIFICATION · PROFILE --}}
            <div class="flex items-center justify-end gap-2">

                {{-- Search button --}}
                <div x-data x-on:keydown.window.prevent.ctrl.k="$dispatch('open-search')"
                     x-on:keydown.window.prevent.meta.k="$dispatch('open-search')">
                    <button @click="$dispatch('open-search')" title="Tìm kiếm (Ctrl+K)"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300"
                        :class="{
                            'bg-white/10 border-white/20 text-white/80 hover:text-white hover:bg-white/20 hover:border-white/30': darkHero,
                            'bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300': !darkHero
                        }">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>

                @auth
                    {{-- Notification button --}}
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
                            class="relative w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300"
                            :class="{
                                'bg-gray-200/70 border-gray-300 text-gray-900': open,
                                'bg-white/10 border-white/20 text-white/80 hover:text-white hover:bg-white/20 hover:border-white/30': !open && darkHero,
                                'bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300': !open && !darkHero
                            }">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" style="display: none;" class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-sky-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-1"></span>
                        </button>

                        {{-- Notification Dropdown Panel --}}
                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                             class="absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50">
                            <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 text-sm">Thông báo</h3>
                                <button x-show="unreadCount > 0" @click="markAllAsRead()" class="text-xs text-sky-500 hover:text-sky-600 font-medium">Đánh dấu đã đọc</button>
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
                                         :class="!item.read_at ? 'bg-sky-50/30' : 'bg-transparent'">
                                        <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-500 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-700 leading-snug break-words" x-text="item.data.message"></p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="item.created_at"></p>
                                        </div>
                                        <div x-show="!item.read_at" class="w-2 h-2 rounded-full bg-sky-500 shrink-0 mt-1.5"></div>
                                    </div>
                                </template>
                            </div>

                            <a href="{{ route('notifications.all') }}" class="block p-2.5 text-center text-xs font-medium text-gray-500 hover:text-sky-600 hover:bg-gray-50 transition border-t border-gray-100">
                                Xem tất cả thông báo
                            </a>
                        </div>
                    </div>

                    {{-- Avatar / Profile button --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="w-10 h-10 rounded-full overflow-hidden
                                           flex items-center justify-center transition-all duration-300"
                                :class="darkHero ? 'ring-2 ring-white/30 hover:ring-white/50' : 'ring-2 ring-gray-200 hover:ring-gray-300'">
                            <div class="w-full h-full rounded-full bg-gradient-to-br from-sky-400 to-sky-600
                                            flex items-center justify-center overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                        </button>

                        {{-- Profile Dropdown --}}
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
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-400 to-sky-600
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
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-sky-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Hồ sơ cá nhân
                            </a>

                            <a href="{{ route('messages.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600
                                          hover:text-gray-900 hover:bg-gray-50 transition-colors group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-sky-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Tin nhắn
                            </a>

                            <a href="{{ route('mylist') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600
                                          hover:text-gray-900 hover:bg-gray-50 transition-colors group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-sky-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                                Danh sách của tôi
                            </a>

                            <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-600
                                          hover:text-gray-900 hover:bg-gray-50 transition-colors group">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-sky-500 transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Cài đặt bảo mật
                            </a>

                            @if(Auth::user()->isStaff())
                                <div class="mt-1 pt-1 border-t border-gray-100">
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                                                  text-sky-500 hover:text-sky-600 hover:bg-sky-50 transition-colors">
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
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center text-[13px] font-medium px-4 py-2 rounded-full border transition-all duration-300"
                           :class="darkHero
                                ? 'border-white/30 text-white/80 hover:bg-white/10 hover:text-white hover:border-white/50'
                                : 'border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-400'">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center text-[13px] font-semibold px-4 py-2 rounded-full text-white
                                      bg-sky-500 hover:bg-sky-600 transition-all duration-200
                                      shadow-sm hover:shadow-md hover:shadow-sky-200/50">
                            Đăng ký
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-gray-100 bg-white"
        style="display:none">

        <div class="px-4 py-4 space-y-1">
            {{-- Mobile search --}}
            <button @click="$dispatch('open-search'); mobileOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Tìm kiếm phim...
            </button>

            {{-- Mobile nav links --}}
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('home') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Trang chủ
            </a>
            <a href="{{ route('explore') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('explore') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Khám phá
            </a>
            <a href="{{ route('news.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('news.*') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Tin tức
            </a>
            <a href="{{ route('forum.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('forum.*') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Diễn đàn
            </a>

            {{-- Mobile auth --}}
            <div class="pt-3 border-t border-gray-100 space-y-1">
                @auth
                    <div class="flex items-center gap-3 px-3 py-3 mb-2 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-sky-600
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
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Hồ sơ (thông tin tài khoản)</a>
                    <a href="{{ route('messages.index') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Tin nhắn</a>
                    <a href="{{ route('mylist') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Danh sách của tôi</a>
                    <a href="{{ route('settings.index') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Cài đặt bảo mật (2FA)</a>
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
                        class="block text-center px-3 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-500 hover:bg-sky-600 transition-colors">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- SPOTLIGHT SEARCH OVERLAY --}}
<div x-data="{
        open: false,
        query: '',
        results: [],
        loading: false,
        showResults: false,
        async search() {
            // Sanitize: strip +, -, %, _ from query
            const cleanQuery = this.query.replace(/[+\-%_]/g, '').trim();
            if (cleanQuery.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }
            this.loading = true;
            try {
                const res = await fetch('{{ route("api.search") }}?q=' + encodeURIComponent(cleanQuery));
                this.results = await res.json();
                this.showResults = true;
            } finally {
                this.loading = false;
            }
        },
        goToExplore() {
            if (this.query.length > 0) {
                window.location.href = '{{ route("explore") }}?q=' + encodeURIComponent(this.query);
            }
        },
        reset() {
            this.open = false;
            this.query = '';
            this.results = [];
            this.showResults = false;
        }
    }"
    x-on:open-search.window="open = true; $nextTick(() => $refs.searchInput.focus())"
    x-on:keydown.escape.window="if(open) reset()"
    class="relative z-[100]">

    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="reset()"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm" style="display:none"></div>

    {{-- Search Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="fixed top-[15vh] left-1/2 -translate-x-1/2 w-full max-w-xl px-4" style="display:none">

        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            {{-- Search Input --}}
            <form @submit.prevent="goToExplore()" class="relative">
                <div class="flex items-center px-5 border-b border-gray-100">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input x-ref="searchInput" x-model="query" @input.debounce.300ms="search()"
                           type="text" placeholder="Tìm kiếm phim..." autocomplete="off"
                           class="flex-1 px-4 py-4 text-base text-gray-900 placeholder-gray-400
                                  border-0 focus:outline-none focus:ring-0 bg-transparent">
                    
                    {{-- Loading spinner --}}
                    <div x-show="loading" class="shrink-0">
                        <svg class="animate-spin h-5 w-5 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    
                    {{-- Keyboard shortcut hint --}}
                    <kbd x-show="!loading && query.length === 0" class="hidden sm:inline-flex items-center px-2 py-0.5 text-[11px] font-medium text-gray-400 bg-gray-100 rounded-md border border-gray-200">
                        ESC
                    </kbd>
                </div>
            </form>

            {{-- Results --}}
            <div x-show="showResults && results.length > 0" class="max-h-[320px] overflow-y-auto">
                <template x-for="movie in results" :key="movie.id">
                    <a :href="'/movies/' + movie.slug" @click="reset()"
                       class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0 group">
                        <div class="w-10 h-14 bg-gray-100 rounded-lg bg-cover bg-center shrink-0 overflow-hidden" :style="movie.poster ? `background-image: url('${movie.poster}')` : ''">
                            <template x-if="!movie.poster">
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                </div>
                            </template>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-sky-600 transition-colors" x-text="movie.title"></p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="movie.release_date ? movie.release_date.substring(0, 4) : ''"></p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </template>
            </div>

            {{-- No results --}}
            <div x-show="showResults && results.length === 0 && !loading && query.length >= 2" class="px-5 py-8 text-center border-t border-gray-50">
                <p class="text-gray-400 text-sm mb-4">Không tìm thấy kết quả cho "<span class="text-gray-600 font-bold" x-text="query"></span>"</p>
                <a :href="'{{ route("explore") }}?q=' + encodeURIComponent(query)" @click="reset()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 text-gray-700 font-medium text-[13px] transition-all">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Tìm kiếm nâng cao
                </a>
            </div>

            {{-- Footer --}}
            <div x-show="showResults && results.length > 0" class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">
                    Nhấn <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 shadow-sm rounded text-[10px] font-medium text-gray-600">Enter</kbd> để tìm chi tiết
                </span>
                <a :href="'{{ route("explore") }}?q=' + encodeURIComponent(query)" @click="reset()" class="inline-flex items-center gap-1.5 text-[13px] font-medium text-sky-600 hover:text-sky-700 transition-colors">
                    Tìm kiếm nâng cao
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            {{-- Empty state hint --}}
            <div x-show="!showResults && query.length === 0" class="px-5 py-6 text-center flex flex-col items-center border-t border-gray-50">
                <p class="text-[13px] text-gray-400 mb-3">Nhập tên phim để tìm kiếm nhanh</p>
                <a href="{{ route('explore') }}" @click="reset()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 text-gray-700 font-medium text-[13px] transition-all">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Sử dụng Tìm kiếm nâng cao
                </a>
            </div>
        </div>
    </div>
</div>