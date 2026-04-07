<x-app-layout>
    <x-slot name="title">Thông báo</x-slot>
    <div class="py-8" x-data="{
        tab: '{{ request('filter') === 'unread' ? 'unread' : 'all' }}',
        loading: true,
        notificationsData: { all: [], unread: [] },
        unreadCount: 0,
        limits: { all: 15, unread: 15 },
        pendingAction: null,
        get limit() { return this.limits[this.tab]; },
        set limit(val) { this.limits[this.tab] = val; },
        get notifications() {
            const list = this.notificationsData[this.tab] || [];
            let filtered = list.filter(n => n.id !== (this.pendingAction ? this.pendingAction.id : null));
            return this.tab === 'unread' ? filtered.filter(n => !n.read_at) : filtered;
        },
        async fetchNotifications() {
            this.loading = true;
            try {
                const res = await fetch('/api/notifications?filter=' + this.tab);
                const data = await res.json();
                this.notificationsData[this.tab] = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (e) { console.error('Error fetching notifications:', e); }
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
                if (foundInUnread && foundInUnread.read_at) foundInUnread.read_at = null;
            } catch (e) {}
        },
        async flushAction(actionObj) {
            try {
                const url = actionObj.type === 'delete' ?
                    '/api/notifications/' + actionObj.id :
                    '/api/notifications/' + actionObj.id + '/turn-off';
                await fetch(url, {
                    method: actionObj.type === 'delete' ? 'DELETE' : 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                this.notificationsData.all = this.notificationsData.all.filter(n => n.id !== actionObj.id);
                this.notificationsData.unread = this.notificationsData.unread.filter(n => n.id !== actionObj.id);
            } catch (e) {}
        },
        deleteNotif(id) {
            if (this.pendingAction) {
                this.flushAction(this.pendingAction);
            }
            this.pendingAction = { id: id, type: 'delete' };
        },
        muteNotif(id) {
            if (this.pendingAction) {
                this.flushAction(this.pendingAction);
            }
            this.pendingAction = { id: id, type: 'mute' };
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
                await fetch('/api/notifications/read-all', {
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
            return window.__formatNotifMessage ? window.__formatNotifMessage(msg, isUnread) : msg;
        },
        init() {
            this.fetchNotifications();
            if (window.Echo) {
                window.Echo.private('App.Models.User.{{ Auth::id() }}')
                    .listen('.new.notification', (data) => {
                        this.notificationsData.all.unshift(data);
                        this.notificationsData.unread.unshift(data);
                        this.unreadCount++;
                    });
            }
            window.addEventListener('beforeunload', () => {
                if (this.pendingAction) {
                    const method = this.pendingAction.type === 'delete' ? 'DELETE' : 'POST';
                    const url = this.pendingAction.type === 'delete' ?
                        '/api/notifications/' + this.pendingAction.id :
                        '/api/notifications/' + this.pendingAction.id + '/turn-off';
                    navigator.sendBeacon(url, new Blob([new URLSearchParams({
                        '_method': method,
                        '_token': '{{ csrf_token() }}'
                    })], { type: 'application/x-www-form-urlencoded' }));
                }
            });
        }
    }">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col pb-4 mb-8">
                {{-- Header --}}
                <div class="px-4 pt-4 pb-2 flex items-center justify-between z-10">
                    <h3 class="font-bold text-gray-900 text-2xl">Thông báo</h3>

                    <div class="relative" x-data="{ menuOpen: false }" @mousedown.outside="menuOpen = false">
                        <button @click="menuOpen = !menuOpen"
                            class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-600 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                            </svg>
                        </button>

                        {{-- 3 dots dropdown menu --}}
                        <div x-show="menuOpen" style="display: none;"
                            class="absolute right-0 mt-1 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1.5 z-50">
                            <button @click="markAllAsRead(); menuOpen = false"
                                class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-sm text-gray-700 font-medium flex items-center gap-3 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Đánh dấu tất cả đã đọc
                            </button>
                            <a href="{{ route('settings.index') }}"
                                class="block px-4 py-2.5 hover:bg-gray-50 text-sm text-gray-700 font-medium flex items-center gap-3 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Cài đặt thông báo
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="px-4 pb-1 flex items-center gap-2 z-10">
                    <button @click="tab = 'all'; fetchNotifications()"
                        class="px-3 py-1.5 rounded-full text-sm font-semibold transition hover:bg-gray-100"
                        :class="tab === 'all' ? 'bg-sky-100 text-sky-600 hover:bg-sky-200/70' : 'text-gray-900'">
                        Tất cả
                    </button>
                    <button @click="tab = 'unread'; fetchNotifications()"
                        class="px-3 py-1.5 rounded-full text-sm font-semibold transition hover:bg-gray-100"
                        :class="tab === 'unread' ? 'bg-sky-100 text-sky-600 hover:bg-sky-200/70' : 'text-gray-900'">
                        Chưa đọc
                    </button>
                </div>

                {{-- Body Container --}}
                <div class="flex-1 pb-2">

                    {{-- Skeleton Loading --}}
                    <template x-if="loading">
                        <div class="animate-pulse py-2">
                            @for ($i = 0; $i < 4; $i++)
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

                    <template x-if="!loading">
                        <div>
                            {{-- Empty state --}}
                            <template x-if="notifications.length === 0">
                                <div class="py-12 text-center flex flex-col items-center justify-center">
                                    <div class="relative mb-5 flex items-center justify-center">
                                        <svg class="w-20 h-20 transform rotate-[15deg] drop-shadow-sm"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2z" fill="#1877F2" />
                                            <path
                                                d="M18 16v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"
                                                fill="#A8ABAF" />
                                        </svg>
                                    </div>
                                    <span class="text-[16px] font-bold text-[#65676B]">Bạn không có thông báo nào</span>
                                </div>
                            </template>

                            {{-- Loaded Items --}}
                            <template x-if="notifications.length > 0">
                                <div>
                                    <template x-if="tab === 'all'">
                                        <div class="px-4 pt-1 pb-1 flex items-center justify-between">
                                            <h4 class="font-bold text-gray-900 text-[16px]">Trước đó</h4>
                                        </div>
                                    </template>
                                    <template x-if="tab === 'unread'">
                                        <div class="px-4 pt-1 pb-1"></div>
                                    </template>

                                    <template x-for="item in notifications.slice(0, limit)" :key="item.id">
                                        <div class="relative group" x-data="{ itemMenuOpen: false }"
                                            @mousedown.outside="itemMenuOpen = false">
                                            <a :href="item.data.url ? item.data.url : '#'"
                                                @click.prevent="if(!item.read_at) markAsRead(item.id); if($event.button === 0 && item.data.url) { window.location.href = item.data.url; }"
                                                class="block px-2 mx-2 mb-0.5 py-2.5 rounded-lg cursor-pointer flex gap-3 transition"
                                                :class="!item.read_at ? 'bg-sky-50/50 hover:bg-sky-100/70' :
                                                    'bg-transparent hover:bg-gray-100'">

                                                {{-- Avatar --}}
                                                <div class="w-14 h-14 rounded-full shrink-0 relative">
                                                    <template x-if="item.data.avatar">
                                                        <img :src="item.data.avatar"
                                                            class="w-14 h-14 rounded-full object-cover border-2"
                                                            :class="!item.read_at ? 'border-sky-100' : 'border-gray-100'">
                                                    </template>
                                                    <template x-if="!item.data.avatar">
                                                        <div class="w-14 h-14 rounded-full flex items-center justify-center border"
                                                            :class="!item.read_at ? 'bg-sky-200 border-sky-200 text-sky-600' :
                                                                'bg-gray-200 border-gray-200 text-gray-500'">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                    </template>
                                                    <div class="absolute -bottom-0.5 -right-0.5 w-6 h-6 rounded-full border-2 border-white flex items-center justify-center"
                                                        :class="!item.read_at ? 'bg-sky-500' : 'bg-gray-400'">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                {{-- Text Content --}}
                                                <div class="min-w-0 flex-1 pt-1 pb-1">
                                                    <p class="text-[14px] leading-snug break-words"
                                                        x-html="formatMessage(item.data.message, !item.read_at)"></p>
                                                    <p class="text-[13px] mt-1"
                                                        :class="!item.read_at ? 'text-sky-600 font-bold' : 'text-gray-500'"
                                                        x-text="item.created_at"></p>
                                                </div>

                                                {{-- Extra Space to prevent hover clash --}}
                                                <div class="w-12 shrink-0"></div>
                                            </a>

                                            {{-- Absolute Overlay for Dots/Badge --}}
                                            <div
                                                class="absolute top-1/2 right-4 -translate-y-1/2 flex items-center justify-center z-10 gap-2">
                                                <button @click.stop.prevent="itemMenuOpen = !itemMenuOpen"
                                                    class="w-8 h-8 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:bg-gray-50 transition opacity-0 group-hover:opacity-100 focus:opacity-100"
                                                    :class="{ 'opacity-100': itemMenuOpen }">
                                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                                    </svg>
                                                </button>
                                                <div x-show="!item.read_at"
                                                    class="w-3 h-3 rounded-full bg-sky-500 shrink-0"></div>
                                            </div>

                                            {{-- Item Menu Dropdown --}}
                                            <div x-show="itemMenuOpen" style="display: none;"
                                                class="absolute right-8 top-12 w-[300px] sm:w-[350px] bg-white border border-gray-200 rounded-xl shadow-xl py-2 z-50">
                                                <template x-if="item.read_at">
                                                    <button @click.stop="markAsUnread(item.id); itemMenuOpen = false"
                                                        class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                        <svg class="w-5 h-5 auto shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M20 6L9 17l-5-5" />
                                                        </svg>
                                                        Đánh dấu là chưa đọc
                                                    </button>
                                                </template>
                                                <template x-if="!item.read_at">
                                                    <button @click.stop="markAsRead(item.id); itemMenuOpen = false"
                                                        class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                        <svg class="w-5 h-5 shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M20 6L9 17l-5-5" />
                                                        </svg>
                                                        Đánh dấu là đã đọc
                                                    </button>
                                                </template>
                                                <button @click.stop="deleteNotif(item.id); itemMenuOpen = false"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                    <svg class="w-5 h-5 shrink-0 border-2 border-gray-900 rounded-md p-0.5"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Xóa thông báo này
                                                </button>
                                                <button @click.stop="muteNotif(item.id); itemMenuOpen = false"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-[15px] text-gray-900 font-semibold flex items-center gap-3 transition">
                                                    <svg class="w-5 h-5 shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="11" width="18" height="11"
                                                            rx="2" ry="2" />
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

                    {{-- Load More button --}}
                    <template x-if="!loading && notifications.length > limit">
                        <div class="px-3 py-3">
                            <button @click.prevent="limit += 15"
                                class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-center text-[15px] font-bold text-gray-900 rounded-lg transition-colors">
                                Xem thông báo trước đó
                            </button>
                        </div>
                    </template>

                </div>
            </div>
        </div>

        {{-- Snackbar for Undo Delete/Mute --}}
        <div x-show="pendingAction" style="display: none;"
            class="fixed bottom-6 left-6 z-[60] flex items-center bg-[#323436] text-white px-4 py-3 rounded-xl shadow-xl min-w-[300px]">

            <span class="text-[15px] mr-auto font-medium"
                x-text="pendingAction && pendingAction.type === 'delete' ? 'Đã xóa thông báo.' : 'Bạn sẽ không nhận được thông báo tương tự nữa.'"></span>

            <button @click.prevent="undoAction"
                class="text-sky-400 font-semibold text-[15px] px-3 ml-2 hover:underline shrink-0">
                Hoàn tác
            </button>
            <button @click.prevent="confirmAction"
                class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition ml-1 shrink-0">
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</x-app-layout>
