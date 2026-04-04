<x-app-layout>
    <x-slot:title>Tin nhắn</x-slot:title>

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-6">Tin nhắn</h1>

    <div class="card overflow-hidden"
         style="height: calc(100vh - 200px); min-height: 500px;"
         x-data="{ mobileShowChat: {{ isset($partner) ? 'true' : 'false' }} }">
        <div class="flex h-full">

            {{-- ══ SIDEBAR: Conversation List ═══════════════════════════ --}}
            <div class="w-80 border-r border-dark-700 flex flex-col shrink-0"
                 :class="mobileShowChat ? 'hidden lg:flex' : 'flex w-full lg:w-80'">
                <div class="p-4 border-b border-dark-700">
                    <h2 class="text-sm font-semibold text-dark-400 uppercase tracking-wider">Hội thoại</h2>
                </div>

                <div class="flex-1 overflow-y-auto scrollbar-hide">
                    @forelse($partners as $conv)
                        <a href="{{ route('messages.show', $conv->user->id) }}"
                           class="flex items-center gap-3 px-4 py-3 border-b border-dark-800 hover:bg-dark-800/50 transition-colors
                                  {{ isset($partner) && $partner->id === $conv->user->id ? 'bg-dark-800 border-l-2 border-l-sky-500' : '' }}">
                            {{-- Avatar --}}
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                                @if($conv->user->avatar)
                                    <img src="{{ $conv->user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($conv->user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-white truncate">{{ $conv->user->name }}</p>
                                    @if($conv->unread_count > 0)
                                        <span class="w-5 h-5 bg-sky-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0">{{ $conv->unread_count }}</span>
                                    @endif
                                </div>
                                @if($conv->last_message)
                                    <p class="text-xs text-dark-500 truncate mt-0.5">
                                        {{ $conv->last_message->sender_id === Auth::id() ? 'Bạn: ' : '' }}{{ Str::limit($conv->last_message->content, 40) }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-dark-500 text-sm">
                            Chưa có cuộc hội thoại nào.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ══ CHAT PANEL ════════════════════════════════════════ --}}
            <div class="flex-1 flex flex-col"
                 :class="mobileShowChat ? 'flex' : 'hidden lg:flex'">
                @isset($partner)
                    {{-- Chat header --}}
                    <div class="p-4 border-b border-dark-700 flex items-center gap-3">
                        <button @click="mobileShowChat = false" class="lg:hidden text-dark-400 hover:text-white transition-colors mr-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <a href="{{ route('profile.show', $partner->id) }}" class="flex items-center gap-3 group">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center overflow-hidden ring-2 ring-dark-700">
                                @if($partner->avatar)
                                    <img src="{{ $partner->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($partner->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <span class="font-semibold text-white group-hover:text-sky-400 transition-colors">{{ $partner->name }}</span>
                        </a>
                    </div>

                    {{-- Messages --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-3 scrollbar-hide" id="chat-messages">
                        @foreach($messages as $msg)
                            @php $isMine = $msg->sender_id === Auth::id(); @endphp
                            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%] {{ $isMine ? 'bg-sky-600/80 text-white' : 'bg-dark-800 text-dark-200' }} rounded-2xl px-4 py-2.5 text-sm
                                            {{ $isMine ? 'rounded-br-md' : 'rounded-bl-md' }}">
                                    <p class="leading-relaxed whitespace-pre-line">{{ $msg->content }}</p>
                                    <p class="text-[10px] mt-1 {{ $isMine ? 'text-sky-200' : 'text-dark-500' }}">
                                        {{ $msg->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Input --}}
                    <div class="p-4 border-t border-dark-700">
                        <form action="{{ route('messages.store') }}" method="POST" class="flex gap-3">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $partner->id }}">
                            <input type="text" name="content" placeholder="Nhập tin nhắn..." required
                                   class="input-dark text-sm flex-1 py-2.5" autocomplete="off">
                            <button type="submit" class="btn-sky py-2.5 px-5 text-sm shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Empty state --}}
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-dark-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-dark-500">Chọn một cuộc hội thoại để bắt đầu</p>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</section>

{{-- Auto scroll to bottom --}}
@isset($partner)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
        });
    </script>
@endisset

</x-app-layout>
