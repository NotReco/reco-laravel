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
}"
    x-bind:class="{
        '{{ request()->routeIs('events.index') ? 'bg-white' : 'bg-white/75' }} shadow-sm border-b border-gray-200': scrolled,
        '{{ request()->routeIs('events.index') ? 'bg-white' : 'bg-white/55' }} border-b border-gray-100': !scrolled && !darkHero,
        'bg-black/30 border-b border-white/10': !scrolled && darkHero,
    }"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 backdrop-blur-xl backdrop-saturate-150 {{ request()->routeIs('events.index') ? 'bg-white' : 'bg-white/55' }} border-b border-gray-100">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- LEFT: MOBILE HAMBURGER + LOGO + DESKTOP LINKS --}}
            <div class="flex items-center gap-4 lg:gap-8">
                
                {{-- Mobile: hamburger on the left --}}
                <div class="md:hidden flex items-center">
                    <button @click="mobileOpen = !mobileOpen"
                        class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 -ml-2"
                        :class="darkHero ? 'text-white/80 hover:text-white hover:bg-white/10' :
                            'text-gray-500 hover:text-gray-900 hover:bg-gray-100'">
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

                {{-- LOGO --}}
                <a href="{{ route('home') }}" class="flex items-center shrink-0">
                    <img x-show="!darkHero" src="{{ asset('storage/images/logo.png') }}" alt="RecoDB" height="28"
                        style="height: 28px; width: auto;">
                    <img x-cloak x-show="darkHero" src="{{ asset('storage/images/logo-dark.png') }}" alt="RecoDB"
                        height="28" style="height: 28px; width: auto; display:none">
                </a>

                {{-- DESKTOP LINKS --}}
                <div class="hidden md:flex items-center gap-5 lg:gap-6 ml-2">
                    @php
                        $links = [
                            ['label' => 'Trang chủ', 'route' => 'home', 'match' => 'home'],
                            ['label' => 'Phim lẻ', 'route' => 'explore', 'match' => 'explore'],
                            ['label' => 'TV Series', 'route' => 'tv-shows.index', 'match' => 'tv-shows.*'],
                            ['label' => 'Diễn viên', 'route' => 'person.index', 'match' => 'person.*'],
                            ['label' => 'Tin tức', 'route' => 'news.index', 'match' => 'news.*'],
                            ['label' => 'Diễn đàn', 'route' => 'forum.index', 'match' => 'forum.*'],
                        ];
                    @endphp

                    @foreach ($links as $link)
                        @php
                            $active = $link['match'] && request()->routeIs($link['match']);
                            $href = $link['route'] ? route($link['route']) : '#';
                        @endphp
                        <a href="{{ $href }}"
                            class="text-[13px] uppercase tracking-wider font-medium transition-colors duration-300 {{ $active ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}"
                            :class="{
                                '{{ $active ? 'text-white' : 'text-white/70 hover:text-white' }}': darkHero,
                                '{{ $active ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}': !darkHero
                            }">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: SEARCH · NOTIFICATION · PROFILE --}}
            <div class="flex items-center justify-end gap-2">

                @auth
                    {{-- Gamified Event Button --}}
                    <a href="{{ route('events.index') }}" 
                        class="relative h-10 flex items-center gap-1.5 px-4 rounded-full transition-all duration-300 border bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300 mr-1 group"
                        :class="{
                            'bg-white/10 border-white/20 text-white/80 hover:text-white hover:bg-white/20 hover:border-white/30': darkHero,
                            'bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300': !darkHero
                        }">
                        
                        <svg class="w-[18px] h-[18px] shrink-0 text-amber-500 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                        <span class="hidden sm:inline text-[13px] font-bold uppercase tracking-wide mt-[1px]">Sự kiện</span>
                        
                        {{-- Unclaimed indicator --}}
                        <span class="absolute top-0 right-1 w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping opacity-75"></span>
                        <span class="absolute top-0 right-1 w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    </a>
                @endauth

                {{-- Search button --}}
                <div x-data x-on:keydown.window.prevent.ctrl.k="$dispatch('open-search')"
                    x-on:keydown.window.prevent.meta.k="$dispatch('open-search')">
                    <button @click="$dispatch('open-search')" title="Tìm kiếm (Ctrl+K)"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300"
                        :class="{
                            'bg-white/10 border-white/20 text-white/80 hover:text-white hover:bg-white/20 hover:border-white/30': darkHero,
                            'bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300':
                                !darkHero
                        }">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>

                @auth
                    {{-- Notification button --}}
                    <div class="relative" x-data="{
                        open: false,
                        tab: 'all',
                        menuOpen: false,
                        initialLoad: true,
                        loading: false,
                        notificationsData: { all: [], unread: [] },
                        loadedTabs: { all: false, unread: false },
                        limits: { all: 5, unread: 5 },
                        unreadCount: {{ Auth::user()->unreadNotifications()->count() }},
                        pendingAction: null,
                        get limit() { return this.limits[this.tab]; },
                        set limit(val) { this.limits[this.tab] = val; },
                        get notifications() {
                            const list = this.notificationsData[this.tab] || [];
                            let filtered = list.filter(n => !(this.pendingAction && this.pendingAction.type === 'delete' && n.id === this.pendingAction.id));
                            return this.tab === 'unread' ? filtered.filter(n => !n.read_at) : filtered;
                        },
                        async fetchNotifications(force = false) {
                            if (!force && this.loadedTabs[this.tab]) return;
                    
                            if (!this.initialLoad) {
                                this.loading = true;
                            }
                    
                            try {
                                const res = await fetch('{{ route('notifications.index') }}?filter=' + this.tab);
                                const data = await res.json();
                                this.notificationsData[this.tab] = data.notifications;
                                this.unreadCount = data.unread_count;
                                this.loadedTabs[this.tab] = true;
                            } catch (e) { console.error('Error fetching notifications:', e); }
                    
                            this.initialLoad = false;
                            this.loading = false;
                        },
                        async markAsRead(id) {
                            try {
                                await fetch('/api/notifications/' + id + '/read', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                let foundInAll = this.notificationsData.all.find(n => n.id === id);
                                if (foundInAll && !foundInAll.read_at) {
                                    foundInAll.read_at = new Date().toISOString();
                                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                                }
                                let foundInUnread = this.notificationsData.unread.find(n => n.id === id);
                                if (foundInUnread && !foundInUnread.read_at) {
                                    foundInUnread.read_at = new Date().toISOString();
                                }
                            } catch (e) { console.error(e); }
                        },
                        async markAsUnread(id) {
                            try {
                                await fetch('/api/notifications/' + id + '/unread', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                let foundInAll = this.notificationsData.all.find(n => n.id === id);
                                if (foundInAll && foundInAll.read_at) {
                                    foundInAll.read_at = null;
                                    this.unreadCount++;
                                }
                                let foundInUnread = this.notificationsData.unread.find(n => n.id === id);
                                if (foundInUnread && foundInUnread.read_at) {
                                    foundInUnread.read_at = null;
                                }
                            } catch (e) { console.error(e); }
                        },
                        async flushAction(actionObj) {
                            if (!actionObj) return;
                            try {
                                if (actionObj.type === 'delete') {
                                    fetch('/api/notifications/' + actionObj.id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, keepalive: true });
                                    this.notificationsData.all = this.notificationsData.all.filter(n => n.id !== actionObj.id);
                                    this.notificationsData.unread = this.notificationsData.unread.filter(n => n.id !== actionObj.id);
                                    if (actionObj.wasUnread) this.unreadCount = Math.max(0, this.unreadCount - 1);
                                } else if (actionObj.type === 'mute') {
                                    const body = new FormData();
                                    body.append('_token', '{{ csrf_token() }}');
                                    body.append('type', actionObj.notifType);
                                    fetch('/api/notifications/' + actionObj.id + '/turn-off', { method: 'POST', body: body, keepalive: true });
                                    const mCount = actionObj.mutedUnreadCount;
                                    this.unreadCount = Math.max(0, this.unreadCount - mCount);
                                }
                            } catch (e) {}
                        },
                        deleteNotif(id) {
                            let n = this.notificationsData.all.find(x => x.id === id);
                            if (!n) return;
                            if (this.pendingAction) this.flushAction(this.pendingAction);
                            this.pendingAction = { type: 'delete', id: id, wasUnread: !n.read_at, message: 'Đã xóa thông báo này.' };
                            setTimeout(() => this.confirmAction(), 4000);
                        },
                        muteNotif(id) {
                            let n = this.notificationsData.all.find(x => x.id === id);
                            if (!n) return;
                            if (this.pendingAction) this.flushAction(this.pendingAction);
                            let toMute = this.notificationsData.all.filter(x => x.type === n.type);
                            let mutedUnreadCount = toMute.filter(x => !x.read_at).length;
                            this.pendingAction = { type: 'mute', id: id, notifType: n.type, mutedUnreadCount: mutedUnreadCount, message: 'Dừng nhận thông báo về cập nhật này.' };
                            setTimeout(() => this.confirmAction(), 4000);
                        },
                        confirmAction() {
                            if (this.pendingAction) {
                                this.flushAction(this.pendingAction);
                                this.pendingAction = null;
                            }
                        },
                        undoAction() {
                            this.pendingAction = null;
                        },
                        async markAllAsRead() {
                            try {
                                await fetch('{{ route('notifications.markAllAsRead') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                const now = new Date().toISOString();
                                this.notificationsData.all.forEach(n => n.read_at = n.read_at || now);
                                this.notificationsData.unread.forEach(n => n.read_at = n.read_at || now);
                                this.unreadCount = 0;
                            } catch (e) { console.error(e); }
                        },
                        formatMessage(msg, isUnread) {
                            return window.__formatNotifMessage(msg, isUnread);
                        },
                        init() {
                            // Listen for realtime notifications via Reverb WebSocket
                            if (window.Echo) {
                                window.Echo.private('App.Models.User.{{ Auth::id() }}')
                                    .listen('.new.notification', (data) => {
                                        // Prepend to both lists
                                        this.notificationsData.all.unshift(data);
                                        this.notificationsData.unread.unshift(data);
                                        this.unreadCount++;
                                    });
                            }
                        }
                    }" @mousedown.outside="open = false; menuOpen = false">

                        @php
                            $isNotifPage =
                                request()->routeIs('notifications.all') || request()->routeIs('notifications.index');
                        @endphp
                        <button
                            @click="{{ $isNotifPage ? 'open = false' : 'open = !open; if(open) fetchNotifications()' }}"
                            title="Thông báo"
                            class="relative w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 {{ $isNotifPage ? 'bg-[#e7f3ff] text-[#1877f2] cursor-default' : 'border bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300' }}"
                            @if (!$isNotifPage) :class="{
                                'bg-gray-200/70 border-gray-300 text-gray-900': open,
                                'bg-white/10 border-white/20 text-white/80 hover:text-white hover:bg-white/20 hover:border-white/30':
                                    !open && darkHero,
                                'bg-gray-100 border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-200/70 hover:border-gray-300':
                                    !open && !darkHero
                            }" @endif>
                            @if ($isNotifPage)
                                <svg class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                                </svg>
                            @else
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                            <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
                                style="display: none;"
                                class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-sky-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-1"
                                @if ($isNotifPage)
                                style="border: 2px solid #e7f3ff;"
                                @endif></span>
                        </button>

                        {{-- Notification Dropdown Panel --}}
                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                            class="absolute right-[-10px] sm:right-[-60px] lg:right-[-115px] mt-2 w-[calc(100vw-2rem)] max-w-sm sm:w-[360px] bg-white border border-gray-200 rounded-xl shadow-xl z-50 flex flex-col max-h-[90vh] overflow-y-auto overflow-x-hidden custom-scrollbar">

                            {{-- Full Skeleton (Initial Load) --}}
                            <template x-if="initialLoad">
                                <div class="flex flex-col w-full pointer-events-none pb-2">
                                    {{-- Skeleton Header --}}
                                    <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                                        <div class="h-6 w-28 bg-gray-200 rounded animate-pulse"></div>
                                        <div class="w-8 h-8 rounded-full bg-gray-200 animate-pulse"></div>
                                    </div>

                                    {{-- Skeleton Tabs --}}
                                    <div class="px-4 pb-1 flex items-center gap-2">
                                        <div class="h-8 w-16 bg-gray-200 rounded-full animate-pulse"></div>
                                        <div class="h-8 w-20 bg-gray-200 rounded-full animate-pulse"></div>
                                    </div>

                                    {{-- Skeleton Subheader --}}
                                    <div class="px-4 pt-1 pb-1 mt-1">
                                        <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                                    </div>

                                    {{-- Skeleton Items --}}
                                    <div class="flex flex-col">
                                        @for ($i = 0; $i < 4; $i++)
                                            <div class="px-2 mx-2 mb-0.5 py-2.5 flex gap-3">
                                                <div class="w-14 h-14 rounded-full bg-gray-200 shrink-0 animate-pulse">
                                                </div>
                                                <div class="flex-1 space-y-2.5 pt-2">
                                                    <div class="h-3 bg-gray-200 rounded w-full animate-pulse"></div>
                                                    <div class="h-3 bg-gray-200 rounded w-3/4 animate-pulse"></div>
                                                    <div class="h-2.5 bg-gray-100 rounded w-16 mt-1 animate-pulse"></div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </template>

                            {{-- Actual Content Wrapper --}}
                            <template x-if="!initialLoad">
                                <div class="flex flex-col flex-1">
                                    {{-- Header --}}
                                    <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                                        <h3 class="font-bold text-gray-900 text-2xl">Thông báo</h3>
                                        <div class="relative" @mousedown.outside="menuOpen = false">
                                            <button @click="menuOpen = !menuOpen"
                                                class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-600 transition">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                                </svg>
                                            </button>
                                            {{-- 3 dots dropdown --}}
                                            <div x-show="menuOpen" style="display: none;"
                                                class="absolute right-0 mt-1 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1.5 z-50">
                                                <button @click="markAllAsRead(); menuOpen = false"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-sm text-gray-700 font-medium flex items-center gap-3 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Đánh dấu tất cả đã đọc
                                                </button>
                                                <a href="{{ route('settings.index') }}"
                                                    class="block px-4 py-2.5 hover:bg-gray-50 text-sm text-gray-700 font-medium flex items-center gap-3 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Cài đặt thông báo
                                                </a>
                                                <a href="{{ route('notifications.all') }}"
                                                    class="block px-4 py-2.5 hover:bg-gray-50 text-sm text-gray-700 font-medium flex items-center gap-3 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                    Mở thông báo
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tabs --}}
                                    <div class="px-4 pb-1 flex items-center gap-2">
                                        <button @click="tab = 'all'; fetchNotifications()"
                                            class="px-3 py-1.5 rounded-full text-sm font-semibold transition hover:bg-gray-100"
                                            :class="tab === 'all' ? 'bg-sky-100 text-sky-600 hover:bg-sky-200/70' :
                                                'text-gray-900'">
                                            Tất cả
                                        </button>
                                        <button @click="tab = 'unread'; fetchNotifications()"
                                            class="px-3 py-1.5 rounded-full text-sm font-semibold transition hover:bg-gray-100"
                                            :class="tab === 'unread' ? 'bg-sky-100 text-sky-600 hover:bg-sky-200/70' :
                                                'text-gray-900'">
                                            Chưa đọc
                                        </button>
                                    </div>

                                    {{-- Body --}}
                                    <div class="flex-1 pb-2">

                                        {{-- Partial Skeleton Loading (Tab Switch) --}}
                                        <template x-if="loading">
                                            <div class="animate-pulse" style="min-height: 160px">
                                                @for ($i = 0; $i < 3; $i++)
                                                    <div class="px-2 mx-2 mb-0.5 py-2.5 flex gap-3">
                                                        <div class="w-14 h-14 rounded-full bg-gray-200 shrink-0"></div>
                                                        <div class="flex-1 space-y-2.5 pt-2">
                                                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                                                            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                                            <div class="h-2.5 bg-gray-100 rounded w-16 mt-1"></div>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        </template>

                                        {{-- Actual content (hidden while partial loading) --}}
                                        <template x-if="!loading">
                                            <div>
                                                {{-- Empty state --}}
                                                <template x-if="notifications.length === 0">
                                                    <div class="p-8 text-center flex flex-col items-center justify-center"
                                                        style="min-height: 220px">
                                                        <div class="relative mb-5 flex items-center justify-center">
                                                            <svg class="w-20 h-20 transform rotate-[15deg] drop-shadow-sm"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2z"
                                                                    fill="#1877F2" />

                                                                <path
                                                                    d="M18 16v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"
                                                                    fill="#A8ABAF" />
                                                            </svg>
                                                        </div>

                                                        <span class="text-[16px] font-bold text-[#65676B]">
                                                            Bạn không có thông báo nào
                                                        </span>
                                                    </div>
                                                </template>

                                                {{-- Consolidated notifications list --}}
                                                <template x-if="notifications.length > 0">
                                                    <div>
                                                        <template x-if="tab === 'all'">
                                                            <div class="px-4 pt-1 pb-1 flex items-center justify-between">
                                                                <h4 class="font-bold text-gray-900 text-[16px]">Trước đó
                                                                </h4>
                                                                <a href="{{ route('notifications.all') }}"
                                                                    class="text-[14px] text-sky-600 hover:text-sky-700 hover:bg-gray-50 px-2 py-1 rounded-md transition duration-200 font-medium">Xem
                                                                    tất cả</a>
                                                            </div>
                                                        </template>
                                                        <template x-for="item in notifications.slice(0, limit)"
                                                            :key="item.id">
                                                            <div class="relative group transition-all" x-data="{ itemMenuOpen: false, openUp: false }"
                                                                @mousedown.outside="itemMenuOpen = false"
                                                                :class="itemMenuOpen ? 'z-[60]' : 'z-10'">
                                                                <a :href="item.data.url ? item.data.url : '#'"
                                                                    @click.prevent="if(!item.read_at) await markAsRead(item.id); if($event.button === 0 && item.data.url) { window.location.href = item.data.url; }"
                                                                    class="block px-2 mx-2 mb-0.5 py-2.5 rounded-lg cursor-pointer flex gap-3 transition"
                                                                    :class="!item.read_at ? 'bg-sky-50/50 hover:bg-sky-100/70' :
                                                                        'bg-transparent hover:bg-gray-100'">
                                                                    <div class="w-14 h-14 rounded-full shrink-0 relative">
                                                                        {{-- Avatar or fallback icon --}}
                                                                        <template x-if="item.data.avatar">
                                                                            <img :src="item.data.avatar"
                                                                                class="w-14 h-14 rounded-full object-cover border-2"
                                                                                :class="!item.read_at ? 'border-sky-100' :
                                                                                    'border-gray-100'">
                                                                        </template>
                                                                        <template x-if="!item.data.avatar">
                                                                            <div class="w-14 h-14 rounded-full flex items-center justify-center border"
                                                                                :class="!item.read_at ?
                                                                                    'bg-sky-200 border-sky-200 text-sky-600' :
                                                                                    'bg-gray-200 border-gray-200 text-gray-500'">
                                                                                <svg class="w-6 h-6" fill="none"
                                                                                    stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                                </svg>
                                                                            </div>
                                                                        </template>
                                                                        {{-- Badge icon overlay --}}
                                                                        <div class="absolute -bottom-0.5 -right-0.5 w-6 h-6 rounded-full border-2 border-white flex items-center justify-center"
                                                                            :class="!item.read_at ? 'bg-sky-500' : 'bg-gray-400'">
                                                                            <svg class="w-3 h-3 text-white"
                                                                                fill="currentColor" viewBox="0 0 24 24">
                                                                                <path
                                                                                    d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                    <div class="min-w-0 flex-1 pt-1 pb-1">
                                                                        <p class="text-[14px] leading-snug break-words"
                                                                            x-html="formatMessage(item.data.message, !item.read_at)">
                                                                        </p>
                                                                        <p class="text-[13px] mt-1"
                                                                            :class="!item.read_at ? 'text-sky-600 font-bold' :
                                                                                'text-gray-500'"
                                                                            x-text="item.created_at"></p>
                                                                    </div>
                                                                    <div
                                                                        class="flex items-center justify-center shrink-0 w-8 h-8 relative self-center">
                                                                        <div x-show="!item.read_at"
                                                                            class="w-3 h-3 rounded-full bg-sky-500">
                                                                        </div>
                                                                        <button
                                                                            @click.prevent.stop="
                                                                                const rect = $el.getBoundingClientRect();
                                                                                openUp = (window.innerHeight - rect.bottom) < 220;
                                                                                itemMenuOpen = !itemMenuOpen;
                                                                            "
                                                                            title="Tùy chọn"
                                                                            class="absolute inset-0 w-8 h-8 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:bg-gray-50 transition opacity-0 group-hover:opacity-100 z-10"
                                                                            :class="itemMenuOpen ? 'opacity-100 bg-gray-50 border-gray-300' : 'opacity-0'">
                                                                            <svg class="w-5 h-5 text-gray-600"
                                                                                fill="currentColor" viewBox="0 0 24 24">
                                                                                <path
                                                                                    d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </a>

                                                                {{-- Item Actions Dropdown --}}
                                                                <template x-if="itemMenuOpen">
                                                                    <div @click.stop
                                                                        class="absolute right-8 w-64 bg-white border border-gray-100 rounded-xl shadow-2xl z-20 overflow-hidden text-gray-700 font-medium py-2"
                                                                        :class="openUp ? 'bottom-8' : 'top-10'">
                                                                        <div class="flex flex-col">
                                                                            <template x-if="!item.read_at">
                                                                                <button
                                                                                    @click.stop="markAsRead(item.id); itemMenuOpen = false"
                                                                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                                                    <svg class="w-5 h-5 text-gray-900 shrink-0"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24"
                                                                                        stroke-width="2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M5 13l4 4L19 7" />
                                                                                    </svg>
                                                                                    Đánh dấu là đã đọc
                                                                                </button>
                                                                            </template>
                                                                            <template x-if="item.read_at">
                                                                                <button
                                                                                    @click.stop="markAsUnread(item.id); itemMenuOpen = false"
                                                                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                                                    <svg class="w-5 h-5 text-gray-900 shrink-0"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24"
                                                                                        stroke-width="2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                    </svg>
                                                                                    Đánh dấu là chưa đọc
                                                                                </button>
                                                                            </template>
                                                                            <button
                                                                                @click.stop="deleteNotif(item.id); itemMenuOpen = false"
                                                                                class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                                                <svg class="w-5 h-5 shrink-0"
                                                                                    fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24" stroke-width="2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                                </svg>
                                                                                Xóa thông báo này
                                                                            </button>
                                                                            <button
                                                                                @click.stop="muteNotif(item.id); itemMenuOpen = false"
                                                                                class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                                                <svg class="w-5 h-5 shrink-0"
                                                                                    fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24" stroke-width="2">
                                                                                    <rect x="3" y="11" width="18"
                                                                                        height="11" rx="2"
                                                                                        ry="2" />
                                                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                                                                    <path d="M12 15l-2 2h4l-2-2z" />
                                                                                </svg>
                                                                                Tắt các thông báo này
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Nút Xem thêm dưới cùng --}}
                                        <template x-if="notifications.length > limit && tab === 'all'">
                                            <div class="px-3 pb-2 pt-1">
                                                <button @click.prevent="limit += 10"
                                                    class="w-full py-2 bg-gray-200 hover:bg-gray-300 text-center text-[14px] font-bold text-gray-900 rounded-lg transition-colors">
                                                    Xem thông báo trước đó
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Global Snackbar for Pending Actions (Positioned Inside Dropdown or fixed on screen) --}}
                                    <template x-teleport="body">
                                        <div x-show="pendingAction" style="display: none;"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-y-4"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-4"
                                            class="fixed bottom-6 right-6 z-[9999] flex items-center bg-[#323436] text-white px-5 py-3 rounded-xl shadow-2xl min-w-[320px] origin-bottom justify-between">
                                            <span class="text-[15px] font-medium" x-text="pendingAction?.message"></span>
                                            <div class="flex items-center ml-4">
                                                <button @click.prevent="undoAction"
                                                    class="text-sky-400 font-bold hover:bg-white/10 px-3 py-1.5 rounded-lg transition text-[15px]">
                                                    Hoàn tác
                                                </button>
                                                <button @click.prevent="confirmAction"
                                                    class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition ml-1 shrink-0">
                                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Avatar / Profile button --}}
                    <div class="relative" x-data="{ open: false }" @mousedown.outside="open = false">
                        <button @click="open = !open" class="relative group flex items-center justify-center">
                            <div class="relative w-10 h-10 shrink-0">
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-sky-500 to-sky-700 overflow-hidden flex items-center justify-center transition-all duration-300 {{ Auth::user()->activeFrame ? 'scale-[1.0475]' : '' }}">
                                    @if (Auth::user()->avatar)
                                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                @if (Auth::user()->activeFrame)
                                    <img src="{{ Storage::url(Auth::user()->activeFrame->image_path) }}" alt="" 
                                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none drop-shadow-lg z-10 transition-all duration-300">
                                @endif
                            </div>
                        </button>

                        {{-- Profile Dropdown --}}
                        <div x-show="open" x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 sm:-right-2 mt-2 min-w-[260px] max-w-[340px] rounded-2xl bg-white border border-gray-200 shadow-2xl p-3 z-50"
                            style="display:none">

                            {{-- Header user info --}}
                            <div class="mb-2">
                                <div class="flex items-center gap-3 p-2 rounded-xl">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center overflow-hidden shrink-0 shadow-sm border border-black/5">
                                        @if (Auth::user()->avatar)
                                            <img src="{{ Auth::user()->avatar }}" alt=""
                                                class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[15px] font-bold text-white">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex flex-col items-start justify-center text-left">
                                        <p class="text-[15px] font-bold text-gray-900 truncate leading-tight">
                                            {{ Auth::user()->name }}</p>
                                        <p class="mt-[2px] text-[13px] text-gray-600 truncate leading-tight">
                                            {{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-t-[1.5px] border-gray-300 my-2 mx-1">

                            <div class="space-y-1">
                                <a href="{{ route('profile.show', Auth::user()) }}"
                                    class="flex items-center gap-3 px-2 py-2 rounded-xl text-[14px] font-semibold text-gray-900 hover:bg-gray-100 transition-colors group">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 group-hover:bg-sky-100 flex items-center justify-center shrink-0 transition-colors">
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-sky-600 transition-colors" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span>Hồ sơ cá nhân</span>
                                </a>

                                <a href="{{ route('mylist') }}"
                                    class="flex items-center gap-3 px-2 py-2 rounded-xl text-[14px] font-semibold text-gray-900 hover:bg-gray-100 transition-colors group">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 group-hover:bg-emerald-100 flex items-center justify-center shrink-0 transition-colors">
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                    </div>
                                    <span>Danh sách của tôi</span>
                                </a>

                                <a href="{{ route('settings.index') }}"
                                    class="flex items-center gap-3 px-2 py-2 rounded-xl text-[14px] font-semibold text-gray-900 hover:bg-gray-100 transition-colors group">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 group-hover:bg-slate-200 flex items-center justify-center shrink-0 transition-colors">
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-slate-700 transition-colors" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span>Cài đặt</span>
                                </a>

                                @if (Auth::user()->isStaff())
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center gap-3 px-2 py-2 rounded-xl text-[14px] font-semibold text-gray-900 hover:bg-gray-100 transition-colors group mt-1">
                                        <div class="w-9 h-9 rounded-full bg-gray-100 group-hover:bg-red-100 flex items-center justify-center shrink-0 transition-colors">
                                            <svg class="w-5 h-5 text-gray-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <span>Control Panel</span>
                                    </a>
                                @endif



                                <form method="POST" action="{{ route('logout') }}" class="w-full block m-0 mt-1">
                                    @csrf
                                    <button type="submit" class="w-full text-left">
                                        <div class="flex items-center gap-3 px-2 py-2 rounded-xl text-[14px] font-semibold text-gray-900 hover:bg-gray-100 transition-colors group">
                                            <div class="w-9 h-9 rounded-full bg-gray-100 group-hover:bg-red-100 flex items-center justify-center shrink-0 transition-colors">
                                                <svg class="w-5 h-5 text-gray-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                            </div>
                                            <span>Đăng xuất</span>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="hidden md:flex items-center gap-2">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center text-[13px] font-medium px-4 py-2 rounded-full border transition-all duration-300"
                            :class="darkHero
                                ?
                                'border-white/30 text-white/80 hover:bg-white/10 hover:text-white hover:border-white/50' :
                                'border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-400'">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center text-[13px] font-semibold px-4 py-2 rounded-full text-white
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
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('home') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Trang chủ
            </a>
            <a href="{{ route('explore') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('explore') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Phim lẻ
            </a>
            <a href="{{ route('tv-shows.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('tv-shows.*') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                TV Series
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
            <a href="{{ route('person.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('person.*') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Diễn viên
            </a>

            {{-- Mobile auth --}}
            @guest
                <div class="pt-3 border-t border-gray-100 space-y-1">
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">Đăng
                        nhập</a>
                    <a href="{{ route('register') }}"
                        class="block text-center px-3 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-500 hover:bg-sky-600 transition-colors mt-2">Đăng
                        ký</a>
                </div>
            @endguest
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
    abortController: null,
    async search() {
        // Sanitize: allow letters, numbers, spaces, and punctuation
        const cleanQuery = this.query.trim();
        if (cleanQuery.length < 2) {
            this.results = [];
            this.showResults = false;
            return;
        }

        // Cancel previous request if any
        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        this.loading = true;
        try {
            const res = await fetch('{{ route('api.search') }}?q=' + encodeURIComponent(cleanQuery), {
                signal: this.abortController.signal
            });
            this.results = await res.json();
            this.showResults = true;
            this.loading = false;
        } catch (e) {
            if (e.name !== 'AbortError') {
                this.loading = false;
            }
        }
    },
    goToExplore() {
        if (this.query.length > 0) {
            window.location.href = '{{ route('explore') }}?q=' + encodeURIComponent(this.query);
        }
    },
    reset() {
        this.open = false;
        this.query = '';
        this.results = [];
        this.showResults = false;
    }
}" x-on:open-search.window="open = true; $nextTick(() => $refs.searchInput.focus())"
    x-on:keydown.escape.window="if(open) reset()" class="relative z-[100]">

    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="reset()"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm" style="display:none"></div>

    {{-- Search Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="fixed top-[15vh] left-1/2 -translate-x-1/2 w-full max-w-xl px-4" style="display:none">

        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            {{-- Search Input --}}
            <form @submit.prevent="goToExplore()" class="relative">
                <div class="flex items-center px-5 border-b border-gray-100">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input x-ref="searchInput" x-model="query" @input.debounce.300ms="search()" type="text"
                        placeholder="Tìm kiếm phim..." autocomplete="off"
                        class="flex-1 px-4 py-4 text-base text-gray-900 placeholder-gray-400
                                  border-0 focus:outline-none focus:ring-0 bg-transparent">

                    {{-- Loading spinner --}}
                    <div x-show="loading" class="shrink-0">
                        <svg class="animate-spin h-5 w-5 text-sky-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    {{-- Keyboard shortcut hint --}}
                    <kbd x-show="!loading && query.length === 0"
                        class="hidden sm:inline-flex items-center px-2 py-0.5 text-[11px] font-medium text-gray-400 bg-gray-100 rounded-md border border-gray-200">
                        ESC
                    </kbd>
                </div>
            </form>

            {{-- Results --}}
            <div x-show="showResults && results.length > 0" class="max-h-[320px] overflow-y-auto">
                <template x-for="item in results" :key="item.id + '-' + item.title">
                    <a :href="item.url" @click="reset()"
                        class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0 group">
                        <div class="w-10 h-14 bg-gray-100 rounded-lg bg-cover bg-center shrink-0 overflow-hidden"
                            :style="item.poster ? `background-image: url('${item.poster}')` : ''">
                            <template x-if="!item.poster">
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                    </svg>
                                </div>
                            </template>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-sky-600 transition-colors"
                                x-text="item.title"></p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="item.release_year"></p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 shrink-0 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </template>
            </div>

            {{-- No results --}}
            <div x-show="showResults && results.length === 0 && !loading && query.length >= 2"
                class="px-5 py-8 text-center border-t border-gray-50">
                <p class="text-gray-400 text-sm mb-4">Không tìm thấy kết quả cho "<span
                        class="text-gray-600 font-bold" x-text="query"></span>"</p>
                <a :href="'{{ route('explore') }}?q=' + encodeURIComponent(query)" @click="reset()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 text-gray-700 font-medium text-[13px] transition-all">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Tìm kiếm nâng cao
                </a>
            </div>

            {{-- Footer --}}
            <div x-show="showResults && results.length > 0"
                class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">
                    Nhấn <kbd
                        class="px-1.5 py-0.5 bg-white border border-gray-200 shadow-sm rounded text-[10px] font-medium text-gray-600">Enter</kbd>
                    để tìm chi tiết
                </span>

            </div>

            {{-- Empty state hint --}}
            <div x-show="!showResults && query.length === 0"
                class="px-5 py-6 text-center flex flex-col items-center border-t border-gray-50">
                <p class="text-[13px] text-gray-400 mb-3">Nhập tên phim để tìm kiếm nhanh</p>

            </div>
        </div>
    </div>
</div>

<script>
    window.__formatNotifMessage = function(msg, isUnread) {
        var textClass = isUnread ? 'text-gray-900' : 'text-gray-500';
        var nameClass = isUnread ? 'text-gray-900 font-bold' : 'text-gray-500 font-bold';
        var regex = /^(.+?) (đã|vừa)(.*)/i;
        if (regex.test(msg)) {
            return msg.replace(regex, '<span class="' + nameClass + '">$1</span> <span class="' + textClass +
                '"> $2$3</span>');
        }
        return '<span class="' + textClass + '">' + msg + '</span>';
    };
</script>
