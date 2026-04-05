<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h1 class="text-3xl font-display font-bold text-gray-900">Thông báo</h1>
                </div>
                
                @if(Auth::user()->unreadNotifications->isNotEmpty())
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST"
                          x-data @submit.prevent="
                            fetch($el.action, { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} })
                            .then(() => window.location.reload())
                          ">
                        <button type="submit" class="text-sm font-medium text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 px-4 py-2 rounded-xl transition-all">
                            Đánh dấu tất cả đã đọc
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                @if($notifications->isEmpty())
                    <div class="p-12 text-center border-dashed border-2 border-gray-200 bg-gray-50 m-6 rounded-xl">
                        <div class="w-16 h-16 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Trống</h3>
                        <p class="text-gray-500 text-sm">Bạn không có thông báo nào vào lúc này.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($notifications as $notification)
                            @php
                                $isRead = !is_null($notification->read_at);
                                $data = $notification->data;
                            @endphp
                                <div class="p-5 sm:p-6 transition-colors hover:bg-gray-50 flex gap-4 sm:gap-6 relative group"
                                    :class="!read ? 'bg-sky-50/10' : 'bg-transparent'"
                                    x-data="{
                                        read: {{ $isRead ? 'true' : 'false' }},
                                        msg: '{{ addslashes($data['message'] ?? 'Thông báo từ hệ thống.') }}',
                                        formatMessage() {
                                            return window.__formatNotifMessage(this.msg, !this.read);
                                        },
                                        async markRead() {
                                            if (this.read) return;
                                            await fetch('/api/notifications/{{ $notification->id }}/read', {
                                                method: 'POST',
                                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                                            });
                                            this.read = true;
                                        }
                                    }">

                                    {{-- Icon --}}
                                    <div class="w-12 h-12 rounded-full border flex items-center justify-center shrink-0 transition-colors"
                                         :class="!read ? 'bg-sky-100/50 border-sky-100 text-sky-500' : 'bg-gray-100 border-gray-100 text-gray-500'">
                                    {!! $data['icon'] ?? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' !!}
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0 flex items-center justify-between" @click="await markRead(); @if(isset($data['url'])) window.location.href = '{{ $data['url'] }}'; @endif">
                                    <div class="cursor-pointer pr-4">
                                        <p class="text-[15px] leading-snug mb-1 transition-colors" x-html="formatMessage()"></p>
                                        <div class="flex items-center gap-2 mt-1.5 transition-colors" :class="!read ? 'text-sky-600 font-bold' : 'text-gray-500 text-sm'">
                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    {{-- Actions right side --}}
                                    <div class="flex items-center justify-center shrink-0 w-12 pr-2 relative">
                                        <!-- Nút 3 chấm hiện khi hover -->
                                        <button @click.stop class="opacity-0 group-hover:opacity-100 w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:bg-gray-50 transition absolute right-10 z-10">
                                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                        </button>
                                        <!-- Dấu chấm xanh unread -->
                                        <div x-show="!read" class="w-3.5 h-3.5 rounded-full bg-sky-500 absolute right-1"></div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($notifications->hasPages())
                        <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
