<x-app-layout>
    <x-slot:title>{{ $thread->title }} — Diễn đàn</x-slot:title>

{{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    <nav class="flex items-center gap-2 text-sm text-dark-500">
        <a href="{{ route('forum.index') }}" class="hover:text-white transition-colors">Diễn đàn</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('forum.index', ['category' => $thread->category->slug]) }}" class="hover:text-white transition-colors">{{ $thread->category->name }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-dark-400 truncate max-w-[200px]">{{ $thread->title }}</span>
    </nav>
</section>

{{-- ── Thread Content ─────────────────────────────────────────── --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <article class="card p-6 sm:p-8">
        {{-- Header --}}
        <div class="flex items-start gap-4">
            {{-- Author avatar --}}
            <a href="{{ route('profile.show', $thread->user->id) }}" class="shrink-0">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center overflow-hidden ring-2 ring-dark-700 hover:ring-rose-500/50 transition-all">
                    @if($thread->user->avatar)
                        <img src="{{ $thread->user->avatar }}" alt="" class="w-full h-full object-cover">
                    @else
                        <span class="text-sm font-bold text-white">{{ strtoupper(substr($thread->user->name, 0, 1)) }}</span>
                    @endif
                </div>
            </a>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    @if($thread->is_pinned)
                        <span class="badge bg-amber-500/20 text-amber-400">📌 Ghim</span>
                    @endif
                    @if($thread->is_locked)
                        <span class="badge bg-dark-700 text-dark-400">🔒 Khóa</span>
                    @endif
                    <h1 class="text-xl sm:text-2xl font-display font-bold text-white">
                        {{ $thread->title }}
                    </h1>
                </div>
                <div class="flex items-center gap-3 mt-1.5 text-sm text-dark-500">
                    <a href="{{ route('profile.show', $thread->user->id) }}" class="font-medium text-dark-300 hover:text-rose-400 transition-colors">{{ $thread->user->name }}</a>
                    <span>·</span>
                    <span>{{ $thread->created_at->format('d/m/Y H:i') }}</span>
                    <span>·</span>
                    <span>{{ $thread->views_count }} lượt xem</span>
                </div>
            </div>

            {{-- Delete button --}}
            @auth
                @if(auth()->id() === $thread->user_id || auth()->user()->isStaff())
                    <form action="{{ route('forum.destroy', $thread) }}" method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-dark-600 hover:text-red-400 transition-colors p-2" title="Xóa bài viết">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                @endif
            @endauth
        </div>

        {{-- Body --}}
        <div class="mt-6 prose prose-invert prose-sm max-w-none text-dark-200 leading-relaxed whitespace-pre-line">
            {{ $thread->content }}
        </div>
    </article>

    {{-- ── Replies ───────────────────────────────────────────── --}}
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            {{ $replies->total() }} Trả lời
        </h2>

        <div class="space-y-4">
            @forelse($replies as $reply)
                <div class="card p-5">
                    <div class="flex items-start gap-3">
                        <a href="{{ route('profile.show', $reply->user->id) }}" class="shrink-0">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center overflow-hidden ring-2 ring-dark-700">
                                @if($reply->user->avatar)
                                    <img src="{{ $reply->user->avatar }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($reply->user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 text-sm">
                                <a href="{{ route('profile.show', $reply->user->id) }}" class="font-medium text-white hover:text-rose-400 transition-colors">{{ $reply->user->name }}</a>
                                <span class="text-dark-600">·</span>
                                <span class="text-dark-500">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="mt-2 text-sm text-dark-300 leading-relaxed whitespace-pre-line">
                                {{ $reply->content }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-dark-500">
                    Chưa có trả lời nào. Hãy là người đầu tiên!
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $replies->links() }}
        </div>
    </div>

    {{-- ── Reply Form ────────────────────────────────────────── --}}
    @auth
        @if(!$thread->is_locked)
            <div class="mt-8 card p-6">
                <h3 class="text-base font-semibold text-white mb-4">Viết trả lời</h3>
                <form action="{{ route('forum.reply', $thread) }}" method="POST">
                    @csrf
                    <textarea name="content" rows="4" required
                              placeholder="Chia sẻ ý kiến của bạn..."
                              class="input-dark text-sm resize-none">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="btn-rose text-sm py-2.5 px-6">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Gửi trả lời
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="mt-8 card p-6 text-center text-dark-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Bài viết này đã bị khóa, không thể trả lời.
            </div>
        @endif
    @else
        <div class="mt-8 card p-6 text-center">
            <p class="text-dark-400">
                <a href="{{ route('login') }}" class="text-rose-400 hover:text-rose-300 font-medium">Đăng nhập</a>
                để tham gia thảo luận.
            </p>
        </div>
    @endauth
</section>

</x-app-layout>
