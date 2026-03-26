<x-app-layout>
<x-slot:title>{{ $article->title }}</x-slot:title>

{{-- ── Article Header ──────────────────────────────────── --}}
<article class="max-w-4xl mx-auto px-4 py-10">

    {{-- Tags --}}
    @if($article->tags->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-5">
            @foreach($article->tags as $tag)
                <a href="{{ route('news.index', ['tag' => $tag->slug]) }}"
                   class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg uppercase tracking-wider hover:bg-gray-200 transition-colors">
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Title --}}
    <h1 class="text-3xl md:text-4xl lg:text-[2.75rem] font-extrabold text-gray-900 leading-tight tracking-tight font-outfit">
        {{ $article->title }}
    </h1>

    {{-- Author & Date --}}
    <div class="mt-5 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center shrink-0 ring-2 ring-rose-100">
            @if($article->user->avatar ?? false)
                <img src="{{ $article->user->avatar }}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">
            @else
                <span class="text-sm font-bold text-white">{{ strtoupper(substr($article->user->name ?? '?', 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">{{ $article->user->name ?? 'Ẩn danh' }}</p>
            <p class="text-xs text-gray-400">
                {{ $article->published_at?->format('d/m/Y') }} lúc {{ $article->published_at?->format('H:i') }}
            </p>
        </div>
    </div>

    {{-- Subtitle --}}
    @if($article->subtitle)
        <p class="mt-6 text-xl md:text-2xl font-bold text-gray-700 leading-relaxed">
            {{ $article->subtitle }}
        </p>
    @endif

    {{-- Thumbnail --}}
    @if($article->thumbnail)
        <div class="mt-8 rounded-2xl overflow-hidden border border-gray-100">
            <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}" class="w-full object-cover" loading="lazy">
        </div>
    @endif

    {{-- Content --}}
    <div class="mt-8 prose prose-lg prose-gray max-w-none
                prose-headings:font-outfit prose-headings:text-gray-900
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-a:text-rose-600 prose-a:no-underline hover:prose-a:underline
                prose-img:rounded-xl prose-img:border prose-img:border-gray-100">
        {!! nl2br(e($article->content)) !!}
    </div>

    {{-- Article Footer --}}
    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between text-sm text-gray-400">
        <span>{{ $article->views_count }} lượt xem</span>
        <a href="{{ route('news.index') }}" class="flex items-center gap-1 text-gray-500 hover:text-gray-900 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            Quay lại tin tức
        </a>
    </div>
</article>

{{-- ═══════════════════════════════════════════════════════ --}}
{{--  COMMENTS SECTION                                      --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<section class="bg-gray-50 border-t border-gray-100">
    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- Comment Count Badge (góc trên bên trái) --}}
        <div class="flex items-center gap-3 mb-8">
            <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-sm font-bold text-gray-900">{{ $commentsCount }}</span>
                <span class="text-sm text-gray-500">bình luận</span>
            </div>
        </div>

        @auth
            {{-- ── Comment Form ────────────────────────────── --}}
            <form action="{{ route('article-comments.store') }}" method="POST" class="mb-8">
                @csrf
                <input type="hidden" name="article_id" value="{{ $article->id }}">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">
                        @else
                            <span class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <textarea name="content" rows="3" required maxlength="1000"
                                  placeholder="Viết bình luận của bạn..."
                                  class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400
                                         focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-400 transition-all resize-none"></textarea>
                        @error('content')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex justify-end">
                            <button type="submit"
                                    class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-xl
                                           hover:bg-gray-800 active:scale-[0.97] transition-all">
                                Gửi bình luận
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ── Comments List ───────────────────────────── --}}
            <div class="space-y-5">
                @forelse($article->comments as $comment)
                    <div class="flex gap-3" id="comment-{{ $comment->id }}">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center shrink-0">
                            @if($comment->user->avatar ?? false)
                                <img src="{{ $comment->user->avatar }}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">
                            @else
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="bg-white rounded-xl px-4 py-3 border border-gray-100">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name ?? 'Ẩn danh' }}</span>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->is_edited)
                                        <span class="text-[10px] text-gray-400 italic">(đã chỉnh sửa)</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">{{ $comment->content }}</p>
                            </div>

                            {{-- Actions --}}
                            @if(auth()->id() === $comment->user_id || auth()->user()->isStaff())
                                <div class="mt-1.5 flex items-center gap-3 pl-4">
                                    @if(auth()->id() === $comment->user_id)
                                        <button onclick="document.getElementById('edit-form-{{ $comment->id }}').classList.toggle('hidden')"
                                                class="text-xs text-gray-400 hover:text-gray-700 transition-colors">Sửa</button>
                                    @endif
                                    <form action="{{ route('article-comments.destroy', $comment) }}" method="POST"
                                          onsubmit="return confirm('Xóa bình luận này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition-colors">Xóa</button>
                                    </form>
                                </div>

                                {{-- Inline Edit Form --}}
                                @if(auth()->id() === $comment->user_id)
                                    <form id="edit-form-{{ $comment->id }}"
                                          action="{{ route('article-comments.update', $comment) }}" method="POST"
                                          class="hidden mt-2">
                                        @csrf @method('PUT')
                                        <textarea name="content" rows="2" required maxlength="1000"
                                                  class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-800
                                                         focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-400 transition-all resize-none">{{ $comment->content }}</textarea>
                                        <div class="mt-1.5 flex gap-2 justify-end">
                                            <button type="button"
                                                    onclick="this.closest('form').classList.add('hidden')"
                                                    class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">Hủy</button>
                                            <button type="submit"
                                                    class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors">Lưu</button>
                                        </div>
                                    </form>
                                @endif
                            @endif

                            {{-- Replies --}}
                            @if($comment->replies->isNotEmpty())
                                <div class="mt-3 ml-2 pl-4 border-l-2 border-gray-200 space-y-3">
                                    @foreach($comment->replies as $reply)
                                        <div class="flex gap-2.5" id="comment-{{ $reply->id }}">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shrink-0">
                                                <span class="text-[10px] font-bold text-white">{{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                                                    <div class="flex items-center gap-2 mb-0.5">
                                                        <span class="text-xs font-semibold text-gray-900">{{ $reply->user->name ?? 'Ẩn danh' }}</span>
                                                        <span class="text-[11px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line break-words">{{ $reply->content }}</p>
                                                </div>

                                                @if(auth()->id() === $reply->user_id || auth()->user()->isStaff())
                                                    <div class="mt-1 flex items-center gap-2 pl-3">
                                                        <form action="{{ route('article-comments.destroy', $reply) }}" method="POST"
                                                              onsubmit="return confirm('Xóa bình luận này?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-[11px] text-gray-400 hover:text-red-500 transition-colors">Xóa</button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Reply Form --}}
                            <div x-data="{ showReply: false }" class="mt-2 pl-4">
                                <button @click="showReply = !showReply" class="text-xs text-gray-400 hover:text-gray-700 transition-colors">
                                    Trả lời
                                </button>
                                <form x-show="showReply" x-transition
                                      action="{{ route('article-comments.store') }}" method="POST"
                                      class="mt-2">
                                    @csrf
                                    <input type="hidden" name="article_id" value="{{ $article->id }}">
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <textarea name="content" rows="2" required maxlength="1000"
                                              placeholder="Trả lời {{ $comment->user->name ?? '' }}..."
                                              class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-800
                                                     focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-400 transition-all resize-none"></textarea>
                                    <div class="mt-1.5 flex gap-2 justify-end">
                                        <button type="button" @click="showReply = false"
                                                class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">Hủy</button>
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors">Gửi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-400 text-sm">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                    </div>
                @endforelse
            </div>

        @else
            {{-- ── Guest: Prompt to Login ──────────────────── --}}
            <div class="text-center py-12 bg-white rounded-2xl border border-gray-100">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-gray-600 font-medium mb-1">Đăng nhập để xem và viết bình luận</p>
                <p class="text-sm text-gray-400 mb-5">Tham gia thảo luận cùng cộng đồng</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl
                          hover:bg-gray-800 active:scale-[0.97] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Đăng nhập
                </a>
            </div>
        @endauth

    </div>
</section>

</x-app-layout>
