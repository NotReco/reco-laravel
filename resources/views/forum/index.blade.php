<x-app-layout>
    <x-slot:title>Diễn đàn</x-slot:title>

{{-- ── Main content ──────────────────────────────────────────── --}}
<div class="bg-gray-50 min-h-screen pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl lg:text-3xl font-display font-bold text-gray-900">
                    Diễn đàn
                </h1>
                <p class="mt-1.5 text-gray-500 text-sm max-w-xl">
                    Thảo luận, chia sẻ và kết nối với cộng đồng yêu điện ảnh.
                </p>
            </div>
            @auth
                <a href="{{ route('forum.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-full
                          hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-sky-200/50 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tạo bài viết
                </a>
            @endauth
        </div>

        <div class="grid lg:grid-cols-4 gap-8">

            {{-- ══ Sidebar: Categories ═══════════════════════════════ --}}
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Chuyên mục</h3>
                    </div>
                    <div class="p-2 space-y-1">
                        <a href="{{ route('forum.index') }}"
                           class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                                  {{ !request('category') ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Tất cả
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold
                                         {{ !request('category') ? 'bg-sky-100 text-sky-600' : 'bg-gray-100 text-gray-500' }}">
                                {{ $threads->total() }}
                            </span>
                        </a>

                        @foreach($categories as $cat)
                            <a href="{{ route('forum.index', ['category' => $cat->slug]) }}"
                               class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                                      {{ request('category') === $cat->slug ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                {{ $cat->name }}
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold
                                             {{ request('category') === $cat->slug ? 'bg-sky-100 text-sky-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $cat->threads_count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- ══ Thread List ═══════════════════════════════════════ --}}
            <div class="lg:col-span-3 space-y-4">

                {{-- Search bar --}}
                <form action="{{ route('forum.index') }}" method="GET" class="flex gap-3">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm bài viết..."
                               class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition-all shadow-sm">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl
                                   hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        Tìm
                    </button>
                </form>

                @forelse($threads as $thread)
                    <a href="{{ route('forum.show', $thread) }}"
                       class="block bg-white rounded-2xl border border-gray-200 shadow-sm p-5 group
                              hover:shadow-md hover:border-gray-300 transition-all duration-200">
                        <div class="flex items-start gap-4">
                            {{-- Avatar --}}
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-white shadow-sm">
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
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                            📌 Ghim
                                        </span>
                                    @endif
                                    @if($thread->is_locked)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                            🔒 Khóa
                                        </span>
                                    @endif
                                    <h3 class="text-base font-semibold text-gray-900 group-hover:text-sky-600 transition-colors truncate">
                                        {{ $thread->title }}
                                    </h3>
                                </div>

                                {{-- Meta --}}
                                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500">
                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">{{ $thread->category->name }}</span>
                                    <span class="font-medium text-gray-700">{{ $thread->user->name }}</span>
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
                                    <p class="text-xs text-gray-600 truncate max-w-[120px] font-medium">{{ $thread->latestReply->user->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $thread->latestReply->created_at->diffForHumans() }}</p>
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-gray-600 text-lg font-medium">Chưa có bài viết nào</p>
                        <p class="text-gray-400 text-sm mt-1">Hãy là người đầu tiên tạo bài viết!</p>
                    </div>
                @endforelse

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $threads->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>
