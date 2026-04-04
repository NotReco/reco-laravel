<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h1 class="text-3xl font-display font-bold text-white">Thông báo</h1>
                </div>
                
                @if(Auth::user()->unreadNotifications->isNotEmpty())
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST"
                          x-data @submit.prevent="
                            fetch($el.action, { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} })
                            .then(() => window.location.reload())
                          ">
                        <button type="submit" class="text-sm font-medium text-sky-400 hover:text-sky-300 bg-sky-500/10 hover:bg-sky-500/20 px-4 py-2 rounded-xl transition-all">
                            Đánh dấu tất cả đã đọc
                        </button>
                    </form>
                @endif
            </div>

            <div class="card overflow-hidden">
                @if($notifications->isEmpty())
                    <div class="p-12 text-center border-dashed border-2 border-dark-700 bg-transparent m-6 rounded-xl">
                        <div class="w-16 h-16 bg-dark-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-dark-700">
                            <svg class="w-8 h-8 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Trống</h3>
                        <p class="text-dark-400 text-sm">Bạn không có thông báo nào vào lúc này.</p>
                    </div>
                @else
                    <div class="divide-y divide-dark-700/50">
                        @foreach($notifications as $notification)
                            @php
                                $isRead = !is_null($notification->read_at);
                                $data = $notification->data;
                            @endphp
                            <div class="p-5 sm:p-6 transition-colors {{ $isRead ? 'bg-transparent' : 'bg-dark-800/50' }} hover:bg-dark-700/30 flex gap-4 sm:gap-6 relative group"
                                x-data="{
                                    read: {{ $isRead ? 'true' : 'false' }},
                                    async markRead() {
                                        if (this.read) return;
                                        await fetch(`/api/notifications/{{ $notification->id }}/read`, {
                                            method: 'POST',
                                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                                        });
                                        this.read = true;
                                    }
                                }">
                                
                                {{-- Unread indicator --}}
                                <div x-show="!read" class="absolute left-0 top-0 bottom-0 w-1 bg-sky-500 rounded-r"></div>

                                {{-- Icon --}}
                                <div class="w-12 h-12 rounded-full bg-sky-500/10 border border-sky-500/20 text-sky-500 flex items-center justify-center shrink-0">
                                    {!! $data['icon'] ?? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' !!}
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0" @click="markRead(); @if(isset($data['url'])) window.location.href = '{{ $data['url'] }}'; @endif">
                                    <div class="cursor-pointer">
                                        <p class="text-white text-base leading-snug mb-1" :class="read ? 'text-dark-300' : 'text-white font-medium'">
                                            {{ $data['message'] ?? 'Thông báo từ hệ thống.' }}
                                        </p>
                                        <div class="flex items-center gap-3 text-sm text-dark-400 mt-2">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if(!$isRead)
                                                <span x-show="!read" class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($notifications->hasPages())
                        <div class="p-6 border-t border-dark-700/50">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
