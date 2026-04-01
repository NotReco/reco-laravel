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

    {{-- ── Alpine.js Comment Logic (MUST be defined before the section) ── --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('commentSection', () => (
                @guest
                {
                    totalComments: {{ $commentsCount }},
                    reportModal: false,
                    reportReasons: [],
                    selectedReason: '',
                    submittingReport: false,
                    likedComments: {},
                    likeCounts: {},
                    replyTo: null,
                    replyingToId: null,
                    replyContent: '',
                }
                @endguest
                @auth
                {
                    newComment: '',
                    submitting: false,
                    replyTo: null,
                    replyParentId: null,
                    replyingToId: null,
                    replyContent: '',
                    submittingReply: false,
                    totalComments: {{ $commentsCount }},

                    likedComments: {
                        @foreach ($article->comments as $c)
                            {{ $c->id }}: {{ $c->isLikedBy(auth()->user()) ? 'true' : 'false' }},
                            @foreach ($c->replies as $r)
                                {{ $r->id }}: {{ $r->isLikedBy(auth()->user()) ? 'true' : 'false' }},
                                @foreach ($r->replies as $rr)
                                    {{ $rr->id }}: {{ $rr->isLikedBy(auth()->user()) ? 'true' : 'false' }},
                                @endforeach
                            @endforeach
                        @endforeach
                    },
                    likeCounts: {
                        @foreach ($article->comments as $c)
                            {{ $c->id }}: {{ $c->likes->count() }},
                            @foreach ($c->replies as $r)
                                {{ $r->id }}: {{ $r->likes->count() }},
                                @foreach ($r->replies as $rr)
                                    {{ $rr->id }}: {{ $rr->likes->count() }},
                                @endforeach
                            @endforeach
                        @endforeach
                    },

                    participantNames: [
                        @foreach($article->comments as $c)
                            @json($c->user->name ?? ''),
                            @foreach($c->replies as $r)
                                @json($r->user->name ?? ''),
                                @foreach($r->replies as $rr)
                                    @json($rr->user->name ?? ''),
                                @endforeach
                            @endforeach
                        @endforeach
                    ].filter((v, i, a) => v && a.indexOf(v) === i),

                    reportModal: false,
                    reportCommentId: null,
                    selectedReason: '',
                    submittingReport: false,
                    deleteModal: false,
                    deleteCommentId: null,
                    isDeleting: false,
                    deleteStep: 1,
                    deleteCountdown: 5,
                    countdownTimer: null,
                    reportReasons: [
                        'Nội dung spam',
                        'Ngôn từ thù ghét / quấy rối',
                        'Thông tin sai lệch',
                        'Nội dung không phù hợp',
                        'Khác'
                    ],

                    currentUser: {
                        name: @json(auth()->user()->name),
                        avatar: @json(auth()->user()->avatar),
                        initial: @json(strtoupper(substr(auth()->user()->name, 0, 1))),
                        isStaff: {{ auth()->user()->isStaff() ? 'true' : 'false' }},
                    },

                    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
                    baseUrl: '{{ url("/") }}',

                    async parseApiError(res) {
                        let payload = null;
                        try {
                            payload = await res.clone().json();
                        } catch (_) {
                            payload = null;
                        }

                        if (payload?.message) return payload.message;
                        if (payload?.errors) {
                            const firstError = Object.values(payload.errors)?.[0]?.[0];
                            if (firstError) return firstError;
                        }

                        switch (res.status) {
                            case 401:
                                return 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.';
                            case 403:
                                return 'Bạn chưa xác minh email hoặc không có quyền thực hiện thao tác này.';
                            case 419:
                                return 'Phiên bảo mật đã hết hạn. Vui lòng tải lại trang.';
                            case 422:
                                return 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại nội dung.';
                            default:
                                return 'Đã có lỗi xảy ra. Vui lòng thử lại.';
                        }
                    },

                    async submitComment(e) {
                        if (this.submitting || !this.newComment.trim()) return;
                        this.submitting = true;
                        try {
                            const res = await fetch('{{ route("article-comments.store") }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                                body: JSON.stringify({ article_id: {{ $article->id }}, content: this.newComment }),
                            });
                            if (!res.ok) {
                                const message = await this.parseApiError(res);
                                throw new Error(message);
                            }
                            const data = await res.json();
                            if (data.success) {
                                this.appendComment(data.comment);
                                this.newComment = '';
                                this.totalComments++;
                                const ph = document.getElementById('no-comments');
                                if (ph) ph.remove();
                            }
                        } catch (err) {
                            console.error('Error posting comment:', err);
                            alert(err?.message || 'Không thể gửi bình luận. Vui lòng thử lại.');
                        } finally { this.submitting = false; }
                    },

                    async submitReply(e) {
                        if (this.submittingReply || !this.replyContent.trim()) return;
                        if (!this.replyParentId) return;
                        this.submittingReply = true;
                        try {
                            const res = await fetch('{{ route("article-comments.store") }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                                body: JSON.stringify({ article_id: {{ $article->id }}, parent_id: this.replyParentId, content: this.replyContent }),
                            });
                            if (!res.ok) {
                                const message = await this.parseApiError(res);
                                throw new Error(message);
                            }
                            const data = await res.json();
                            if (data.success) {
                                this.appendReply(data.comment);
                                this.replyContent = '';
                                this.replyTo = null;
                                this.replyParentId = null;
                                this.replyingToId = null;
                                this.totalComments++;
                            }
                        } catch (err) {
                            console.error('Error posting reply:', err);
                            alert(err?.message || 'Không thể gửi trả lời. Vui lòng thử lại.');
                        } finally { this.submittingReply = false; }
                    },

                    async toggleLike(commentId, e) {
                        try {
                            const res = await fetch(this.baseUrl + '/article-comments/' + commentId + '/like', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            });
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            const data = await res.json();
                            this.likedComments[commentId] = data.isLiked;
                            this.likeCounts[commentId] = data.likesCount;
                        } catch (err) { console.error('Error toggling like:', err); }
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
                            const res = await fetch(this.baseUrl + '/article-comments/' + this.reportCommentId + '/report', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                                body: JSON.stringify({ reason: this.selectedReason }),
                            });
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            const data = await res.json();
                            alert(data.success ? data.message : (data.message || 'Có lỗi xảy ra.'));
                            this.reportModal = false;
                            this.selectedReason = '';
                        } catch (err) {
                            console.error('Error reporting:', err);
                            alert('Không thể gửi báo cáo. Vui lòng thử lại.');
                        } finally { this.submittingReport = false; }
                    },

                    openDeleteModal(commentId) {
                        this.deleteCommentId = commentId;
                        this.deleteStep = 1;
                        this.deleteModal = true;
                    },

                    confirmDelete() {
                        this.deleteStep = 2;
                        this.deleteCountdown = 5;
                        this.countdownTimer = setInterval(() => {
                            this.deleteCountdown--;
                            if (this.deleteCountdown <= 0) {
                                clearInterval(this.countdownTimer);
                            }
                        }, 1000);
                    },

                    cancelDelete() {
                        if (this.countdownTimer) clearInterval(this.countdownTimer);
                        this.deleteModal = false;
                        setTimeout(() => {
                            this.deleteCommentId = null;
                            this.deleteStep = 1;
                        }, 300);
                    },

                    async executeDelete() {
                        if (!this.deleteCommentId || this.isDeleting) return;
                        if (this.countdownTimer) clearInterval(this.countdownTimer);
                        this.isDeleting = true;
                        try {
                            const res = await fetch(this.baseUrl + '/article-comments/' + this.deleteCommentId, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            });
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            const data = await res.json();
                            if (data.success) {
                                const el = document.getElementById('comment-' + this.deleteCommentId);
                                if (el) {
                                    el.style.transition = 'opacity 0.3s, transform 0.3s';
                                    el.style.opacity = '0';
                                    el.style.transform = 'translateX(-10px)';
                                    setTimeout(() => el.remove(), 300);
                                }
                                this.totalComments--;
                                this.deleteModal = false;
                                setTimeout(() => this.deleteCommentId = null, 300);
                            }
                        } catch (err) {
                            console.error('Error deleting:', err);
                            alert('Không thể xóa bình luận.');
                        } finally {
                            this.isDeleting = false;
                            setTimeout(() => this.deleteStep = 1, 300);
                        }
                    },

                    appendComment(comment) {
                        const list = document.getElementById('comments-list');
                        const avatarHtml = comment.user.avatar
                            ? '<img src="' + comment.user.avatar + '" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">'
                            : '<span class="text-xs font-bold text-white">' + comment.user.initial + '</span>';
                        const deleteBtn = this.currentUser.isStaff
                            ? '<button @click="openDeleteModal(' + comment.id + ')" class="text-xs font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">Xóa</button>'
                            : '';
                        const html = '<div class="flex gap-3" id="comment-' + comment.id + '" data-depth="0" style="opacity:0;transform:translateY(10px);transition:all 0.3s">'
                            + '<div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center shrink-0">' + avatarHtml + '</div>'
                            + '<div class="flex-1 min-w-0">'
                            + '<div class="bg-white rounded-xl px-3.5 py-2 border border-gray-200 transition-colors" :class="replyingToId === ' + comment.id + ' ? \'!bg-blue-50 !border-blue-200\' : \'\'">'
                            + '<div class="flex items-center gap-2 mb-0.5"><span class="text-sm font-semibold text-gray-900">' + this.escapeHtml(comment.user.name) + '</span></div>'
                            + '<p class="text-sm text-gray-800 leading-snug whitespace-pre-line break-words">' + this.formatMentions(comment.content) + '</p>'
                            + '</div>'
                            + '<div class="mt-1 flex items-center justify-between pl-3 pr-2 flex-nowrap">'
                            + '<div class="flex items-center gap-4">'
                            + '<span class="text-xs font-medium text-gray-500 hover:underline cursor-pointer" title="' + this.getFullDateString() + '">' + comment.created_at.replace(" trước", "") + '</span>'
                            + '<button @click="toggleLike(' + comment.id + ', $event)" class="group flex items-center gap-1.5 text-[13px] font-bold transition-colors whitespace-nowrap" :class="likedComments[' + comment.id + '] ? \'text-blue-600\' : \'text-gray-500 hover:text-gray-700 hover:underline\'" id="like-btn-' + comment.id + '"><span>Thích</span></button>'
                            + '<button @click="focusReply(' + comment.id + ', ' + comment.id + ', \'' + this.escapeHtml(comment.user.name) + '\')" class="text-xs font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Trả lời</button>'
                            + '<button @click="openReport(' + comment.id + ')" class="text-xs font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Báo cáo</button>'
                            + deleteBtn
                            + '</div>'
                            + '<div x-show="likeCounts[' + comment.id + '] > 0" x-cloak style="display: none" class="flex items-center gap-1 cursor-pointer">'
                            + '<span x-text="likeCounts[' + comment.id + ']" class="text-xs text-gray-500 hover:underline"></span>'
                            + '<div class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center shadow-sm"><svg class="w-2.5 h-2.5 text-white fill-current" viewBox="0 0 24 24"><path d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"></path></svg></div>'
                            + '</div></div></div></div>';
                        list.insertAdjacentHTML('afterbegin', html);
                        this.likedComments[comment.id] = false;
                        this.likeCounts[comment.id] = 0;
                        requestAnimationFrame(() => {
                            const el = document.getElementById('comment-' + comment.id);
                            if (el) { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }
                        });
                    },

                    appendReply(reply) {
                        const parentId = reply.parent_id;
                        const parentEl = document.getElementById('comment-' + parentId);
                        if (!parentEl) return;
                        const parentDepth = Number(parentEl.dataset.depth || 0);
                        const depth = parentDepth + 1;
                        if (depth > 2) return;
                        const rootCommentEl = parentEl.closest('[data-depth="0"]');
                        const rootCommentId = rootCommentEl ? Number(String(rootCommentEl.id).replace('comment-', '')) : parentId;
                        let rcContainer = parentEl.querySelector('.replies-container');
                        let rc;
                        if (depth === 1 && !rcContainer) {
                            const cd = parentEl.querySelector('.flex-1.min-w-0');
                            const rf = cd.querySelector('[x-show="replyTo === ' + parentId + '"]') || cd.lastElementChild;
                            rcContainer = document.createElement('div');
                            rcContainer.className = 'mt-2 text-sm replies-container relative';
                            rcContainer.setAttribute('x-data', '{ expandedReplies: true }');
                            rcContainer.setAttribute('@expand-replies.window', 'if($event.detail.id === ' + parentId + ') expandedReplies = true');
                            
                            const wrapper = document.createElement('div');
                            wrapper.setAttribute('x-show', 'expandedReplies');
                            wrapper.className = 'relative';

                            const borderLine = document.createElement('div');
                            borderLine.className = 'absolute top-0 bottom-4 left-0 border-l-2 border-gray-200';
                            
                            rc = document.createElement('div');
                            rc.className = 'space-y-3 pl-6 mt-2';
                            
                            wrapper.appendChild(borderLine);
                            wrapper.appendChild(rc);
                            rcContainer.appendChild(wrapper);
                            cd.insertBefore(rcContainer, rf);
                            Alpine.initTree(rcContainer);
                        } else if (depth === 1) {
                            rc = rcContainer.querySelector('.space-y-3');
                            window.dispatchEvent(new CustomEvent('expand-replies', { detail: { id: parentId } }));
                        } else {
                            rcContainer = parentEl.querySelector('.nested-replies-container');
                            if (!rcContainer) {
                                const cd = parentEl.querySelector('.flex-1.min-w-0');
                                rcContainer = document.createElement('div');
                                rcContainer.className = 'nested-replies-container mt-2 pl-5 border-l-2 border-gray-200 space-y-3';
                                cd.appendChild(rcContainer);
                            }
                            rc = rcContainer;
                        }
                        const avatarHtml = reply.user.avatar
                            ? '<img src="' + reply.user.avatar + '" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">'
                            : '<span class="text-[10px] font-bold text-white">' + reply.user.initial + '</span>';
                        const deleteBtn = this.currentUser.isStaff
                            ? '<button @click="openDeleteModal(' + reply.id + ')" class="text-[11px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">Xóa</button>'
                            : '';
                        const replyBtn = depth < 2
                            ? '<button @click="focusReply(' + rootCommentId + ', ' + reply.id + ', \'' + this.escapeHtml(reply.user.name) + '\')" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Trả lời</button>'
                            : '';
                        const html = '<div class="flex gap-2.5" id="comment-' + reply.id + '" data-depth="' + depth + '" style="opacity:0;transform:translateY(5px);transition:all 0.3s">'
                            + '<div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shrink-0">' + avatarHtml + '</div>'
                            + '<div class="flex-1 min-w-0">'
                            + '<div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 transition-colors" :class="replyingToId === ' + reply.id + ' ? \'!bg-blue-50 !border-blue-100\' : \'\'">'
                            + '<div class="flex items-center gap-2 mb-0.5"><span class="text-xs font-semibold text-gray-900">' + this.escapeHtml(reply.user.name) + '</span></div>'
                            + '<p class="text-xs text-gray-700 leading-snug whitespace-pre-line break-words">' + this.formatMentions(reply.content) + '</p>'
                            + '</div>'
                            + '<div class="mt-1 flex items-center justify-between pl-2 pr-2 flex-nowrap">'
                            + '<div class="flex items-center gap-3">'
                            + '<span class="text-[11px] font-medium text-gray-500 hover:underline cursor-pointer" title="' + this.getFullDateString() + '">' + reply.created_at.replace(" trước", "") + '</span>'
                            + '<button @click="toggleLike(' + reply.id + ', $event)" class="group flex items-center gap-1.5 text-xs font-bold transition-colors whitespace-nowrap" :class="likedComments[' + reply.id + '] ? \'text-blue-600\' : \'text-gray-500 hover:text-gray-700 hover:underline\'" id="like-btn-' + reply.id + '"><span>Thích</span></button>'
                            + replyBtn
                            + '<button @click="openReport(' + reply.id + ')" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Báo cáo</button>'
                            + deleteBtn
                            + '</div>'
                            + '<div x-show="likeCounts[' + reply.id + '] > 0" x-cloak style="display: none" class="flex items-center gap-1 cursor-pointer">'
                            + '<span x-text="likeCounts[' + reply.id + ']" class="text-[11px] text-gray-500 hover:underline"></span>'
                            + '<div class="w-[14px] h-[14px] rounded-full bg-blue-500 flex items-center justify-center shadow-sm"><svg class="w-2 h-2 text-white fill-current" viewBox="0 0 24 24"><path d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"></path></svg></div>'
                            + '</div></div></div></div>';
                        rc.insertAdjacentHTML('beforeend', html);
                        this.likedComments[reply.id] = false;
                        this.likeCounts[reply.id] = 0;
                        requestAnimationFrame(() => {
                            const el = document.getElementById('comment-' + reply.id);
                            if (el) {
                                el.dataset.depth = String(depth);
                                el.style.opacity = '1';
                                el.style.transform = 'translateY(0)';
                            }
                        });
                    },

                    escapeHtml(text) {
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    },

                    escapeRegExp(string) {
                        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    },

                    formatMentions(text) {
                        let result = this.escapeHtml(text);
                        this.participantNames.forEach(name => {
                            if (!name) return;
                            const regex = new RegExp('(@' + this.escapeRegExp(name) + ')(?![\\p{L}\\p{N}_])', 'gu');
                            result = result.replace(regex, '<span class="inline-block bg-blue-50/80 text-blue-600 font-semibold px-1.5 py-0.5 rounded-md hover:bg-blue-100 transition-colors cursor-pointer">$1</span>');
                        });
                        return result;
                    },

                    getFullDateString() {
                        const now = new Date();
                        const days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
                        return days[now.getDay()] + ', ' + now.getDate() + ' tháng ' + (now.getMonth() + 1) + ', ' + now.getFullYear() + ' lúc ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                    },

                    focusReply(rootId, parentId, username) {
                        this.replyTo = rootId;
                        this.replyParentId = parentId;
                        this.replyingToId = parentId;
                        this.replyContent = '@' + username + ' ';
                        window.dispatchEvent(new CustomEvent('expand-replies', { detail: { id: rootId } }));
                        this.$nextTick(() => {
                            const el = document.getElementById('reply-input-' + rootId);
                            if (el) {
                                el.focus();
                                el.setSelectionRange(el.value.length, el.value.length);
                            }
                        });
                    },
                }
                @endauth
            ));
        });
    </script>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{--  COMMENTS SECTION (Facebook-style)                     --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <section class="bg-gray-50 border-t border-gray-100" x-data="commentSection">
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
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                            @if (auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt=""
                                    class="w-full h-full rounded-full object-cover" loading="lazy">
                            @else
                                <span
                                    class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
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
                        <div class="flex gap-3" id="comment-{{ $comment->id }}" data-depth="0">
                            {{-- Avatar --}}
                            <div
                                class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center shrink-0">
                                @if ($comment->user->avatar ?? false)
                                    <img src="{{ $comment->user->avatar }}" alt=""
                                        class="w-full h-full rounded-full object-cover" loading="lazy">
                                @else
                                    <span
                                        class="text-xs font-bold text-white">{{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                {{-- Comment Bubble --}}
                                <div class="bg-white rounded-xl px-3.5 py-2 border border-gray-200 transition-colors" :class="replyingToId === {{ $comment->id }} ? '!bg-blue-50 !border-blue-200' : ''">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span
                                            class="text-sm font-semibold text-gray-900">{{ $comment->user->name ?? 'Ẩn danh' }}</span>
                                    </div>
                                    <p class="text-sm text-gray-800 leading-snug whitespace-pre-line break-words" x-html="formatMentions(@json($comment->content))">{{ $comment->content }}</p>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-1 flex items-center justify-between pl-3 pr-2">
                                    <div class="flex items-center gap-4">
                                        {{-- Time --}}
                                        <span class="text-xs font-medium text-gray-500 hover:underline cursor-pointer" title="{{ ucfirst($comment->created_at->isoFormat('dddd, D [tháng] M, Y [lúc] HH:mm')) }}">
                                            {{ str_replace(' trước', '', $comment->created_at->diffForHumans()) }}
                                        </span>

                                        {{-- Like --}}
                                        <button @click="toggleLike({{ $comment->id }}, $event)"
                                            class="group flex items-center gap-1.5 text-[13px] font-bold transition-colors whitespace-nowrap"
                                            :class="likedComments[{{ $comment->id }}] ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:underline'"
                                            id="like-btn-{{ $comment->id }}">
                                            <span>Thích</span>
                                        </button>

                                        {{-- Reply --}}
                                        <button
                                            @click="focusReply({{ $comment->id }}, {{ $comment->id }}, '{{ addslashes($comment->user->name ?? 'Ẩn danh') }}')"
                                            class="text-xs font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                            Trả lời
                                        </button>

                                        {{-- Report --}}
                                        <button @click="openReport({{ $comment->id }})"
                                            class="text-xs font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                            Báo cáo
                                        </button>

                                        {{-- Delete (staff only) --}}
                                        @if (auth()->user()->isStaff())
                                            <button @click="openDeleteModal({{ $comment->id }})"
                                                class="text-xs font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">
                                                Xóa
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Like Count Indicator --}}
                                    <div x-show="likeCounts[{{ $comment->id }}] > 0" x-cloak
                                         class="flex items-center gap-1 cursor-pointer"
                                         @if($comment->likes->count() == 0) style="display: none;" @endif>
                                        <span x-text="likeCounts[{{ $comment->id }}]" class="text-xs text-gray-500 hover:underline">
                                            {{ $comment->likes->count() }}
                                        </span>
                                        <div class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center shadow-sm">
                                            <svg class="w-2.5 h-2.5 text-white fill-current" viewBox="0 0 24 24">
                                                <path d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Replies --}}
                                @if ($comment->replies->isNotEmpty())
                                    <div class="mt-2 text-sm replies-container relative" x-data="{ expandedReplies: false }" @expand-replies.window="if($event.detail.id === {{ $comment->id }}) expandedReplies = true">
                                        
                                        <!-- Curve and button (Collapsed state) -->
                                        <div x-show="!expandedReplies" class="relative flex items-center mb-2">
                                            <div class="absolute -left-[0px] top-[-10px] w-5 h-[26px] border-b-2 border-l-2 border-gray-200 rounded-bl-[10px]"></div>
                                            <button @click="expandedReplies = true" class="flex items-center gap-2 text-[13px] font-semibold text-gray-600 hover:text-gray-900 group transition-colors relative z-10 pt-[5px] ml-[26px] focus:outline-none">
                                                @php $firstReplyUser = $comment->replies->first()->user; @endphp
                                                <div class="w-[22px] h-[22px] rounded-full bg-gray-200 flex items-center justify-center shrink-0 border border-white">
                                                    @if ($firstReplyUser->avatar ?? false) 
                                                        <img src="{{ $firstReplyUser->avatar }}" class="w-full h-full rounded-full object-cover">
                                                    @else 
                                                        <span class="text-[10px] font-bold text-gray-500">{{ strtoupper(substr($firstReplyUser->name ?? '?', 0, 1)) }}</span> 
                                                    @endif
                                                </div>
                                                <span class="group-hover:underline">
                                                    <span class="font-bold text-gray-800">{{ $firstReplyUser->name ?? 'Ẩn danh' }}</span> đã trả lời · {{ $comment->replies->count() }} phản hồi
                                                </span>
                                            </button>
                                        </div>

                                        <!-- Expanded State with Vertical Line -->
                                        <div x-show="expandedReplies" x-cloak class="relative">
                                            <div class="absolute top-[-8px] bottom-4 left-[0px] border-l-2 border-gray-200"></div>
                                            <div class="space-y-3 pl-[26px] mt-1">
                                                @foreach ($comment->replies as $reply)
                                            <div class="flex gap-2.5" id="comment-{{ $reply->id }}" data-depth="1">
                                                <div
                                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shrink-0">
                                                    @if ($reply->user->avatar ?? false)
                                                        <img src="{{ $reply->user->avatar }}" alt=""
                                                            class="w-full h-full rounded-full object-cover"
                                                            loading="lazy">
                                                    @else
                                                        <span
                                                            class="text-[10px] font-bold text-white">{{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 transition-colors" :class="replyingToId === {{ $reply->id }} ? '!bg-blue-50 !border-blue-100' : ''">
                                                        <div class="flex items-center gap-2 mb-0.5">
                                                            <span
                                                                class="text-xs font-semibold text-gray-900">{{ $reply->user->name ?? 'Ẩn danh' }}</span>
                                                        </div>
                                                        <p x-html="formatMentions(@json($reply->content))"
                                                            class="text-xs text-gray-700 leading-snug whitespace-pre-line break-words">{{ $reply->content }}</p>
                                                    </div>

                                                    {{-- Reply actions --}}
                                                    <div class="mt-1 flex items-center justify-between pl-2 pr-2">
                                                        <div class="flex items-center gap-3">
                                                            {{-- Time --}}
                                                            <span class="text-[11px] font-medium text-gray-500 hover:underline cursor-pointer" title="{{ ucfirst($reply->created_at->isoFormat('dddd, D [tháng] M, Y [lúc] HH:mm')) }}">
                                                                {{ str_replace(' trước', '', $reply->created_at->diffForHumans()) }}
                                                            </span>

                                                            {{-- Like --}}
                                                            <button @click="toggleLike({{ $reply->id }}, $event)"
                                                            class="group flex items-center gap-1.5 text-xs font-bold transition-colors whitespace-nowrap"
                                                                :class="likedComments[{{ $reply->id }}] ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:underline'"
                                                                id="like-btn-{{ $reply->id }}">
                                                                <span>Thích</span>
                                                            </button>

                                                            {{-- Reply --}}
                                                            <button @click="focusReply({{ $comment->id }}, {{ $reply->id }}, '{{ addslashes($reply->user->name ?? 'Ẩn danh') }}')"
                                                                class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                                Trả lời
                                                            </button>

                                                            {{-- Report --}}
                                                            <button @click="openReport({{ $reply->id }})"
                                                                class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                                Báo cáo
                                                            </button>

                                                            {{-- Delete (staff only) --}}
                                                            @if (auth()->user()->isStaff())
                                                                <button @click="openDeleteModal({{ $reply->id }})"
                                                                    class="text-[11px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">
                                                                    Xóa
                                                                </button>
                                                            @endif
                                                        </div>

                                                        {{-- Like Count Indicator --}}
                                                        <div x-show="likeCounts[{{ $reply->id }}] > 0" x-cloak
                                                             class="flex items-center gap-1 cursor-pointer"
                                                             @if($reply->likes->count() == 0) style="display: none;" @endif>
                                                            <span x-text="likeCounts[{{ $reply->id }}]" class="text-[11px] text-gray-500 hover:underline">
                                                                {{ $reply->likes->count() }}
                                                            </span>
                                                            <div class="w-[14px] h-[14px] rounded-full bg-blue-500 flex items-center justify-center shadow-sm">
                                                                <svg class="w-2 h-2 text-white fill-current" viewBox="0 0 24 24">
                                                                    <path d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($reply->replies->isNotEmpty())
                                                        <div class="nested-replies-container mt-2 pl-5 border-l-2 border-gray-200 space-y-3">
                                                            @foreach ($reply->replies as $nestedReply)
                                                                <div class="flex gap-2.5" id="comment-{{ $nestedReply->id }}" data-depth="2">
                                                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shrink-0">
                                                                        @if ($nestedReply->user->avatar ?? false)
                                                                            <img src="{{ $nestedReply->user->avatar }}" alt=""
                                                                                class="w-full h-full rounded-full object-cover" loading="lazy">
                                                                        @else
                                                                            <span class="text-[10px] font-bold text-white">{{ strtoupper(substr($nestedReply->user->name ?? '?', 0, 1)) }}</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                                                                            <div class="flex items-center gap-2 mb-0.5">
                                                                                <span class="text-xs font-semibold text-gray-900">{{ $nestedReply->user->name ?? 'Ẩn danh' }}</span>
                                                                            </div>
                                                                            <p x-html="formatMentions(@json($nestedReply->content))"
                                                                                class="text-xs text-gray-700 leading-snug whitespace-pre-line break-words">{{ $nestedReply->content }}</p>
                                                                        </div>
                                                                        <div class="mt-1 flex items-center justify-between pl-2 pr-2">
                                                                            <div class="flex items-center gap-3">
                                                                                <span class="text-[11px] font-medium text-gray-500 hover:underline cursor-pointer" title="{{ ucfirst($nestedReply->created_at->isoFormat('dddd, D [tháng] M, Y [lúc] HH:mm')) }}">
                                                                                    {{ str_replace(' trước', '', $nestedReply->created_at->diffForHumans()) }}
                                                                                </span>
                                                                                <button @click="toggleLike({{ $nestedReply->id }}, $event)"
                                                                                    class="group flex items-center gap-1.5 text-xs font-bold transition-colors whitespace-nowrap"
                                                                                    :class="likedComments[{{ $nestedReply->id }}] ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:underline'"
                                                                                    id="like-btn-{{ $nestedReply->id }}">
                                                                                    <span>Thích</span>
                                                                                </button>
                                                                                <button @click="openReport({{ $nestedReply->id }})"
                                                                                    class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                                                    Báo cáo
                                                                                </button>
                                                                                @if (auth()->user()->isStaff())
                                                                                    <button @click="openDeleteModal({{ $nestedReply->id }})"
                                                                                        class="text-[11px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">
                                                                                        Xóa
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                            <div x-show="likeCounts[{{ $nestedReply->id }}] > 0" x-cloak
                                                                                class="flex items-center gap-1 cursor-pointer"
                                                                                @if($nestedReply->likes->count() == 0) style="display: none;" @endif>
                                                                                <span x-text="likeCounts[{{ $nestedReply->id }}]" class="text-[11px] text-gray-500 hover:underline">
                                                                                    {{ $nestedReply->likes->count() }}
                                                                                </span>
                                                                                <div class="w-[14px] h-[14px] rounded-full bg-blue-500 flex items-center justify-center shadow-sm">
                                                                                    <svg class="w-2 h-2 text-white fill-current" viewBox="0 0 24 24">
                                                                                        <path d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"></path>
                                                                                    </svg>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Reply Form (AJAX) --}}
                                <div x-show="replyTo === {{ $comment->id }}" x-cloak style="display: none"
                                    class="mt-3 ml-2 pl-4 border-l-2 border-blue-200">
                                    <form @submit.prevent="submitReply($event)">
                                        <textarea x-model="replyContent" id="reply-input-{{ $comment->id }}" rows="2" required maxlength="1000"
                                            placeholder="Trả lời {{ $comment->user->name ?? '' }}..."
                                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-800
                                                     focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none"></textarea>
                                        <div class="mt-1.5 flex gap-2 justify-end">
                                            <button type="button" @click="replyTo = null; replyParentId = null; replyingToId = null; replyContent = ''"
                                                class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">Hủy</button>
                                            <button type="submit" :disabled="submittingReply || !replyContent.trim()"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
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
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p class="text-gray-700 font-medium mb-1">Bạn đang nghĩ gì về bài viết này?</p>
                    <p class="text-sm text-gray-500 mb-5">Đăng nhập để chia sẻ ý kiến của bạn với cộng đồng nhé!</p>
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
                        <label
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="report_reason" :value="reason"
                                x-model="selectedReason"
                                class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700" x-text="reason"></span>
                        </label>
                    </template>
                </div>
                <div class="px-5 py-4 border-t border-gray-100 flex gap-2 justify-end">
                    <button @click="reportModal = false; selectedReason = ''"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Hủy</button>
                    <button @click="submitReport()" :disabled="!selectedReason || submittingReport"
                        class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50">
                        <span x-text="submittingReport ? 'Đang gửi...' : 'Gửi báo cáo'"></span>
                    </button>
                </div>
            </div>
        </div>

        @auth
            {{-- ── Delete Dialog ──────────────────────────────── --}}
            <div x-show="deleteModal" x-transition.opacity x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                @click.self="cancelDelete()" style="display: none">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden" @click.stop>
                    
                    <!-- Step 1: Initial Warning -->
                    <div x-show="deleteStep === 1">
                        <div class="px-5 py-5 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900">Xóa bình luận</h3>
                            <p class="text-[13px] text-gray-500 mt-1 leading-relaxed">Bạn có chắc chắn muốn xóa bình luận này không?</p>
                        </div>
                        <div class="px-5 py-3.5 bg-gray-50 flex gap-2 justify-end">
                            <button @click="confirmDelete()"
                                class="min-w-[80px] flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors gap-2">
                                Xóa
                            </button>
                            <button @click="cancelDelete()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 rounded-lg transition-colors">Hủy</button>
                        </div>
                    </div>

                    <!-- Step 2: Countdown Warning -->
                    <div x-show="deleteStep === 2" x-cloak>
                        <div class="px-5 py-5 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900">Cảnh báo cuối cùng</h3>
                            <p class="text-[13px] text-gray-500 mt-1 leading-relaxed">Hành động này không thể hoàn tác. Bạn đã chắc chắn đọc kỹ và muốn xóa bình luận này chưa?</p>
                        </div>
                        <div class="px-5 py-3.5 bg-gray-50 flex gap-2 justify-end">
                            <button @click="executeDelete()" :disabled="isDeleting || deleteCountdown > 0"
                                class="min-w-[110px] flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 gap-2 disabled:cursor-not-allowed">
                                <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-show="deleteCountdown > 0" x-text="'Đồng ý (' + deleteCountdown + 's)'"></span>
                                <span x-show="deleteCountdown <= 0" x-text="isDeleting ? 'Đang xóa...' : 'Đồng ý'"></span>
                            </button>
                            <button @click="cancelDelete()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 rounded-lg transition-colors">Hủy</button>
                        </div>
                    </div>

                </div>
            </div>
        @endauth
    </section>

</x-app-layout>
