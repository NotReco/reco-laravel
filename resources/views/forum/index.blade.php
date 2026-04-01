<x-app-layout>
    <x-slot:title>Diễn đàn</x-slot:title>

{{-- ── Hero Header ───────────────────────────────────────────── --}}
<section class="relative py-12 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-rose-900/20 via-dark-950 to-dark-950"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl lg:text-4xl font-display font-bold text-white">
                    Diễn đàn
                </h1>
                <p class="mt-2 text-dark-400 max-w-xl">
                    Thảo luận, chia sẻ và kết nối với cộng đồng yêu điện ảnh.
                </p>
            </div>
            @auth
                <a href="{{ route('forum.create') }}"
                   class="btn-rose shrink-0 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tạo bài viết
                </a>
            @endauth
        </div>
    </div>
</section>

{{-- ── Main content ──────────────────────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="grid lg:grid-cols-4 gap-8">

        {{-- ══ Sidebar: Categories ═══════════════════════════════ --}}
        <aside class="lg:col-span-1 space-y-3">
            <h3 class="text-sm font-semibold text-dark-400 uppercase tracking-wider mb-3">Chuyên mục</h3>

            <a href="{{ route('forum.index') }}"
               class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all
                      {{ !request('category') ? 'bg-rose-600/20 text-rose-400 border border-rose-500/30' : 'text-dark-300 hover:text-white hover:bg-dark-800 border border-transparent' }}">
                Tất cả
                <span class="text-xs px-2 py-0.5 rounded-full {{ !request('category') ? 'bg-rose-600/30' : 'bg-dark-700' }}">
                    {{ $threads->total() }}
                </span>
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('forum.index', ['category' => $cat->slug]) }}"
                   class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all
                          {{ request('category') === $cat->slug ? 'bg-rose-600/20 text-rose-400 border border-rose-500/30' : 'text-dark-300 hover:text-white hover:bg-dark-800 border border-transparent' }}">
                    {{ $cat->name }}
                    <span class="text-xs px-2 py-0.5 rounded-full {{ request('category') === $cat->slug ? 'bg-rose-600/30' : 'bg-dark-700' }}">
                        {{ $cat->threads_count }}
                    </span>
                </a>
            @endforeach
        </aside>

        {{-- ══ Thread List ═══════════════════════════════════════ --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Search bar --}}
            <form action="{{ route('forum.index') }}" method="GET" class="flex gap-3">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm bài viết..."
                           class="input-dark pl-11 py-2.5 text-sm">
                </div>
                <button type="submit" class="btn-secondary py-2.5 px-5 text-sm">Tìm</button>
            </form>

            @forelse($threads as $thread)
                <a href="{{ route('forum.show', $thread) }}"
                   class="block card-hover p-5 group">
                    <div class="flex items-start gap-4">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-dark-700">
                            @if($thread->user->avatar)
                                <img src="{{ $thread->user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr($thread->user->name, 0, 1)) }}</span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Title + badges --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($thread->is_pinned)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-400">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/></svg>
                                        Ghim
                                    </span>
                                @endif
                                @if($thread->is_locked)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-dark-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Khóa
                                    </span>
                                @endif
                                <h3 class="text-base font-semibold text-white group-hover:text-rose-400 transition-colors truncate">
                                    {{ $thread->title }}
                                </h3>
                            </div>

                            {{-- Meta --}}
                            <div class="flex items-center gap-3 mt-1.5 text-xs text-dark-500">
                                <span class="px-2 py-0.5 rounded-full bg-dark-800 text-dark-400">{{ $thread->category->name }}</span>
                                <span>{{ $thread->user->name }}</span>
                                <span>·</span>
                                <span>{{ $thread->created_at->diffForHumans() }}</span>
                                <span>·</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ $thread->views_count }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    {{ $thread->replies_count }}
                                </span>
                            </div>
                        </div>

                        {{-- Latest reply --}}
                        @if($thread->latestReply)
                            <div class="hidden sm:block text-right shrink-0">
                                <p class="text-xs text-dark-400 truncate max-w-[120px]">{{ $thread->latestReply->user->name ?? '—' }}</p>
                                <p class="text-xs text-dark-600">{{ $thread->latestReply->created_at->diffForHumans() }}</p>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-dark-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-dark-400 text-lg font-medium">Chưa có bài viết nào</p>
                    <p class="text-dark-500 text-sm mt-1">Hãy là người đầu tiên tạo bài viết!</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $threads->links() }}
            </div>
        </div>
    </div>
</section>

</x-app-layout>
