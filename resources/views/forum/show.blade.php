<x-app-layout>
    <x-slot:title>{{ $thread->title }} — Diễn đàn</x-slot:title>

<div class="bg-gray-50 min-h-screen pb-12">

    {{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('forum.index') }}" class="hover:text-gray-900 transition-colors">Diễn đàn</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('forum.index', ['category' => $thread->category->slug]) }}" class="hover:text-gray-900 transition-colors">{{ $thread->category->name }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 truncate max-w-[200px] font-medium">{{ $thread->title }}</span>
        </nav>
    </div>

    {{-- ── Thread Content ─────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <article class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
            {{-- Header --}}
            <div class="flex items-start gap-4">
                {{-- Author avatar --}}
                <a href="{{ route('profile.show', $thread->user->id) }}" class="shrink-0">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center overflow-hidden ring-2 ring-white shadow-sm hover:ring-sky-200 transition-all">
                        @if($thread->user->avatar)
                            <img src="{{ $thread->user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <span class="text-sm font-bold text-white">{{ strtoupper(substr($thread->user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </a>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($thread->is_pinned)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full">📌 Ghim</span>
                        @endif
                        @if($thread->is_locked)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">🔒 Khóa</span>
                        @endif
                        <h1 class="text-xl sm:text-2xl font-display font-bold text-gray-900">
                            {{ $thread->title }}
                        </h1>
                    </div>
                    <div class="flex items-center gap-3 mt-1.5 text-sm text-gray-500">
                        <a href="{{ route('profile.show', $thread->user->id) }}" class="font-medium text-gray-700 hover:text-sky-600 transition-colors">{{ $thread->user->name }}</a>
                        @if($thread->user->activeTitle)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold border"
                                  style="color: {{ $thread->user->activeTitle->color_hex }}; border-color: {{ $thread->user->activeTitle->color_hex }}40; background-color: {{ $thread->user->activeTitle->color_hex }}15;">
                                {{ $thread->user->activeTitle->name }}
                            </span>
                        @endif
                        <span>·</span>
                        <span>{{ $thread->created_at->format('d/m/Y H:i') }}</span>
                        <span>·</span>
                        <span>{{ $thread->views_count }} lượt xem</span>
                    </div>
                </div>

                {{-- Actions (Edit / Delete) --}}
                @auth
                    @if(auth()->id() === $thread->user_id || auth()->user()->isStaff())
                        <div class="flex items-center gap-1 shrink-0">
                            @if(auth()->id() === $thread->user_id)
                                <a href="{{ route('forum.editThread', $thread) }}"
                                   class="text-gray-400 hover:text-sky-500 transition-colors p-2 rounded-lg hover:bg-sky-50" title="Sửa bài viết">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                            @endif
                            <form action="{{ route('forum.destroy', $thread) }}" method="POST"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50" title="Xóa bài viết">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            {{-- Body --}}
            <div class="mt-6 prose prose-sm prose-gray max-w-none text-gray-700 leading-relaxed">
                {!! $thread->content !!}
            </div>
        </article>

        {{-- ── Replies ───────────────────────────────────────────── --}}
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ $replies->total() }} Trả lời
            </h2>

            <div class="space-y-4">
                @forelse($replies as $reply)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                        <div class="flex items-start gap-3">
                            <a href="{{ route('profile.show', $reply->user->id) }}" class="shrink-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center overflow-hidden ring-2 ring-white shadow-sm">
                                    @if($reply->user->avatar)
                                        <img src="{{ $reply->user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr($reply->user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                            </a>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm">
                                        <a href="{{ route('profile.show', $reply->user->id) }}" class="font-semibold text-gray-900 hover:text-sky-600 transition-colors">{{ $reply->user->name }}</a>
                                        @if($reply->user->activeTitle)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold border"
                                                  style="color: {{ $reply->user->activeTitle->color_hex }}; border-color: {{ $reply->user->activeTitle->color_hex }}40; background-color: {{ $reply->user->activeTitle->color_hex }}15;">
                                                {{ $reply->user->activeTitle->name }}
                                            </span>
                                        @endif
                                        <span class="text-gray-300">·</span>
                                        <span class="text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    @auth
                                        @if(auth()->id() === $reply->user_id || auth()->user()->isStaff())
                                            <div class="flex items-center gap-1">
                                                @if(auth()->id() === $reply->user_id)
                                                    <a href="{{ route('forum.editReply', $reply) }}"
                                                       class="text-gray-400 hover:text-sky-500 transition-colors p-1.5 rounded-lg hover:bg-sky-50" title="Sửa">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <form action="{{ route('forum.destroyReply', $reply) }}" method="POST"
                                                      onsubmit="return confirm('Bạn có chắc muốn xóa phản hồi này?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50" title="Xóa">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                                <div class="mt-2 text-sm text-gray-700 leading-relaxed prose prose-sm prose-gray max-w-none">
                                    {!! $reply->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
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
                <div class="mt-8 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Viết trả lời</h3>
                    <form action="{{ route('forum.reply', $thread) }}" method="POST">
                        @csrf
                        <textarea name="content" rows="4" required
                              class="js-richtext-simple w-full"
                              data-richtext-height="200"
                              placeholder="Chia sẻ ý kiến của bạn...">{!! old('content') !!}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 flex justify-end">
                            <button type="submit"
                                    class="px-6 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl
                                           hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md">
                                Gửi trả lời
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-8 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center text-gray-500">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Bài viết này đã bị khóa, không thể trả lời.
                </div>
            @endif
        @else
            <div class="mt-8 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center">
                <p class="text-gray-500">
                    <a href="{{ route('login') }}" class="text-sky-500 hover:text-sky-600 font-medium">Đăng nhập</a>
                    để tham gia thảo luận.
                </p>
            </div>
        @endauth
    </div>
</div>

@include('partials.tinymce-simple')
</x-app-layout>
