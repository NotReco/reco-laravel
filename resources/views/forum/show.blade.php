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
        <article class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8"
                 x-data="{
                     liked: {{ auth()->check() ? ($thread->likes->contains('user_id', auth()->id()) ? 'true' : 'false') : 'false' }},
                     likesCount: {{ $thread->likes->count() }},
                     isLiking: false,
                     toggleLike() {
                         if (!{{ auth()->check() ? 'true' : 'false' }}) {
                             window.location.href = '{{ route('login') }}';
                             return;
                         }
                         if (this.isLiking) return;
                         this.isLiking = true;
                         fetch('{{ route('forum.thread.like', $thread) }}', {
                             method: 'POST',
                             headers: {
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                 'Accept': 'application/json',
                                 'Content-Type': 'application/json'
                             }
                         })
                         .then(response => response.json())
                         .then(data => {
                             this.liked = data.isLiked;
                             this.likesCount = data.likesCount;
                         })
                         .finally(() => {
                             this.isLiking = false;
                         });
                     }
                 }">
            {{-- Header --}}
            <div class="flex items-start gap-4">
                {{-- Author avatar --}}
                <a href="{{ route('profile.show', $thread->user) }}" class="w-12 h-12 shrink-0 relative group">
                    <div class="w-full h-full rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center overflow-hidden transition-all duration-300 {{ $thread->user->activeFrame ? 'scale-[1.0475]' : 'ring-2 ring-white shadow-sm group-hover:ring-sky-200' }}">
                        @if($thread->user->avatar)
                            <img src="{{ $thread->user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <span class="text-sm font-bold text-white">{{ mb_strtoupper(mb_substr($thread->user->name, 0, 1, 'UTF-8'), 'UTF-8') }}</span>
                        @endif
                    </div>
                    @if($thread->user->activeFrame)
                        <img src="{{ Storage::url($thread->user->activeFrame->image_path) }}" alt="" 
                             class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                    @endif
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
                        <a href="{{ route('profile.show', $thread->user) }}" class="font-medium text-gray-700 hover:text-sky-600 transition-colors">{{ $thread->user->name }}</a>
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

                {{-- Actions (Edit / Delete / Pin / Lock) --}}
                @auth
                    @if(auth()->id() === $thread->user_id || auth()->user()->isStaff())
                        <div class="flex items-center gap-1 shrink-0">
                            @if(auth()->user()->isStaff())
                                {{-- Nút Ghim --}}
                                <form action="{{ route('forum.thread.togglePin', $thread) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2 rounded-lg transition-colors {{ $thread->is_pinned ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' }}"
                                            title="{{ $thread->is_pinned ? 'Bỏ ghim bài viết' : 'Ghim bài viết' }}">
                                        <svg class="w-5 h-5" fill="{{ $thread->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                    </button>
                                </form>
                                {{-- Nút Khóa --}}
                                <form action="{{ route('forum.thread.toggleLock', $thread) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2 rounded-lg transition-colors {{ $thread->is_locked ? 'text-gray-800 bg-gray-100 hover:bg-gray-200' : 'text-gray-400 hover:text-gray-800 hover:bg-gray-50' }}"
                                            title="{{ $thread->is_locked ? 'Mở khóa bài viết' : 'Khóa bài viết' }}">
                                        <svg class="w-5 h-5" fill="{{ $thread->is_locked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            @if(auth()->id() === $thread->user_id)
                                <a href="{{ route('forum.editThread', $thread) }}"
                                   class="text-gray-400 hover:text-sky-500 transition-colors p-2 rounded-lg hover:bg-sky-50" title="Sửa bài viết">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                            @endif
                            <button x-data="" x-on:click="$dispatch('open-modal', 'confirm-delete-thread')" type="button" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50" title="Xóa bài viết">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <x-modal name="confirm-delete-thread" focusable maxWidth="sm">
                                <form method="post" action="{{ route('forum.destroy', $thread) }}" class="p-6">
                                    @csrf @method('delete')
                                    <div class="flex items-center gap-3 mb-4 text-red-600">
                                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <h2 class="text-lg font-bold text-gray-900">Xóa bài viết này?</h2>
                                    </div>
                                    <p class="text-sm text-gray-600">Bài viết cùng toàn bộ phản hồi sẽ bị xóa vĩnh viễn khỏi diễn đàn. Hành động này không thể hoàn tác đâu nhé!</p>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Hủy bỏ</button>
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">Đồng ý xóa</button>
                                    </div>
                                </form>
                            </x-modal>
                        </div>
                    @endif
                @endauth
            </div>

            {{-- Body --}}
            <div class="mt-6 prose prose-sm prose-gray max-w-none text-gray-700 leading-relaxed">
                {!! Purify::clean(Str::markdown($thread->content, ['html_input' => 'allow'])) !!}
            </div>

            {{-- Footer: Actions (Like) --}}
            <div class="mt-6 flex items-center gap-4 pt-6 border-t border-gray-100">
                <button type="button" 
                        @click="toggleLike"
                        class="flex items-center gap-2 group transition-colors"
                        :class="liked ? 'text-rose-500' : 'text-gray-500 hover:text-rose-500'">
                    <div class="relative flex items-center justify-center">
                        <svg class="w-6 h-6 transition-transform duration-300"
                             :class="[liked ? 'fill-current scale-110' : 'fill-none group-hover:scale-110', isLiking ? 'animate-pulse' : '']"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <span class="font-medium" x-text="likesCount"></span>
                </button>
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
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5" id="reply-{{ $reply->id }}"
                         x-data="{
                             liked: {{ auth()->check() ? ($reply->likes->contains('user_id', auth()->id()) ? 'true' : 'false') : 'false' }},
                             likesCount: {{ $reply->likes->count() }},
                             isLiking: false,
                             toggleLike() {
                                 if (!{{ auth()->check() ? 'true' : 'false' }}) {
                                     window.location.href = '{{ route('login') }}';
                                     return;
                                 }
                                 if (this.isLiking) return;
                                 this.isLiking = true;
                                 fetch('{{ route('forum.reply.like', $reply) }}', {
                                     method: 'POST',
                                     headers: {
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         'Accept': 'application/json',
                                         'Content-Type': 'application/json'
                                     }
                                 })
                                 .then(response => response.json())
                                 .then(data => {
                                     this.liked = data.isLiked;
                                     this.likesCount = data.likesCount;
                                 })
                                 .finally(() => {
                                     this.isLiking = false;
                                 });
                             }
                         }">
                        <div class="flex items-start gap-3">
                            <a href="{{ route('profile.show', $reply->user) }}" class="w-9 h-9 shrink-0 relative group">
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center overflow-hidden transition-all duration-300 {{ $reply->user->activeFrame ? 'scale-[1.0475]' : 'ring-2 ring-white shadow-sm' }}">
                                    @if($reply->user->avatar)
                                        <img src="{{ $reply->user->avatar }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <span class="text-xs font-bold text-white">{{ mb_strtoupper(mb_substr($reply->user->name, 0, 1, 'UTF-8'), 'UTF-8') }}</span>
                                    @endif
                                </div>
                                @if($reply->user->activeFrame)
                                    <img src="{{ Storage::url($reply->user->activeFrame->image_path) }}" alt="" 
                                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm">
                                        <a href="{{ route('profile.show', $reply->user) }}" class="font-semibold text-gray-900 hover:text-sky-600 transition-colors">{{ $reply->user->name }}</a>
                                        @if($reply->user->activeTitle)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold border"
                                                  style="color: {{ $reply->user->activeTitle->color_hex }}; border-color: {{ $reply->user->activeTitle->color_hex }}40; background-color: {{ $reply->user->activeTitle->color_hex }}15;">
                                                {{ $reply->user->activeTitle->name }}
                                            </span>
                                        @endif
                                        <span class="text-gray-300">&middot;</span>
                                        <span class="text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" 
                                                @click="toggleLike"
                                                class="flex items-center gap-1.5 transition-colors p-1.5 rounded-lg hover:bg-rose-50"
                                                :class="liked ? 'text-rose-500' : 'text-gray-400 hover:text-rose-500'"
                                                title="Thích phản hồi">
                                            <svg class="w-4 h-4 transition-transform duration-300"
                                                 :class="[liked ? 'fill-current scale-110' : 'fill-none group-hover:scale-110', isLiking ? 'animate-pulse' : '']"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            <span class="text-xs font-medium" x-text="likesCount || ''"></span>
                                        </button>
                                        @auth
                                            @if(!$thread->is_locked)
                                                <button type="button" onclick="setReplyParent({{ $reply->id }}, '{{ addslashes($reply->user->name) }}')"
                                                        class="text-gray-400 hover:text-sky-500 transition-colors p-1.5 rounded-lg hover:bg-sky-50" title="Trả lời">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            @if(auth()->check() && auth()->id() !== $reply->user_id)
                                                <button type="button" x-data @click="$dispatch('open-report', { type: 'ForumReply', id: {{ $reply->id }} })"
                                                        class="text-gray-400 hover:text-orange-500 transition-colors p-1.5 rounded-lg hover:bg-orange-50" title="Báo cáo">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            @if(auth()->id() === $reply->user_id || auth()->user()->isStaff())
                                                @if(auth()->id() === $reply->user_id)
                                                    <a href="{{ route('forum.editReply', $reply) }}"
                                                       class="text-gray-400 hover:text-sky-500 transition-colors p-1.5 rounded-lg hover:bg-sky-50" title="Sửa">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'confirm-delete-reply-{{ $reply->id }}')" class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50" title="Xóa">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                                <x-modal name="confirm-delete-reply-{{ $reply->id }}" focusable maxWidth="sm">
                                                    <form method="post" action="{{ route('forum.destroyReply', $reply) }}" class="p-6">
                                                        @csrf @method('delete')
                                                        <div class="flex items-center gap-3 mb-4 text-red-600">
                                                            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                            <h2 class="text-lg font-bold text-gray-900">Xóa phản hồi này?</h2>
                                                        </div>
                                                        <p class="text-sm text-gray-600">Phản hồi sẽ được gỡ khỏi bài viết. Hành động này không thể hoàn tác.</p>
                                                        <div class="mt-6 flex justify-end gap-3">
                                                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Hủy</button>
                                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">Đồng ý xóa</button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                {{-- Nested reply indicator --}}
                                @if($reply->parent_id && $reply->parent)
                                    <div class="mt-1.5 mb-1 flex items-center gap-1.5 text-xs text-gray-400">
                                        <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        <span>Đã trả lời</span>
                                        <a href="#reply-{{ $reply->parent_id }}" class="font-semibold text-sky-500 hover:text-sky-600 transition-colors">{{ $reply->parent->user->name ?? 'Ẩn danh' }}</a>
                                    </div>
                                @endif
                                <div class="mt-2 text-sm text-gray-700 leading-relaxed prose prose-sm prose-gray max-w-none">
                                    {!! Purify::clean(Str::markdown($reply->content, ['html_input' => 'allow'])) !!}
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
                <div class="mt-8 bg-white rounded-2xl border border-gray-200 shadow-sm p-6" id="reply-form-area">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Viết trả lời</h3>

                    {{-- Replying-to banner --}}
                    <div id="replying-to-banner" class="hidden mb-3 items-center justify-between px-3 py-2 bg-sky-50 border border-sky-200 rounded-lg">
                        <div class="flex items-center gap-2 text-sm text-sky-700">
                            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            <span>Đang trả lời <strong id="replying-to-name"></strong></span>
                        </div>
                        <button type="button" onclick="clearReplyParent()" class="text-sky-400 hover:text-sky-600 transition-colors p-1 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('forum.reply', $thread) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" id="reply-parent-id" value="">
                        <textarea name="content" rows="4" required maxlength="10000"
                              class="js-markdown-editor w-full"
                              placeholder="Chia sẻ ý kiến của bạn...">{!! old('content') !!}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div class="md-editor-footer">
                            <button type="submit"
                                    class="px-5 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl
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

@include('partials.markdown-editor')

<script>
    function setReplyParent(replyId, userName) {
        document.getElementById('reply-parent-id').value = replyId;
        document.getElementById('replying-to-name').textContent = userName;
        const banner = document.getElementById('replying-to-banner');
        banner.classList.remove('hidden');
        banner.classList.add('flex');
        document.getElementById('reply-form-area').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    function clearReplyParent() {
        document.getElementById('reply-parent-id').value = '';
        const banner = document.getElementById('replying-to-banner');
        banner.classList.add('hidden');
        banner.classList.remove('flex');
    }
</script>
</x-app-layout>
