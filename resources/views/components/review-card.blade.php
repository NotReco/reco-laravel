{{-- Review Card Component --}}
{{-- Usage: <x-review-card :review="$review" /> --}}

@props(['review', 'showMovie' => true])

@php
    $ratingColor = match(true) {
        $review->rating >= 9 => 'rating-bg-excellent',
        $review->rating >= 7 => 'rating-bg-good',
        $review->rating >= 5 => 'rating-bg-average',
        default => 'rating-bg-terrible',
    };

    $ratingLabel = match(true) {
        $review->rating >= 9 => 'Kiệt tác',
        $review->rating >= 8 => 'Xuất sắc',
        $review->rating >= 7 => 'Rất tốt',
        $review->rating >= 6 => 'Tốt',
        $review->rating >= 5 => 'Khá',
        $review->rating >= 4 => 'Trung bình',
        $review->rating >= 3 => 'Tạm được',
        $review->rating >= 2 => 'Tệ',
        default => 'Rất tệ',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-200 rounded-2xl p-5']) }}>
    {{-- Header: User + Rating --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="relative group w-10 h-10 shrink-0">
                <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center overflow-hidden transition-all duration-300 {{ $review->user?->activeFrame ? 'scale-[1.0475]' : 'ring-2 ring-white hover:ring-sky-200' }}">
                    @if($review->user?->avatar)
                        <img src="{{ $review->user->avatar }}" class="w-full h-full object-cover" alt="" loading="lazy">
                    @else
                        <span class="text-sm font-bold text-gray-500">{{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}</span>
                    @endif
                </div>
                @if($review->user?->activeFrame)
                    <img src="{{ Storage::url($review->user->activeFrame->image_path) }}" alt="" 
                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                @endif
            </div>
            <div class="min-w-0">
                <a href="{{ route('profile.show', $review->user) }}" class="block text-sm font-semibold text-gray-900 truncate hover:text-sky-500 transition-colors">{{ $review->user?->name ?? 'Ẩn danh' }}</a>
                <p class="text-xs text-gray-400">{{ $review->published_at?->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Rating Badge --}}
        @if($review->rating)
            <div class="flex items-center gap-1.5 shrink-0">
                <div class="{{ $ratingColor }} px-2.5 py-1 rounded-lg">
                    <span class="text-white text-sm font-bold">{{ number_format($review->rating, 1) }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Movie link --}}
    @if($showMovie && $review->movie)
        <a href="{{ route('movies.show', $review->movie) }}"
            class="flex items-center gap-2 text-xs font-medium text-sky-500 hover:text-sky-600 transition-colors mb-2 bg-sky-50 w-fit px-2 py-1 rounded-md">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
            {{ $review->movie->title }}
        </a>
    @endif

    {{-- Review title --}}
    @if($review->title)
        <h4 class="font-heading font-bold text-gray-900 text-sm mb-1 group-hover:text-sky-500 transition-colors">{{ $review->title }}</h4>
    @endif

    {{-- Review content --}}
    @if($review->is_spoiler)
        <x-spoiler-toggle>
            <div x-data="{ expanded: false, showToggle: false }"
                 x-init="$nextTick(() => { showToggle = $refs.content.scrollHeight > $refs.content.clientHeight; })"
                 class="relative">
                <div x-ref="content" class="text-gray-600 text-sm leading-relaxed"
                     :class="expanded ? '' : 'line-clamp-4 overflow-hidden'">
                    {{ $review->content }}
                </div>
                <button x-show="showToggle" x-cloak
                        @click="expanded = !expanded"
                        class="text-xs text-sky-500 hover:text-sky-600 font-semibold mt-1"
                        x-text="expanded ? 'Thu gọn' : 'Mở rộng'"></button>
            </div>
        </x-spoiler-toggle>
    @else
        <div x-data="{ expanded: false, showToggle: false }"
             x-init="$nextTick(() => { showToggle = $refs.content.scrollHeight > $refs.content.clientHeight; })"
             class="relative">
            <div x-ref="content" class="text-gray-600 text-sm leading-relaxed"
                 :class="expanded ? '' : 'line-clamp-4 overflow-hidden'">
                {{ $review->content }}
            </div>
            <button x-show="showToggle" x-cloak
                    @click="expanded = !expanded"
                    class="text-xs text-sky-500 hover:text-sky-600 font-semibold mt-1"
                    x-text="expanded ? 'Thu gọn' : 'Mở rộng'"></button>
        </div>
    @endif

    {{-- Rating label --}}
    @if($review->rating)
        <p class="text-xs mt-2 mb-4 font-medium {{ str_replace('rating-bg-', 'rating-', $ratingColor) }}">{{ $ratingLabel }}</p>
    @endif

    {{-- Interactions (Likes & Comments) --}}
    <div x-data="{
        liked: {{ auth()->check() && $review->likes->contains('user_id', auth()->id()) ? 'true' : 'false' }},
        likesCount: {{ $review->likes_count }},
        commentsCount: {{ $review->comments->count() }},
        showComments: false,
        replyContent: '',
        submittingComment: false,
        deleteModal: false,
        deleteCommentId: null,
        isDeleting: false,
        openDeleteModal(id) {
            this.deleteCommentId = id;
            this.deleteModal = true;
        },
        cancelDelete() {
            if (this.isDeleting) return;
            this.deleteModal = false;
            this.deleteCommentId = null;
        },
        async executeDelete() {
            if (this.isDeleting || !this.deleteCommentId) return;
            
            this.isDeleting = true;
            try {
                const res = await fetch('{{ url('comments') }}/' + this.deleteCommentId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!res.ok) {
                    const text = await res.text();
                    throw new Error('HTTP ' + res.status + ' - ' + text.substring(0, 50));
                }
                
                const data = await res.json();
                if (data.success) {
                    const el = document.getElementById('review-comment-' + this.deleteCommentId);
                    if (el) {
                        el.style.transition = 'opacity 0.3s, transform 0.3s';
                        el.style.opacity = '0';
                        el.style.transform = 'translateX(-10px)';
                        setTimeout(() => el.remove(), 300);
                    }
                    this.commentsCount--;
                    this.deleteModal = false;
                    setTimeout(() => { this.deleteCommentId = null; }, 300);
                }
            } catch (err) {
                console.error('Error deleting:', err);
                alert('Không thể xóa bình luận. Lỗi: ' + err.message);
            } finally {
                this.isDeleting = false;
            }
        },
        async submitComment(e) {
            if (!this.replyContent.trim() || this.submittingComment) return;
            this.submittingComment = true;
            try {
                const formData = new FormData(this.$refs.commentForm);
                const res = await fetch('{{ route('comments.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    this.replyContent = '';
                    this.commentsCount++;
                    this.$refs.commentInput.style.height = '38px';
                    if (this.$refs.emptyState) this.$refs.emptyState.style.display = 'none';
                    if (this.$refs.commentsList) {
                        this.$refs.commentsList.style.display = 'block';
                        this.$refs.commentsList.insertAdjacentHTML('beforeend', data.html);
                        this.$refs.commentsList.scrollTop = this.$refs.commentsList.scrollHeight;
                    }
                }
            } catch (err) { console.error('Error submitting comment:', err); }
            finally { this.submittingComment = false; }
        },
        focusReply(name) {
            this.replyContent = '@' + name + ' ';
            this.showComments = true;
            this.$nextTick(() => { 
                if (this.$refs.commentInput) {
                    this.$refs.commentInput.focus(); 
                    this.$refs.commentInput.setSelectionRange(this.replyContent.length, this.replyContent.length);
                }
            });
        },
        async toggleLike() {
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest
            
            try {
                const res = await fetch('{{ route('likes.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: 'review', id: {{ $review->id }} })
                });
                const data = await res.json();
                if (data.success) {
                    this.liked = data.is_liked;
                    this.likesCount = data.likes_count;
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        }
    }" class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center gap-4">
            {{-- Like Button --}}
            <button @click="toggleLike()" class="flex items-center gap-1.5 text-sm font-medium transition-colors group"
                :class="liked ? 'text-rose-500' : 'text-gray-500 hover:text-gray-900'">
                <svg class="w-4 h-4 transition-transform group-active:scale-75" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span x-text="likesCount > 0 ? likesCount : 'Thích'"></span>
            </button>

            {{-- Comment Toggle --}}
            <button @click="showComments = !showComments" class="flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span x-text="commentsCount > 0 ? commentsCount : 'Bình luận'"></span>
            </button>

            {{-- Report Review --}}
            @if(auth()->check() && auth()->id() !== $review->user_id)
                <button type="button" @click="$dispatch('open-report', { type: 'Review', id: {{ $review->id }} })" class="flex items-center text-sm font-medium text-gray-400 hover:text-orange-500 transition-colors ml-auto p-1.5 hover:bg-orange-50 rounded-lg" title="Báo cáo">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Expanded Comments Section --}}
        <div x-show="showComments" x-collapse class="mt-4 pt-4 border-t border-gray-100" style="display: none;">
            {{-- List existing comments --}}
            <div x-ref="commentsList" class="space-y-4 mb-4 max-h-[360px] overflow-y-auto px-3 -mx-3 py-2 custom-scrollbar" x-show="commentsCount > 0" style="{{ $review->comments->isEmpty() ? 'display: none;' : '' }}">
                @foreach($review->comments as $comment)
                    <x-reviews.comment-item :comment="$comment" :review="$review" />
                @endforeach
            </div>
            
            <div x-ref="emptyState" class="text-center py-4 text-sm text-gray-500 bg-gray-50 rounded-xl mb-4 border border-gray-100" x-show="commentsCount === 0" style="{{ $review->comments->isNotEmpty() ? 'display: none;' : '' }}">
                Chưa có bình luận nào. Hãy là người đầu tiên bóc tem!
            </div>

            {{-- Comment form --}}
            @auth
                <form x-ref="commentForm" action="{{ route('comments.store') }}" method="POST" class="flex gap-3" @submit.prevent="submitComment">
                    @csrf
                    <input type="hidden" name="review_id" value="{{ $review->id }}">
                    <div class="relative group w-8 h-8 shrink-0">
                        <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center overflow-hidden text-center leading-8 text-[10px] font-bold text-gray-500 transition-all duration-300 {{ Auth::user()->activeFrame ? 'scale-[1.0475]' : 'ring-1 ring-gray-200' }}">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                        @if(Auth::user()->activeFrame)
                            <img src="{{ Storage::url(Auth::user()->activeFrame->image_path) }}" alt="" 
                                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                        @endif
                    </div>
                    <div class="flex-1 flex items-start gap-2">
                        <textarea name="content" x-ref="commentInput" x-model="replyContent" required placeholder="Viết bình luận..." rows="1" 
                                  @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $refs.commentForm.requestSubmit(); }"
                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2 px-3 text-sm flex-1 focus:border-sky-300 focus:ring-1 focus:ring-sky-300 outline-none transition-colors resize-none overflow-hidden" 
                                  style="min-height: 38px; max-height: 120px;" 
                                  oninput="this.style.height = '38px'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'; this.style.overflowY = this.scrollHeight > 120 ? 'auto' : 'hidden';"></textarea>
                        <button type="submit" :disabled="submittingComment" class="shrink-0 px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 shadow-sm shadow-sky-500/20 text-white text-sm font-medium transition-colors disabled:opacity-50">
                            <span x-show="!submittingComment">Gửi</span>
                            <svg x-show="submittingComment" class="animate-spin h-5 w-5 text-white" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            @else
                <p class="text-xs text-center text-gray-500">Vui lòng <a href="{{ route('login') }}" class="text-sky-500 hover:underline font-medium">đăng nhập</a> để bình luận.</p>
            @endauth
        </div>

        {{-- Custom Single-Step Delete Modal --}}
        @auth
            <div x-show="deleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">

                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all"
                    @click.outside="cancelDelete()" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                    <div>
                        <div class="px-5 py-5 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900">Xóa bình luận</h3>
                            <p class="text-[13px] text-gray-500 mt-1 leading-relaxed">Hành động này không thể hoàn tác. Bạn chắc chắn muốn xóa bình luận này chứ?</p>
                        </div>
                        <div class="px-5 py-3.5 bg-gray-50 flex gap-2 justify-end">
                            <button @click="executeDelete()" :disabled="isDeleting"
                                class="min-w-[90px] flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-show="!isDeleting">Xóa</span>
                                <span x-show="isDeleting" style="display: none;">Đang xóa</span>
                            </button>
                            <button @click="cancelDelete()" :disabled="isDeleting"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 rounded-lg transition-colors disabled:opacity-50">Hủy</button>
                        </div>
                    </div>

                </div>
            </div>
        @endauth
    </div>

    {{-- ── Báo cáo công khai đã xác nhận ──────────────────────────── --}}
    @php
        $publicReports = $review->relationLoaded('reports') ? $review->reports : collect();
    @endphp
    @if($publicReports->isNotEmpty())
        <div x-data="{ open: false }" class="mt-3 pt-3 border-t border-orange-100">
            {{-- Toggle button --}}
            <button @click="open = !open"
                    class="flex items-center gap-1.5 text-xs font-medium text-orange-500 hover:text-orange-600 transition-colors group">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                </svg>
                <span x-text="open ? 'Ẩn báo cáo' : '{{ $publicReports->count() }} báo cáo vi phạm từ cộng đồng'"></span>
                <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Report list --}}
            <div x-show="open" x-collapse x-cloak class="mt-2 space-y-2">
                @foreach($publicReports as $report)
                    <div class="flex items-start gap-2.5 bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5">
                        {{-- Avatar --}}
                        <div class="w-6 h-6 rounded-full bg-orange-200 flex items-center justify-center shrink-0 overflow-hidden text-[10px] font-bold text-orange-700 mt-0.5">
                            @if($report->user?->avatar)
                                <img src="{{ $report->user->avatar }}" class="w-full h-full object-cover" alt="">
                            @else
                                {{ strtoupper(substr($report->user?->name ?? '?', 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-semibold text-orange-700">{{ $report->user?->name ?? 'Ẩn danh' }}</span>
                                <span class="text-[10px] text-orange-400">·</span>
                                <span class="text-[10px] text-orange-500 font-medium bg-orange-100 px-1.5 py-0.5 rounded-full">
                                    {{ $report->reason }}
                                </span>
                                <span class="text-[10px] text-gray-400 ml-auto">{{ $report->created_at->diffForHumans() }}</span>
                            </div>
                            @if($report->description)
                                <p class="text-xs text-orange-600/80 mt-1 leading-relaxed">{{ $report->description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <p class="text-[10px] text-gray-400 text-center pt-1">
                    ✓ Đã được kiểm duyệt bởi Admin
                </p>
            </div>
        </div>
    @endif
</div>
