<x-app-layout>
    <x-slot:title>{{ $article->title }}</x-slot:title>

    {{-- ── Article Header ──────────────────────────────────── --}}
    <article class="max-w-[720px] mx-auto px-4 py-8">

        {{-- Time --}}
        @php
            $days = [
                'Sunday' => 'Chủ nhật',
                'Monday' => 'Thứ hai',
                'Tuesday' => 'Thứ ba',
                'Wednesday' => 'Thứ tư',
                'Thursday' => 'Thứ năm',
                'Friday' => 'Thứ sáu',
                'Saturday' => 'Thứ bảy',
            ];
            $pubDate = $article->published_at;
            $dateStr = '';
            if ($pubDate) {
                $dayOfWeek = $days[$pubDate->format('l')] ?? '';
                $dateStr = $dayOfWeek . ', ' . $pubDate->format('d/m/Y, H:i') . ' (GMT+7)';
            }
        @endphp
        @if ($dateStr)
            <p class="mb-4 text-[13px] text-gray-500 font-medium">
                {{ $dateStr }}
            </p>
        @endif

        {{-- Title --}}
        <h1
            class="text-2xl md:text-3xl lg:text-[2rem] font-extrabold text-gray-900 leading-tight tracking-tight font-outfit">
            {{ $article->title }}
        </h1>

        {{-- Subtitle --}}
        @if ($article->subtitle)
            <p class="mt-4 text-[15px] md:text-base font-medium text-gray-600 leading-relaxed">
                {{ $article->subtitle }}
            </p>
        @endif

        {{-- Ratings --}}
        <div class="mt-5 mb-8">
            @include('news.partials.article-ratings')
        </div>

        {{-- ── Lưới lọc CSS (CSS Shield): Tự động sửa lỗi Copy-Paste và định dạng sai từ Editor ── --}}
        <style>
            /* 1. Xóa gạch ngang rác do copy-paste */
            .article-body hr {
                display: none !important;
            }

            /* 2. Ép toàn bộ ẢNH và VIDEO (Iframe) thành khối chuẩn: Căn giữa, bo góc, cách lề đều đặn */
            /* Kể cả khi gõ nhiều ảnh dính chùm trong 1 thẻ <p> hoặc có width="NaN", nó vẫn tự dàn đều chữ dọc */
            .article-body img,
            .article-body iframe {
                display: block !important;
                max-width: 100% !important;
                height: auto !important;
                margin: 2rem auto 0.5rem auto !important;
                border-radius: 0.75rem !important;
            }

            .article-body img {
                border: 1px solid #f3f4f6 !important;
            }

            /* 3. Phá bỏ lỗi dấu xuống dòng (Shift+Enter / thẻ <br>) nằm án ngữ giữa hình ảnh/video và phụ đề */
            .article-body img+br,
            .article-body iframe+br {
                display: none !important;
            }

            /* 4. Ép trọn gói Phụ đề: Dù là <figcaption> hay lỡ tay bôi đen chọn size Nhỏ (<small>), Đổi màu, hay In nghiêng thì cũng bị bẻ lại về chuẩn đen thuần, căn giữa */
            .article-body figcaption,
            .article-body figcaption *,
            .article-body p>small {
                display: block !important;
                color: #111827 !important;
                /* Xóa màu xám, ép về đen */
                font-size: 15px !important;
                font-family: inherit !important;
                font-style: normal !important;
                /* Xóa in nghiêng */
                text-align: center !important;
                margin-top: 0.5rem !important;
                margin-bottom: 2rem !important;
                line-height: 1.5 !important;
            }
        </style>

        {{-- Content --}}
        <div
            class="article-body mt-8 prose prose-lg prose-gray max-w-none
                prose-headings:font-outfit prose-headings:text-gray-900
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-a:text-sky-600 prose-a:no-underline hover:prose-a:underline">
            {!! $article->content !!}
        </div>

        {{-- Author --}}
        <div class="mt-8 flex justify-end">
            @if ($article->user)
                <a href="{{ route('profile.show', $article->user->slug) }}"
                    class="group flex items-center gap-2 hover:bg-gray-50 border border-transparent hover:border-gray-200 rounded-full pr-3 py-1 transition-all"
                    title="Xem hồ sơ">
                    <span
                        class="text-base font-bold text-gray-900 group-hover:text-sky-600 transition-colors">{{ $article->user->name }}</span>
                    @if ($article->user->avatar)
                        <img src="{{ $article->user->avatar }}" alt="{{ $article->user->name }}"
                            class="w-8 h-8 rounded-full object-cover border border-gray-200 ml-1">
                    @else
                        <div
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-sky-100 text-sky-600 font-bold text-sm ml-1">
                            {{ strtoupper(substr($article->user->name, 0, 1)) }}
                        </div>
                    @endif
                </a>
            @else
                <p class="text-base font-bold text-gray-900 pr-3 py-1">
                    <strong>Ẩn danh</strong>
                </p>
            @endif
        </div>

        {{-- Tags (Inline) --}}
        @if ($article->tags->isNotEmpty())
            <div class="mt-4 flex flex-wrap items-center gap-1.5 text-sm text-gray-800">
                <span class="font-bold">Từ khóa:</span>
                @foreach ($article->tags as $tag)
                    <a href="{{ route('news.index', ['tag' => $tag->slug]) }}"
                        class="uppercase hover:text-sky-600 transition-colors">
                        {{ $tag->name }}
                    </a>{{ !$loop->last ? ',' : '' }}
                @endforeach
            </div>
        @endif

        {{-- Article Footer --}}
        <div class="mt-10 pt-6 border-t border-gray-200 flex items-center justify-between text-sm text-gray-600">
            <span>{{ $article->views_count }} lượt xem</span>
            <a href="{{ route('news.index') }}"
                class="flex items-center gap-1 text-gray-600 hover:text-gray-900 transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Quay lại tin tức
            </a>
        </div>
    </article>

    {{-- ── Alpine.js Comment Logic ── --}}
    <script>
        document.addEventListener('alpine:init', () => {
        Alpine.data('commentSection', () => (
                @guest {
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
            @auth {
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
                commentData: {
                    @foreach ($article->comments as $c)
                        {{ $c->id }}: @json($c->content),
                        @foreach ($c->replies as $r)
                            {{ $r->id }}: @json($r->content),
                            @foreach ($r->replies as $rr)
                                {{ $rr->id }}: @json($rr->content),
                            @endforeach
                        @endforeach
                    @endforeach
                },

                participantNames: [
                    @foreach ($article->comments as $c)
                        @json($c->user->name ?? ''),
                        @foreach ($c->replies as $r)
                            @json($r->user->name ?? ''),
                            @foreach ($r->replies as $rr)
                                @json($rr->user->name ?? ''),
                            @endforeach
                        @endforeach
                    @endforeach
                ].filter((v, i, a) => v && a.indexOf(v) === i),

                deleteModal: false,
                deleteCommentId: null,
                isDeleting: false,
                deleteStep: 1,
                deleteCountdown: 5,
                countdownTimer: null,

                currentUser: {
                    id: @json(auth()->user()->id ?? null),
                },

                editingCommentId: null,
                editingContent: '',
                submittingEdit: false,

                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
                baseUrl: '{{ url('/') }}',

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
                            return 'Phiên đăng nhập đã hết hạn. V vui lòng đăng nhập lại.';
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

                startEdit(uuid) {
                    this.editingCommentId = uuid;
                    this.editingContent = this.commentData[uuid] || '';
                    this.$nextTick(() => {
                        const el = document.getElementById('edit-input-' + id);
                        if (el) {
                            el.focus();
                            el.setSelectionRange(el.value.length, el.value.length);
                        }
                    });
                },

                cancelEdit() {
                    this.editingCommentId = null;
                    this.editingContent = '';
                },

                async submitEdit() {
                    if (this.submittingEdit || !this.editingContent.trim() || !this.editingCommentId)
                        return;
                    this.submittingEdit = true;
                    try {
                        const res = await fetch(this.baseUrl + '/article-comments/' + this
                            .editingCommentId, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    content: this.editingContent
                                })
                            });
                        if (!res.ok) throw new Error(await this.parseApiError(res));
                        const data = await res.json();
                        if (data.success) {
                            this.commentData[this.editingCommentId] = data.content;
                            const textEl = document.getElementById('comment-text-' + this
                                .editingCommentId);
                            if (textEl) textEl.innerHTML = this.formatMentions(data.content);
                            this.editingCommentId = null;
                            this.editingContent = '';
                        }
                    } catch (err) {
                        alert(err?.message || 'Không thể cập nhật bình luận.');
                    } finally {
                        this.submittingEdit = false;
                    }
                },

                async submitComment(e) {
                    if (this.submitting || !this.newComment.trim()) return;
                    this.submitting = true;
                    try {
                        const res = await fetch('{{ route('article-comments.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                article_id: {{ $article->id }},
                                content: this.newComment
                            }),
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
                    } finally {
                        this.submitting = false;
                    }
                },

                async submitReply(e) {
                    if (this.submittingReply || !this.replyContent.trim()) return;
                    if (!this.replyParentId) return;
                    this.submittingReply = true;
                    try {
                        const res = await fetch('{{ route('article-comments.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                article_id: {{ $article->id }},
                                parent_id: this.replyParentId,
                                content: this.replyContent
                            }),
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
                    } finally {
                        this.submittingReply = false;
                    }
                },

                async toggleLike(commentId, e) {
                    try {
                        const res = await fetch(this.baseUrl + '/article-comments/' + commentId +
                            '/like', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken,
                                    'Accept': 'application/json'
                                },
                            });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const data = await res.json();
                        this.likedComments[commentId] = data.isLiked;
                        this.likeCounts[commentId] = data.likesCount;
                    } catch (err) {
                        console.error('Error toggling like:', err);
                    }
                },

                    } finally {
                        this.submittingReport = false;
                    }
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

                openReport(id) {
                    window.dispatchEvent(new CustomEvent('open-report', {
                        detail: { type: 'ArticleComment', id: id }
                    }));
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
                        const res = await fetch(this.baseUrl + '/article-comments/' + this
                            .deleteCommentId, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': this.csrfToken,
                                    'Accept': 'application/json'
                                },
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
                    const avatarHtml = comment.user.avatar ?
                        '<img src="' + comment.user.avatar +
                        '" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">' :
                        '<span class="text-xs font-bold text-white">' + comment.user.initial + '</span>';
                    const deleteBtn = (this.currentUser.isStaff || this.currentUser.id === comment.user
                            .id) ?
                        '<button @click="openDeleteModal(' + comment.uuid +
                        ')" class="text-[13px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">Xóa</button>' :
                        '';
                    const editBtn = (this.currentUser.id === comment.user.id) ?
                        '<button @click="startEdit(' + comment.uuid +
                        ')" class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Sửa</button>' :
                        '';
                    const ringClass = (comment.user.active_frame && comment.user.active_frame.image_path) ?
                        'scale-[1.05]' : 'ring-2 ring-sky-300';
                    const frameHtml = (comment.user.active_frame && comment.user.active_frame.image_path) ?
                        '<img src="' + comment.user.active_frame.image_path +
                        '" alt="" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">' :
                        '';
                    const profileUrl = this.baseUrl + '/profile/' + (comment.user.slug || comment.user.id);

                    const ringClass = (comment.user.active_frame && comment.user.active_frame.image_path) ? 'scale-[1.0475]' : 'ring-2 ring-sky-300';
                    const frameHtml = (comment.user.active_frame && comment.user.active_frame.image_path) ?
                        '<img src="' + comment.user.active_frame.image_path +
                        '" alt="" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">' :
                        '';
                    const profileUrl = this.baseUrl + '/profile/' + (comment.user.slug || comment.user.id);

                    const reportBtn = (this.currentUser.id && this.currentUser.id !== comment.user.id) ?
                        '<button @click="openReport(' + comment.uuid +
                        ')" class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Báo cáo</button>' : '';

                    const html = '<div class="flex gap-3" id="comment-' + comment.uuid +
                        '" data-depth="0" style="opacity:0;transform:translateY(10px);transition:all 0.3s">' +
                        '<a href="' + profileUrl +
                        '" class="relative group w-9 h-9 shrink-0 transition-all duration-300"><div class="w-full h-full rounded-full bg-gradient-to-br from-gray-300 to-gray-500 overflow-hidden flex items-center justify-center transition-all duration-300 ' +
                        ringClass + '">' +
                        avatarHtml + '</div>' + frameHtml + '</a>' +
                        '<div class="flex-1 min-w-0">' +
                        '<div class="bg-white rounded-xl px-3.5 py-2 border border-gray-200 transition-colors" :class="replyingToId === ' +
                        comment.uuid + ' ? \'!bg-blue-50 !border-blue-200\' : \'\'">' +
                        '<div class="flex items-center gap-2 mb-0.5"><a href="' + profileUrl +
                        '" class="text-[15px] font-bold text-gray-900 hover:text-sky-600 hover:underline transition-colors">' +
                        this.escapeHtml(comment.user.name) + '</a></div>' +
                        '<div x-show="editingCommentId !== ' + comment.uuid + '"><p id="comment-text-' +
                        comment.uuid +
                        '" class="text-[15px] text-gray-800 leading-relaxed whitespace-pre-line break-words">' +
                        this.formatMentions(comment.content) + '</p></div>' +
                        '<div x-show="editingCommentId === ' + comment.uuid +
                        '" x-cloak style="display:none">' +
                        '<textarea id="edit-input-' + comment.uuid +
                        '" x-model="editingContent" @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); submitEdit(); }" class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none mt-1" rows="2"></textarea>' +
                        '<div class="flex gap-2 justify-end mt-1.5"><button @click="cancelEdit()" class="text-xs text-gray-500 hover:underline px-2 py-1">Hủy</button><button @click="submitEdit()" class="text-xs font-semibold bg-blue-500 text-white rounded px-3 py-1.5 mt-1 hover:bg-blue-600 disabled:opacity-50" :disabled="submittingEdit || !editingContent.trim()">Lưu</button></div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="mt-1 flex items-center justify-between pl-3 pr-2 flex-nowrap">' +
                        '<div class="flex items-center gap-4">' +
                        '<span class="text-[13px] font-medium text-gray-500 hover:underline cursor-pointer" title="' +
                        this.getFullDateString() + '">' + comment.created_at.replace(" trước", "") +
                        '</span>' +
                        '<button @click="toggleLike(' + comment.uuid +
                        ', $event)" class="group flex items-center gap-1.5 text-[13px] font-bold transition-colors whitespace-nowrap" :class="likedComments[' +
                        comment.uuid +
                        '] ? \'text-rose-500\' : \'text-gray-500 hover:text-gray-700 hover:underline\'" id="like-btn-' +
                        comment.uuid + '"><span>Thích</span></button>' +
                        '<button @click="focusReply(' + comment.uuid + ', ' + comment.uuid + ', \'' + this
                        .escapeHtml(comment.user.name) +
                        '\')" class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Trả lời</button>' +
                        reportBtn +
                        editBtn +
                        deleteBtn +
                        '</div>' +
                        '<div x-show="likeCounts[' + comment.uuid +
                        '] > 0" x-cloak style="display: none" class="flex items-center gap-1 cursor-pointer">' +
                        '<span x-text="likeCounts[' + comment.uuid +
                        ']" class="text-xs text-gray-500 hover:underline"></span>' +
                        '<div class="w-4 h-4 rounded-full bg-rose-500 flex items-center justify-center shadow-sm"><svg class="w-2.5 h-2.5 text-white fill-current" viewBox="0 0 24 24"><path d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"></path></svg></div>' +
                        '</div></div></div></div>';
                    list.insertAdjacentHTML('afterbegin', html);
                    this.commentData[comment.uuid] = comment.content;
                    this.likedComments[comment.uuid] = false;
                    this.likeCounts[comment.uuid] = 0;
                    requestAnimationFrame(() => {
                        const el = document.getElementById('comment-' + comment.uuid);
                        if (el) {
                            el.style.opacity = '1';
                            el.style.transform = 'translateY(0)';
                        }
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
                    const rootCommentId = rootCommentEl ? Number(String(rootCommentEl.id).replace(
                        'comment-', '')) : parentId;
                    let rcContainer = parentEl.querySelector('.replies-container');
                    let rc;
                    if (depth === 1 && !rcContainer) {
                        const cd = parentEl.querySelector('.flex-1.min-w-0');
                        const rf = cd.querySelector('[x-show="replyTo === ' + parentId + '"]') || cd
                            .lastElementChild;
                        rcContainer = document.createElement('div');
                        rcContainer.className = 'mt-2 text-sm replies-container relative';
                        rcContainer.setAttribute('x-data', '{ expandedReplies: true }');
                        rcContainer.setAttribute('@expand-replies.window', 'if($event.detail.id === ' +
                            parentId + ') expandedReplies = true');

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
                        window.dispatchEvent(new CustomEvent('expand-replies', {
                            detail: {
                                id: parentId
                            }
                        }));
                    } else {
                        rcContainer = parentEl.querySelector('.nested-replies-container');
                        if (!rcContainer) {
                            const cd = parentEl.querySelector('.flex-1.min-w-0');
                            rcContainer = document.createElement('div');
                            rcContainer.className =
                                'nested-replies-container mt-2 pl-5 border-l-2 border-gray-200 space-y-3';
                            cd.appendChild(rcContainer);
                        }
                        rc = rcContainer;
                    }
                    const avatarHtml = reply.user.avatar ?
                        '<img src="' + reply.user.avatar +
                        '" alt="" class="w-full h-full rounded-full object-cover" loading="lazy">' :
                        '<span class="text-[10px] font-bold text-white">' + reply.user.initial + '</span>';
                    const deleteBtn = (this.currentUser.isStaff || this.currentUser.id === reply.user.id) ?
                        '<button @click="openDeleteModal(' + reply.uuid +
                        ')" class="text-[13px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">Xóa</button>' :
                        '';
                    const editBtn = (this.currentUser.id === reply.user.id) ? '<button @click="startEdit(' +
                        reply.uuid +
                        ')" class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Sửa</button>' :
                        '';

                    const replyBtn = depth < 2 ?
                        '<button @click="focusReply(' + rootCommentId + ', ' + reply.uuid + ', \'' + this
                        .escapeHtml(reply.user.name) +
                        '\')" class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Trả lời</button>' :
                        '';
                    const frameHtml = (reply.user.active_frame && reply.user.active_frame.image_path) ?
                        '<img src="' + reply.user.active_frame.image_path +
                        '" alt="" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">' :
                        '';
                    const replyProfileUrl = this.baseUrl + '/profile/' + (reply.user.slug || reply.user.id);
                    const reportBtn = (this.currentUser.id && this.currentUser.id !== reply.user.id) ?
                        '<button @click="openReport(' + reply.uuid +
                        ')" class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">Báo cáo</button>' : '';

                    const ringClass = (reply.user.active_frame && reply.user.active_frame.image_path) ?
                        'scale-[1.0475]' : 'ring-2 ring-sky-300';
                    const html = '<div class="flex gap-2.5" id="comment-' + reply.uuid + '" data-depth="' +
                        depth + '" style="opacity:0;transform:translateY(5px);transition:all 0.3s">' +
                        '<a href="' + replyProfileUrl +
                        '" class="relative group w-7 h-7 shrink-0 transition-all duration-300"><div class="w-full h-full rounded-full bg-gradient-to-br from-gray-300 to-gray-400 overflow-hidden flex items-center justify-center transition-all duration-300 ' +
                        ringClass + '">' +
                        avatarHtml + '</div>' + frameHtml + '</a>' +
                        '<div class="flex-1 min-w-0">' +
                        '<div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 transition-colors" :class="replyingToId === ' +
                        reply.uuid + ' ? \'!bg-blue-50 !border-blue-100\' : \'\'">' +
                        '<div class="flex items-center gap-2 mb-0.5"><a href="' + replyProfileUrl +
                        '" class="text-sm font-bold text-gray-900 hover:text-sky-600 hover:underline transition-colors">' +
                        this.escapeHtml(reply.user.name) + '</a></div>' +
                        '<div x-show="editingCommentId !== ' + reply.uuid + '"><p id="comment-text-' + reply
                        .uuid +
                        '" class="text-[15px] text-gray-800 leading-relaxed whitespace-pre-line break-words">' +
                        this.formatMentions(reply.content) + '</p></div>' +
                        '<div x-show="editingCommentId === ' + reply.uuid +
                        '" x-cloak style="display:none">' +
                        '<textarea id="edit-input-' + reply.uuid +
                        '" x-model="editingContent" @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); submitEdit(); }" class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none mt-1" rows="2"></textarea>' +
                        '<div class="flex gap-2 justify-end mt-1.5"><button @click="cancelEdit()" class="text-xs text-gray-500 hover:underline px-2 py-1">Hủy</button><button @click="submitEdit()" class="text-xs font-semibold bg-blue-500 text-white rounded px-3 py-1.5 mt-1 hover:bg-blue-600 disabled:opacity-50" :disabled="submittingEdit || !editingContent.trim()">Lưu</button></div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="mt-1 flex items-center justify-between pl-2 pr-2 flex-nowrap">' +
                        '<div class="flex items-center gap-3">' +
                        '<span class="text-[13px] font-medium text-gray-500 hover:underline cursor-pointer" title="' +
                        this.getFullDateString() + '">' + reply.created_at.replace(" trước", "") +
                        '</span>' +
                        '<button @click="toggleLike(' + reply.uuid +
                        ', $event)" class="group flex items-center gap-1.5 text-[13px] font-bold transition-colors whitespace-nowrap" :class="likedComments[' +
                        reply.uuid +
                        '] ? \'text-rose-500\' : \'text-gray-500 hover:text-gray-700 hover:underline\'" id="like-btn-' +
                        reply.uuid + '"><span>Thích</span></button>' +
                        replyBtn +
                        editBtn +
                        reportBtn +
                        deleteBtn +
                        '</div>' +
                        '<div x-show="likeCounts[' + reply.uuid +
                        '] > 0" x-cloak style="display: none" class="flex items-center gap-1 cursor-pointer">' +
                        '<span x-text="likeCounts[' + reply.uuid +
                        ']" class="text-[11px] text-gray-500 hover:underline"></span>' +
                        '<div class="w-[14px] h-[14px] rounded-full bg-rose-500 flex items-center justify-center shadow-sm"><svg class="w-2 h-2 text-white fill-current" viewBox="0 0 24 24"><path d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z"></path></svg></div>' +
                        '</div></div></div></div>';
                    rc.insertAdjacentHTML('beforeend', html);
                    this.commentData[reply.uuid] = reply.content;
                    this.likedComments[reply.uuid] = false;
                    this.likeCounts[reply.uuid] = 0;
                    requestAnimationFrame(() => {
                        const el = document.getElementById('comment-' + reply.uuid);
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
                        const regex = new RegExp('(@' + this.escapeRegExp(name) +
                            ')(?![\\p{L}\\p{N}_])', 'gu');
                        result = result.replace(regex,
                            '<span class="text-[#0866FF] font-semibold hover:underline cursor-pointer">$1</span>'
                        );
                    });
                    return result;
                },

                getFullDateString() {
                    const now = new Date();
                    const days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu',
                        'Thứ bảy'
                    ];
                    return days[now.getDay()] + ', ' + now.getDate() + ' tháng ' + (now.getMonth() + 1) +
                        ', ' + now.getFullYear() + ' lúc ' + String(now.getHours()).padStart(2, '0') + ':' +
                        String(now.getMinutes()).padStart(2, '0');
                },

                focusReply(rootId, parentId, username) {
                    this.replyTo = rootId;
                    this.replyParentId = parentId;
                    this.replyingToId = parentId;
                    this.replyContent = '@' + username + ' ';
                    window.dispatchEvent(new CustomEvent('expand-replies', {
                        detail: {
                            id: rootId
                        }
                    }));
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
                            class="w-10 h-10 shrink-0 mt-0.5 relative group transition-all duration-300">
                            <div class="w-full h-full rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center overflow-hidden transition-all duration-300 {{ auth()->user()->activeFrame ? 'scale-[1.0475]' : '' }}">
                                @if (auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt=""
                                        class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <span
                                        class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            @if (auth()->user()->activeFrame)
                                <img src="{{ Storage::url(auth()->user()->activeFrame->image_path) }}" alt=""
                                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                            @endif
                        </div>
                        <div class="flex-1">
                            <textarea x-model="newComment" @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); submitComment($event); }"
                                rows="3" required maxlength="1000" placeholder="Viết bình luận của bạn..."
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400
                                         focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none"></textarea>
                            <div class="mt-2 flex justify-end">
                                <button type="submit" :disabled="submitting || !newComment.trim()" title="Bình luận"
                                    class="inline-flex items-center justify-center w-11 h-11 bg-blue-500 text-white rounded-xl shadow-sm shadow-blue-500/20
                                           hover:bg-blue-600 active:scale-[0.95] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        fill="currentColor" class="w-[22px] h-[22px] ml-1">
                                        <path
                                            d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                                    </svg>
                                    <svg x-show="submitting" x-cloak class="animate-spin w-5 h-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
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
                            <a href="{{ route('profile.show', $comment->user) }}"
                                class="w-9 h-9 shrink-0 relative group">
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-gray-300 to-gray-500 overflow-hidden flex items-center justify-center transition-all duration-300 {{ $comment->user->activeFrame ?? false ? 'scale-[1.0475]' : 'group-hover:ring-2 group-hover:ring-sky-300' }}">
                                    @if ($comment->user->avatar ?? false)
                                        <img src="{{ $comment->user->avatar }}" alt=""
                                            class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <span
                                            class="text-xs font-bold text-white">{{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}</span>
                                    @endif
                                </div>
                                @if ($comment->user->activeFrame ?? false)
                                    <img src="{{ Storage::url($comment->user->activeFrame->image_path) }}" alt=""
                                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                {{-- Comment Bubble --}}
                                <div class="bg-white rounded-xl px-3.5 py-2 border border-gray-200 transition-colors"
                                    :class="replyingToId === {{ $comment->id }} ? '!bg-blue-50 !border-blue-200' : ''">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <a href="{{ route('profile.show', $comment->user) }}"
                                            class="text-[15px] font-bold text-gray-900 hover:text-sky-600 hover:underline transition-colors">{{ $comment->user->name ?? 'Ẩn danh' }}</a>
                                    </div>
                                    <div x-show="editingCommentId !== {{ $comment->id }}">
                                        <p id="comment-text-{{ $comment->id }}"
                                            class="text-[15px] text-gray-800 leading-relaxed whitespace-pre-line break-words"
                                            x-html='formatMentions(commentData[{{ $comment->id }}])'></p>
                                    </div>
                                    <div x-show="editingCommentId === {{ $comment->id }}" x-cloak style="display: none">
                                        <textarea id="edit-input-{{ $comment->id }}" x-model="editingContent"
                                            @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); submitEdit(); }"
                                            class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none mt-1"
                                            rows="2"></textarea>
                                        <div class="flex gap-2 justify-end mt-1.5">
                                            <button @click="cancelEdit()"
                                                class="text-xs text-gray-500 hover:underline px-2 py-1">Hủy</button>
                                            <button @click="submitEdit()"
                                                class="text-xs font-semibold bg-blue-500 text-white rounded px-3 py-1.5 hover:bg-blue-600 disabled:opacity-50"
                                                :disabled="submittingEdit || !editingContent.trim()">Lưu</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-1 flex items-center justify-between pl-3 pr-2">
                                    <div class="flex items-center gap-4">
                                        {{-- Time --}}
                                        <span class="text-[13px] font-medium text-gray-500 hover:underline cursor-pointer"
                                            title="{{ ucfirst($comment->created_at->isoFormat('dddd, D [tháng] M, Y [lúc] HH:mm')) }}">
                                            {{ str_replace(' trước', '', $comment->created_at->diffForHumans()) }}
                                        </span>

                                        {{-- Like --}}
                                        <button @click="toggleLike({{ $comment->id }}, $event)"
                                            class="group flex items-center gap-1.5 text-[13px] font-bold transition-colors whitespace-nowrap"
                                            :class="likedComments[{{ $comment->id }}] ? 'text-rose-500' :
                                                'text-gray-500 hover:text-gray-700 hover:underline'"
                                            id="like-btn-{{ $comment->id }}">
                                            <span>Thích</span>
                                        </button>

                                        {{-- Reply --}}
                                        <button
                                            @click="focusReply({{ $comment->id }}, {{ $comment->id }}, '{{ addslashes($comment->user->name ?? 'Ẩn danh') }}')"
                                            class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                            Trả lời
                                        </button>

                                        {{-- Report --}}
                                        @if(auth()->check() && auth()->id() !== $comment->user_id)
                                            <button @click="openReport({{ $comment->id }})"
                                                class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                Báo cáo
                                            </button>
                                        @endif

                                        {{-- Edit (owner) --}}
                                        @if (auth()->id() === $comment->user_id)
                                            <button @click="startEdit({{ $comment->id }})"
                                                class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                Sửa
                                            </button>
                                        @endif

                                        {{-- Delete (owner or staff) --}}
                                        @if (auth()->id() === $comment->user_id || auth()->user()->isStaff())
                                            <button @click="openDeleteModal({{ $comment->id }})"
                                                class="text-[13px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">
                                                Xóa
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Like Count Indicator --}}
                                    <div x-show="likeCounts[{{ $comment->id }}] > 0" x-cloak
                                        class="flex items-center gap-1 cursor-pointer" @if ($comment->likes->count() == 0)
                                        style="display: none;"
                    @endif>
                    <span x-text="likeCounts[{{ $comment->id }}]" class="text-[13px] text-gray-500 hover:underline">
                        {{ $comment->likes->count() }}
                    </span>
                    <div class="w-4 h-4 rounded-full bg-rose-500 flex items-center justify-center shadow-sm">
                        <svg class="w-2.5 h-2.5 text-white fill-current" viewBox="0 0 24 24">
                            <path
                                d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Replies --}}
            @if ($comment->replies->isNotEmpty())
                <div class="mt-2 text-sm replies-container relative" x-data="{ expandedReplies: false }"
                    @expand-replies.window="if($event.detail.id === {{ $comment->id }}) expandedReplies = true">

                    <!-- Curve and button (Collapsed state) -->
                    <div x-show="!expandedReplies" class="relative flex items-center mb-2">
                        <div
                            class="absolute -left-[0px] top-[-10px] w-5 h-[26px] border-b-2 border-l-2 border-gray-200 rounded-bl-[10px]">
                        </div>
                        <button @click="expandedReplies = true"
                            class="flex items-center gap-2 text-[14px] font-bold text-gray-700 hover:text-gray-900 group transition-colors relative z-10 pt-[3px] ml-[26px] focus:outline-none">
                            @php
                                $firstReplyUser = $comment->replies->first()->user;
                                $totalReplies =
                                    $comment->replies->count() +
                                    $comment->replies->sum(function ($r) {
                                        return $r->replies ? $r->replies->count() : 0;
                                    });
                            @endphp
                            <div
                                class="w-[24px] h-[24px] rounded-full bg-gray-200 flex items-center justify-center shrink-0 border border-white">
                                @if ($firstReplyUser->avatar ?? false)
                                    <img src="{{ $firstReplyUser->avatar }}"
                                        class="w-full h-full rounded-full object-cover">
                                @else
                                    <span
                                        class="text-[11px] font-bold text-gray-500">{{ strtoupper(substr($firstReplyUser->name ?? '?', 0, 1)) }}</span>
                                @endif
                            </div>
                            <span class="group-hover:underline">
                                <span class="font-bold text-gray-900">{{ $firstReplyUser->name ?? 'Ẩn danh' }}</span> đã
                                trả lời · {{ $totalReplies }} phản hồi
                            </span>
                        </button>
                    </div>

                    <!-- Expanded State with Vertical Line -->
                    <div x-show="expandedReplies" x-cloak class="relative">
                        <div class="absolute top-[-8px] bottom-4 left-[0px] border-l-2 border-gray-200"></div>
                        <div class="space-y-3 pl-[26px] mt-1">
                            @foreach ($comment->replies as $reply)
                                <div class="flex gap-2.5" id="comment-{{ $reply->id }}" data-depth="1">
                                    <a href="{{ route('profile.show', $reply->user) }}"
                                        class="w-7 h-7 shrink-0 relative group">
                                        <div class="w-full h-full rounded-full bg-gradient-to-br from-gray-300 to-gray-400 overflow-hidden flex items-center justify-center transition-all duration-300 {{ $reply->user->activeFrame ?? false ? 'scale-[1.0475]' : 'group-hover:ring-2 group-hover:ring-sky-300' }}">
                                            @if ($reply->user->avatar ?? false)
                                                <img src="{{ $reply->user->avatar }}" alt=""
                                                    class="w-full h-full object-cover" loading="lazy">
                                            @else
                                                <span
                                                    class="text-[10px] font-bold text-white">{{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        @if ($reply->user->activeFrame ?? false)
                                            <img src="{{ Storage::url($reply->user->activeFrame->image_path) }}"
                                                alt=""
                                                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                                        @endif
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 transition-colors"
                                            :class="replyingToId === {{ $reply->id }} ? '!bg-blue-50 !border-blue-100' : ''">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <a href="{{ route('profile.show', $reply->user) }}"
                                                    class="text-sm font-bold text-gray-900 hover:text-sky-600 hover:underline transition-colors">{{ $reply->user->name ?? 'Ẩn danh' }}</a>
                                            </div>
                                            <div x-show="editingCommentId !== {{ $reply->id }}">
                                                <p id="comment-text-{{ $reply->id }}"
                                                    x-html='formatMentions(commentData[{{ $reply->id }}])'
                                                    class="text-[15px] text-gray-800 leading-relaxed whitespace-pre-line break-words">
                                                </p>
                                            </div>
                                            <div x-show="editingCommentId === {{ $reply->id }}" x-cloak
                                                style="display: none">
                                                <textarea id="edit-input-{{ $reply->id }}" x-model="editingContent"
                                                    @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); submitEdit(); }"
                                                    class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none mt-1"
                                                    rows="2"></textarea>
                                                <div class="flex gap-2 justify-end mt-1.5">
                                                    <button @click="cancelEdit()"
                                                        class="text-xs text-gray-500 hover:underline px-2 py-1">Hủy</button>
                                                    <button @click="submitEdit()"
                                                        class="text-xs font-semibold bg-blue-500 text-white rounded px-3 py-1.5 mt-1 hover:bg-blue-600 disabled:opacity-50"
                                                        :disabled="submittingEdit || !editingContent.trim()">Lưu</button>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Reply actions --}}
                                        <div class="mt-1 flex items-center justify-between pl-2 pr-2">
                                            <div class="flex items-center gap-3">
                                                {{-- Time --}}
                                                <span
                                                    class="text-[13px] font-medium text-gray-500 hover:underline cursor-pointer"
                                                    title="{{ ucfirst($reply->created_at->isoFormat('dddd, D [tháng] M, Y [lúc] HH:mm')) }}">
                                                    {{ str_replace(' trước', '', $reply->created_at->diffForHumans()) }}
                                                </span>

                                                {{-- Like --}}
                                                <button @click="toggleLike({{ $reply->id }}, $event)"
                                                    class="group flex items-center gap-1.5 text-[13px] font-bold transition-colors whitespace-nowrap"
                                                    :class="likedComments[{{ $reply->id }}] ? 'text-rose-500' :
                                                        'text-gray-500 hover:text-gray-700 hover:underline'"
                                                    id="like-btn-{{ $reply->id }}">
                                                    <span>Thích</span>
                                                </button>

                                                {{-- Reply --}}
                                                <button
                                                    @click="focusReply({{ $comment->id }}, {{ $reply->id }}, '{{ addslashes($reply->user->name ?? 'Ẩn danh') }}')"
                                                    class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                    Trả lời
                                                </button>

                                                {{-- Report --}}
                                                @if(auth()->check() && auth()->id() !== $reply->user_id)
                                                    <button @click="openReport({{ $reply->id }})"
                                                        class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                        Báo cáo
                                                    </button>
                                                @endif

                                                {{-- Edit (owner) --}}
                                                @if (auth()->id() === $reply->user_id)
                                                    <button @click="startEdit({{ $reply->id }})"
                                                        class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                        Sửa
                                                    </button>
                                                @endif

                                                {{-- Delete (owner or staff) --}}
                                                @if (auth()->id() === $reply->user_id || auth()->user()->isStaff())
                                                    <button @click="openDeleteModal({{ $reply->id }})"
                                                        class="text-[13px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">
                                                        Xóa
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Like Count Indicator --}}
                                            <div x-show="likeCounts[{{ $reply->id }}] > 0" x-cloak
                                                class="flex items-center gap-1 cursor-pointer" @if ($reply->likes->count() == 0)
                                                style="display: none;"
                            @endif>
                            <span x-text="likeCounts[{{ $reply->id }}]"
                                class="text-[13px] text-gray-500 hover:underline">
                                {{ $reply->likes->count() }}
                            </span>
                            <div
                                class="w-[14px] h-[14px] rounded-full bg-rose-500 flex items-center justify-center shadow-sm">
                                <svg class="w-2 h-2 text-white fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @if ($reply->replies->isNotEmpty())
                        <div class="nested-replies-container mt-2 pl-5 border-l-2 border-gray-200 space-y-3">
                            @foreach ($reply->replies as $nestedReply)
                                <div class="flex gap-2.5" id="comment-{{ $nestedReply->id }}" data-depth="2">
                                    <a href="{{ route('profile.show', $nestedReply->user) }}"
                                        class="w-7 h-7 shrink-0 relative group">
                                        <div class="w-full h-full rounded-full bg-gradient-to-br from-gray-300 to-gray-400 overflow-hidden flex items-center justify-center transition-all duration-300 {{ $nestedReply->user->activeFrame ?? false ? 'scale-[1.0475]' : 'group-hover:ring-2 group-hover:ring-sky-300' }}">
                                            @if ($nestedReply->user->avatar ?? false)
                                                <img src="{{ $nestedReply->user->avatar }}" alt=""
                                                    class="w-full h-full object-cover" loading="lazy">
                                            @else
                                                <span
                                                    class="text-[10px] font-bold text-white">{{ strtoupper(substr($nestedReply->user->name ?? '?', 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        @if ($nestedReply->user->activeFrame ?? false)
                                            <img src="{{ Storage::url($nestedReply->user->activeFrame->image_path) }}"
                                                alt=""
                                                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[126%] h-[126%] max-w-none object-contain pointer-events-none z-10 transition-all duration-300">
                                        @endif
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <a href="{{ route('profile.show', $nestedReply->user) }}"
                                                    class="text-sm font-bold text-gray-900 hover:text-sky-600 hover:underline transition-colors">{{ $nestedReply->user->name ?? 'Ẩn danh' }}</a>
                                            </div>
                                            <div x-show="editingCommentId !== {{ $nestedReply->id }}">
                                                <p id="comment-text-{{ $nestedReply->id }}"
                                                    x-html='formatMentions(commentData[{{ $nestedReply->id }}])'
                                                    class="text-[15px] text-gray-800 leading-relaxed whitespace-pre-line break-words">
                                                </p>
                                            </div>
                                            <div x-show="editingCommentId === {{ $nestedReply->id }}" x-cloak
                                                style="display: none">
                                                <textarea id="edit-input-{{ $nestedReply->id }}" x-model="editingContent"
                                                    @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); submitEdit(); }"
                                                    class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none mt-1"
                                                    rows="2"></textarea>
                                                <div class="flex gap-2 justify-end mt-1.5">
                                                    <button @click="cancelEdit()"
                                                        class="text-xs text-gray-500 hover:underline px-2 py-1">Hủy</button>
                                                    <button @click="submitEdit()"
                                                        class="text-xs font-semibold bg-blue-500 text-white rounded px-3 py-1.5 mt-1 hover:bg-blue-600 disabled:opacity-50"
                                                        :disabled="submittingEdit || !editingContent.trim()">Lưu</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-1 flex items-center justify-between pl-2 pr-2">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="text-[13px] font-medium text-gray-500 hover:underline cursor-pointer"
                                                    title="{{ ucfirst($nestedReply->created_at->isoFormat('dddd, D [tháng] M, Y [lúc] HH:mm')) }}">
                                                    {{ str_replace(' trước', '', $nestedReply->created_at->diffForHumans()) }}
                                                </span>
                                                <button @click="toggleLike({{ $nestedReply->id }}, $event)"
                                                    class="group flex items-center gap-1.5 text-[13px] font-bold transition-colors whitespace-nowrap"
                                                    :class="likedComments[{{ $nestedReply->id }}] ? 'text-rose-500' :
                                                        'text-gray-500 hover:text-gray-700 hover:underline'"
                                                    id="like-btn-{{ $nestedReply->id }}">
                                                    <span>Thích</span>
                                                </button>
                                                @if(auth()->check() && auth()->id() !== $nestedReply->user_id)
                                                    <button @click="openReport({{ $nestedReply->id }})"
                                                        class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                        Báo cáo
                                                    </button>
                                                @endif
                                                @if (auth()->id() === $nestedReply->user_id)
                                                    <button @click="startEdit({{ $nestedReply->id }})"
                                                        class="text-[13px] font-semibold text-gray-500 hover:text-gray-700 hover:underline transition-colors whitespace-nowrap">
                                                        Sửa
                                                    </button>
                                                @endif
                                                @if (auth()->id() === $nestedReply->user_id || auth()->user()->isStaff())
                                                    <button @click="openDeleteModal({{ $nestedReply->id }})"
                                                        class="text-[13px] font-semibold text-gray-500 hover:text-red-500 hover:underline transition-colors whitespace-nowrap">
                                                        Xóa
                                                    </button>
                                                @endif
                                            </div>
                                            <div x-show="likeCounts[{{ $nestedReply->id }}] > 0" x-cloak
                                                class="flex items-center gap-1 cursor-pointer" @if ($nestedReply->likes->count() == 0)
                                                style="display: none;"
                            @endif>
                            <span x-text="likeCounts[{{ $nestedReply->id }}]"
                                class="text-[13px] text-gray-500 hover:underline">
                                {{ $nestedReply->likes->count() }}
                            </span>
                            <div
                                class="w-[14px] h-[14px] rounded-full bg-rose-500 flex items-center justify-center shadow-sm">
                                <svg class="w-2 h-2 text-white fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M4 21h1V8H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2zM20.28 8H14V4.11a2.11 2.11 0 0 0-2.11-2.11c-.48 0-.94.19-1.29.54L5 8.12v12.76l6.83 1.13c.44.07.89.11 1.34.11H19a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2z">
                                    </path>
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
                    <textarea x-model="replyContent"
                        @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); submitReply($event); }"
                        id="reply-input-{{ $comment->id }}" rows="2" required maxlength="1000"
                        placeholder="Trả lời {{ $comment->user->name ?? '' }}..."
                        class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-800
                                                     focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all resize-none"></textarea>
                    <div class="mt-1.5 flex gap-2 justify-end">
                        <button type="button"
                            @click="replyTo = null; replyParentId = null; replyingToId = null; replyContent = ''"
                            class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700 transition-colors">Hủy</button>
                        <button type="submit" :disabled="submittingReply || !replyContent.trim()" title="Trả lời"
                            class="inline-flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-lg shadow-sm hover:bg-blue-600 active:scale-[0.95] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="!submittingReply" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor" class="w-[18px] h-[18px] ml-0.5">
                                <path
                                    d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                            </svg>
                            <svg x-show="submittingReply" x-cloak class="animate-spin w-4 h-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
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
                            <p class="text-[13px] text-gray-500 mt-1 leading-relaxed">Bạn có chắc chắn muốn xóa bình luận
                                này không?</p>
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
                            <p class="text-[13px] text-gray-500 mt-1 leading-relaxed">Hành động này không thể hoàn tác. Bạn
                                đã chắc chắn đọc kỹ và muốn xóa bình luận này chưa?</p>
                        </div>
                        <div class="px-5 py-3.5 bg-gray-50 flex gap-2 justify-end">
                            <button @click="executeDelete()" :disabled="isDeleting || deleteCountdown > 0"
                                class="min-w-[110px] flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 gap-2 disabled:cursor-not-allowed">
                                <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
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

    {{-- ── Nút Cuộn lên đầu (Back to Top) ────────────────────── --}}
    <button x-data="{ show: false }" @scroll.window="show = window.pageYOffset > 400" x-show="show"
        x-transition.opacity.duration.300ms @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-8 right-8 z-40 w-12 h-12 bg-white border border-gray-200 rounded-2xl shadow-sm flex items-center justify-center text-black hover:bg-gray-50 hover:shadow-md transition-all group"
        aria-label="Lên đầu trang" style="display: none;">
        <svg class="w-6 h-6 group-hover:-translate-y-1 transition-transform duration-300" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

</x-app-layout>
