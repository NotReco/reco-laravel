<x-app-layout>
    <x-slot:title>{{ $article->title }}</x-slot:title>

    {{-- ── Article Header ──────────────────────────────────── --}}
    <article class="max-w-4xl mx-auto px-4 py-10">

        {{-- Tags --}}
        @if ($article->tags->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-5">
                @foreach ($article->tags as $tag)
                    <a href="{{ route('news.index', ['tag' => $tag->slug]) }}"
                        class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg uppercase tracking-wider hover:bg-gray-200 transition-colors">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Title --}}
        <h1
            class="text-3xl md:text-4xl lg:text-[2.75rem] font-extrabold text-gray-900 leading-tight tracking-tight font-outfit">
            {{ $article->title }}
        </h1>

        {{-- Author & Date --}}
        <div class="mt-5 flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center shrink-0 ring-2 ring-rose-100">
                @if ($article->user->avatar ?? false)
                    <img src="{{ $article->user->avatar }}" alt="" class="w-full h-full rounded-full object-cover"
                        loading="lazy">
                @else
                    <span
                        class="text-sm font-bold text-white">{{ strtoupper(substr($article->user->name ?? '?', 0, 1)) }}</span>
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
        @if ($article->subtitle)
            <p class="mt-6 text-xl md:text-2xl font-bold text-gray-700 leading-relaxed">
                {{ $article->subtitle }}
            </p>
        @endif

        {{-- Thumbnail --}}
        @if ($article->thumbnail)
            <div class="mt-8 rounded-2xl overflow-hidden border border-gray-100">
                <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}" class="w-full object-cover"
                    loading="lazy">
            </div>
        @endif

        {{-- Content --}}
        <div
            class="mt-8 prose prose-lg prose-gray max-w-none
                prose-headings:font-outfit prose-headings:text-gray-900
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-a:text-rose-600 prose-a:no-underline hover:prose-a:underline
                prose-img:rounded-xl prose-img:border prose-img:border-gray-100">
            {!! nl2br(e($article->content)) !!}
        </div>

        {{-- Article Footer --}}
        <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between text-sm text-gray-400">
            <span>{{ $article->views_count }} lượt xem</span>
            <a href="{{ route('news.index') }}"
                class="flex items-center gap-1 text-gray-500 hover:text-gray-900 transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Quay lại tin tức
            </a>
        </div>
    </article>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{--  COMMENTS SECTION (Facebook-style)                     --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <section class="bg-gray-50 border-t border-gray-100" x-data="commentSection()">
        <div class="max-w-4xl mx-auto px-4 py-10">

            {{-- Comment Count Badge --}}
            <div class="flex items-center gap-3 mb-8">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm font-bold text-gray-900" x-text="totalComments">{{ $commentsCount }}</span>
                    <span class="text-sm text-gray-500">bình luận</span>
                </div>
            </div>

            @auth
                {{-- ── Comment Form (AJAX) ─────────────────────── --}}
                <form @submit.prevent="submitComment($event)" class="mb-8">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                            @if (auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">
                            @else
                                <span class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <textarea x-model="newComment" rows="3" required maxlength="1000" placeholder="Viết bình luận của bạn..."
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400
                                         focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none"></textarea>
                            <div class="mt-2 flex justify-end">
                                <button type="submit" :disabled="submitting || !newComment.trim()"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-blue-500 text-white text-sm font-medium rounded-xl
                                           hover:bg-blue-600 active:scale-[0.97] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span x-text="submitting ? 'Đang gửi...' : 'Bình luận'">Bình luận</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- ── Comments List ───────────────────────────── --}}
                <div class="space-y-5" id="comments-list">
                    @forelse($article->comments as $comment)
                        <div class="flex gap-3" id="comment-{{ $comment->id }}">
                            {{-- Avatar --}}
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center shrink-0">
                                @if ($comment->user->avatar ?? false)
                                    <img src="{{ $comment->user->avatar }}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">
                                @else
                                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                {{-- Comment Bubble --}}
                                <div class="bg-white rounded-xl px-4 py-3 border border-gray-200">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name ?? 'Ẩn danh' }}</span>
                                        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">{{ $comment->content }}</p>
                                </div>

                                {{-- Action Buttons (all inline, single row) --}}
                                <div class="mt-1.5 flex items-center gap-3 pl-4 flex-nowrap">
                                    {{-- Like --}}
                                    <button @click="toggleLike({{ $comment->id }}, $event)"
                                        class="text-xs font-medium transition-colors whitespace-nowrap {{ $comment->isLikedBy(auth()->user()) ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500' }}"
                                        :class="likedComments[{{ $comment->id }}] ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500'"
                                        id="like-btn-{{ $comment->id }}">
                                        <span x-text="(likeCounts[{{ $comment->id }}] > 0 ? likeCounts[{{ $comment->id }}] + ' ' : '') + 'Thích'">
                                            {{ $comment->likes->count() > 0 ? $comment->likes->count() . ' ' : '' }}Thích
                                        </span>
                                    </button>

                                    <span class="text-gray-200">·</span>

                                    {{-- Reply --}}
                                    <button @click="replyTo = replyTo === {{ $comment->id }} ? null : {{ $comment->id }}"
                                        class="text-xs font-medium text-gray-400 hover:text-blue-500 transition-colors whitespace-nowrap">
                                        Trả lời
                                    </button>

                                    <span class="text-gray-200">·</span>

                                    {{-- Report --}}
                                    <button @click="openReport({{ $comment->id }})"
                                        class="text-xs font-medium text-gray-400 hover:text-orange-500 transition-colors whitespace-nowrap">
                                        Báo cáo
                                    </button>

                                    {{-- Delete (staff only) --}}
                                    @if (auth()->user()->isStaff())
                                        <span class="text-gray-200">·</span>
                                        <button @click="deleteComment({{ $comment->id }})"
                                            class="text-xs font-medium text-gray-400 hover:text-red-500 transition-colors whitespace-nowrap">
                                            Xóa
                                        </button>
                                    @endif
                                </div>

                                {{-- Replies --}}
                                @if ($comment->replies->isNotEmpty())
                                    <div class="mt-3 ml-2 pl-4 border-l-2 border-gray-200 space-y-3">
                                        @foreach ($comment->replies as $reply)
                                            <div class="flex gap-2.5" id="comment-{{ $reply->id }}">
                                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shrink-0">
                                                    @if ($reply->user->avatar ?? false)
                                                        <img src="{{ $reply->user->avatar }}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">
                                                    @else
                                                        <span class="text-[10px] font-bold text-white">{{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                                                        <div class="flex items-center gap-2 mb-0.5">
                                                            <span class="text-xs font-semibold text-gray-900">{{ $reply->user->name ?? 'Ẩn danh' }}</span>
                                                            <span class="text-[11px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line break-words">{{ $reply->content }}</p>
                                                    </div>

                                                    {{-- Reply actions (inline, single row) --}}
                                                    <div class="mt-1 flex items-center gap-2 pl-3 flex-nowrap">
                                                        {{-- Like --}}
                                                        <button @click="toggleLike({{ $reply->id }}, $event)"
                                                            class="text-[11px] font-medium transition-colors whitespace-nowrap {{ $reply->isLikedBy(auth()->user()) ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500' }}"
                                                            :class="likedComments[{{ $reply->id }}] ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500'"
                                                            id="like-btn-{{ $reply->id }}">
                                                            <span x-text="(likeCounts[{{ $reply->id }}] > 0 ? likeCounts[{{ $reply->id }}] + ' ' : '') + 'Thích'">
                                                                {{ $reply->likes->count() > 0 ? $reply->likes->count() . ' ' : '' }}Thích
                                                            </span>
                                                        </button>

                                                        <span class="text-gray-200">·</span>

                                                        {{-- Report --}}
                                                        <button @click="openReport({{ $reply->id }})"
                                                            class="text-[11px] font-medium text-gray-400 hover:text-orange-500 transition-colors whitespace-nowrap">
                                                            Báo cáo
                                                        </button>

                                                        {{-- Delete (staff only) --}}
                                                        @if (auth()->user()->isStaff())
                                                            <span class="text-gray-200">·</span>
                                                            <button @click="deleteComment({{ $reply->id }})"
                                                                class="text-[11px] font-medium text-gray-400 hover:text-red-500 transition-colors whitespace-nowrap">
                                                                Xóa
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Reply Form (AJAX) --}}
                                <div x-show="replyTo === {{ $comment->id }}" x-transition x-cloak style="display: none" class="mt-3 ml-2 pl-4 border-l-2 border-blue-200">
                                    <form @submit.prevent="submitReply({{ $comment->id }}, $event)">
                                        <textarea x-model="replyContent" rows="2" required maxlength="1000"
                                            placeholder="Trả lời {{ $comment->user->name ?? '' }}..."
                                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-800
                                                     focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none"></textarea>
                                        <div class="mt-1.5 flex gap-2 justify-end">
                                            <button type="button" @click="replyTo = null; replyContent = ''"
                                                class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">Hủy</button>
                                            <button type="submit" :disabled="submittingReply || !replyContent.trim()"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span x-text="submittingReply ? 'Đang gửi...' : 'Gửi'">Gửi</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8" id="no-comments">
                            <p class="text-gray-400 text-sm">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                        </div>
                    @endforelse
                </div>
            @else
                {{-- ── Guest: Prompt to Login ──────────────────── --}}
                <div class="text-center py-12 bg-white rounded-2xl border border-gray-100">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p class="text-gray-600 font-medium mb-1">Đăng nhập để xem và viết bình luận</p>
                    <p class="text-sm text-gray-400 mb-5">Tham gia thảo luận cùng cộng đồng</p>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl
                          hover:bg-gray-800 active:scale-[0.97] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Đăng nhập
                    </a>
                </div>
            @endauth

        </div>

        {{-- ── Report Dialog ──────────────────────────────── --}}
        <div x-show="reportModal" x-transition.opacity x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            @click.self="reportModal = false" style="display: none">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden" @click.stop>
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Báo cáo bình luận</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Chọn lý do báo cáo</p>
                </div>
                <div class="px-5 py-3 space-y-1">
                    <template x-for="reason in reportReasons" :key="reason">
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="report_reason" :value="reason" x-model="selectedReason"
                                class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700" x-text="reason"></span>
                        </label>
                    </template>
                </div>
                <div class="px-5 py-4 border-t border-gray-100 flex gap-2 justify-end">
                    <button @click="reportModal = false; selectedReason = ''"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Hủy</button>
                    <button @click="submitReport()" :disabled="!selectedReason || submittingReport"
                        class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50">
                        <span x-text="submittingReport ? 'Đang gửi...' : 'Gửi báo cáo'"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Alpine.js Comment Logic ─────────────────────── --}}
    @auth
    <script>
    function commentSection() {
        return {
            // State
            newComment: '',
            submitting: false,
            replyTo: null,
            replyContent: '',
            submittingReply: false,
            totalComments: {{ $commentsCount }},

            // Like state
            likedComments: {
                @foreach($article->comments as $comment)
                    {{ $comment->id }}: {{ $comment->isLikedBy(auth()->user()) ? 'true' : 'false' }},
                    @foreach($comment->replies as $reply)
                        {{ $reply->id }}: {{ $reply->isLikedBy(auth()->user()) ? 'true' : 'false' }},
                    @endforeach
                @endforeach
            },
            likeCounts: {
                @foreach($article->comments as $comment)
                    {{ $comment->id }}: {{ $comment->likes->count() }},
                    @foreach($comment->replies as $reply)
                        {{ $reply->id }}: {{ $reply->likes->count() }},
                    @endforeach
                @endforeach
            },

            // Report state
            reportModal: false,
            reportCommentId: null,
            selectedReason: '',
            submittingReport: false,
            reportReasons: [
                'Nội dung spam',
                'Ngôn từ thù ghét / quấy rối',
                'Thông tin sai lệch',
                'Nội dung không phù hợp',
                'Khác'
            ],

            // Current user info
            // Base URL for AJAX calls
            baseUrl: '{{ url("/") }}',

            currentUser: {
                name: @json(auth()->user()->name),
                avatar: @json(auth()->user()->avatar),
                initial: @json(strtoupper(substr(auth()->user()->name, 0, 1))),
                isStaff: {{ auth()->user()->isStaff() ? 'true' : 'false' }},
            },

            // Methods
            async submitComment(e) {
                if (this.submitting || !this.newComment.trim()) return;
                this.submitting = true;

                try {
                    const res = await fetch('{{ route("article-comments.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            article_id: {{ $article->id }},
                            content: this.newComment,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.appendComment(data.comment);
                        this.newComment = '';
                        this.totalComments++;
                        // Remove "no comments" placeholder
                        const placeholder = document.getElementById('no-comments');
                        if (placeholder) placeholder.remove();
                    }
                } catch (err) {
                    console.error('Error posting comment:', err);
                    alert('Không thể gửi bình luận. Vui lòng thử lại.');
                } finally {
                    this.submitting = false;
                }
            },

            async submitReply(parentId, e) {
                if (this.submittingReply || !this.replyContent.trim()) return;
                this.submittingReply = true;

                try {
                    const res = await fetch('{{ route("article-comments.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            article_id: {{ $article->id }},
                            parent_id: parentId,
                            content: this.replyContent,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.appendReply(parentId, data.comment);
                        this.replyContent = '';
                        this.replyTo = null;
                        this.totalComments++;
                    }
                } catch (err) {
                    console.error('Error posting reply:', err);
                    alert('Không thể gửi trả lời. Vui lòng thử lại.');
                } finally {
                    this.submittingReply = false;
                }
            },

            async toggleLike(commentId, e) {
                try {
                    const res = await fetch(`${this.baseUrl}/article-comments/${commentId}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    this.likedComments[commentId] = data.isLiked;
                    this.likeCounts[commentId] = data.likesCount;
                } catch (err) {
                    console.error('Error toggling like:', err);
                }
            },

            openReport(commentId) {
                this.reportCommentId = commentId;
                this.selectedReason = '';
                this.reportModal = true;
            },

            async submitReport() {
                if (!this.selectedReason || this.submittingReport) return;
                this.submittingReport = true;

                try {
                    const res = await fetch(`${this.baseUrl}/article-comments/${this.reportCommentId}/report`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ reason: this.selectedReason }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert(data.message || 'Có lỗi xảy ra.');
                    }
                    this.reportModal = false;
                    this.selectedReason = '';
                } catch (err) {
                    console.error('Error reporting:', err);
                    alert('Không thể gửi báo cáo. Vui lòng thử lại.');
                } finally {
                    this.submittingReport = false;
                }
            },

            async deleteComment(commentId) {
                if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;

                try {
                    const res = await fetch(`${this.baseUrl}/article-comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    if (data.success) {
                        const el = document.getElementById(`comment-${commentId}`);
                        if (el) {
                            el.style.transition = 'opacity 0.3s, transform 0.3s';
                            el.style.opacity = '0';
                            el.style.transform = 'translateX(-10px)';
                            setTimeout(() => el.remove(), 300);
                        }
                        this.totalComments--;
                    }
                } catch (err) {
                    console.error('Error deleting:', err);
                    alert('Không thể xóa bình luận.');
                }
            },

            // DOM helpers
            appendComment(comment) {
                const list = document.getElementById('comments-list');
                const avatarContent = comment.user.avatar
                    ? `<img src="${comment.user.avatar}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">`
                    : `<span class="text-xs font-bold text-white">${comment.user.initial}</span>`;

                const staffDeleteBtn = this.currentUser.isStaff
                    ? `<span class="text-gray-200">·</span>
                       <button onclick="document.querySelector('[x-data]').__x.$data.deleteComment(${comment.id})"
                           class="text-xs font-medium text-gray-400 hover:text-red-500 transition-colors whitespace-nowrap">Xóa</button>`
                    : '';

                const html = `
                    <div class="flex gap-3" id="comment-${comment.id}" style="opacity:0;transform:translateY(10px);transition:all 0.3s">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center shrink-0">
                            ${avatarContent}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="bg-white rounded-xl px-4 py-3 border border-gray-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-900">${comment.user.name}</span>
                                    <span class="text-xs text-gray-400">${comment.created_at}</span>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">${this.escapeHtml(comment.content)}</p>
                            </div>
                            <div class="mt-1.5 flex items-center gap-3 pl-4 flex-nowrap">
                                <button onclick="document.querySelector('[x-data]').__x.$data.toggleLike(${comment.id}, event)"
                                    class="text-xs font-medium text-gray-400 hover:text-blue-500 transition-colors whitespace-nowrap" id="like-btn-${comment.id}">Thích</button>
                                <span class="text-gray-200">·</span>
                                <button onclick="document.querySelector('[x-data]').__x.$data.replyTo = document.querySelector('[x-data]').__x.$data.replyTo === ${comment.id} ? null : ${comment.id}"
                                    class="text-xs font-medium text-gray-400 hover:text-blue-500 transition-colors whitespace-nowrap">Trả lời</button>
                                <span class="text-gray-200">·</span>
                                <button onclick="document.querySelector('[x-data]').__x.$data.openReport(${comment.id})"
                                    class="text-xs font-medium text-gray-400 hover:text-orange-500 transition-colors whitespace-nowrap">Báo cáo</button>
                                ${staffDeleteBtn}
                            </div>
                        </div>
                    </div>`;

                list.insertAdjacentHTML('afterbegin', html);
                this.likedComments[comment.id] = false;
                this.likeCounts[comment.id] = 0;

                requestAnimationFrame(() => {
                    const el = document.getElementById(`comment-${comment.id}`);
                    if (el) { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }
                });
            },

            appendReply(parentId, reply) {
                const parentEl = document.getElementById(`comment-${parentId}`);
                if (!parentEl) return;

                let repliesContainer = parentEl.querySelector('.border-l-2.border-gray-200');
                if (!repliesContainer) {
                    const contentDiv = parentEl.querySelector('.flex-1.min-w-0');
                    const replyFormDiv = contentDiv.querySelector('[x-show]');
                    const container = document.createElement('div');
                    container.className = 'mt-3 ml-2 pl-4 border-l-2 border-gray-200 space-y-3';
                    contentDiv.insertBefore(container, replyFormDiv);
                    repliesContainer = container;
                }

                const avatarContent = reply.user.avatar
                    ? `<img src="${reply.user.avatar}" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">`
                    : `<span class="text-[10px] font-bold text-white">${reply.user.initial}</span>`;

                const staffDeleteBtn = this.currentUser.isStaff
                    ? `<span class="text-gray-200">·</span>
                       <button onclick="document.querySelector('[x-data]').__x.$data.deleteComment(${reply.id})"
                           class="text-[11px] font-medium text-gray-400 hover:text-red-500 transition-colors whitespace-nowrap">Xóa</button>`
                    : '';

                const html = `
                    <div class="flex gap-2.5" id="comment-${reply.id}" style="opacity:0;transform:translateY(5px);transition:all 0.3s">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shrink-0">
                            ${avatarContent}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-xs font-semibold text-gray-900">${reply.user.name}</span>
                                    <span class="text-[11px] text-gray-400">${reply.created_at}</span>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line break-words">${this.escapeHtml(reply.content)}</p>
                            </div>
                            <div class="mt-1 flex items-center gap-2 pl-3 flex-nowrap">
                                <button onclick="document.querySelector('[x-data]').__x.$data.toggleLike(${reply.id}, event)"
                                    class="text-[11px] font-medium text-gray-400 hover:text-blue-500 transition-colors whitespace-nowrap" id="like-btn-${reply.id}">Thích</button>
                                <span class="text-gray-200">·</span>
                                <button onclick="document.querySelector('[x-data]').__x.$data.openReport(${reply.id})"
                                    class="text-[11px] font-medium text-gray-400 hover:text-orange-500 transition-colors whitespace-nowrap">Báo cáo</button>
                                ${staffDeleteBtn}
                            </div>
                        </div>
                    </div>`;

                repliesContainer.insertAdjacentHTML('beforeend', html);
                this.likedComments[reply.id] = false;
                this.likeCounts[reply.id] = 0;

                requestAnimationFrame(() => {
                    const el = document.getElementById(`comment-${reply.id}`);
                    if (el) { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }
                });
            },

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            },
        };
    }
    </script>
    @endauth

</x-app-layout>
