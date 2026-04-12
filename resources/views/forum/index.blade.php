<x-app-layout>
    <x-slot:title>Diễn đàn</x-slot:title>

{{-- ── Main content ──────────────────────────────────────────── --}}
<div class="bg-gray-50 min-h-screen pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="forumIndex()" x-init="init()">

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
                        {{-- "Tất cả" button --}}
                        <button @click="filterCategory('')"
                           class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-all"
                           :class="activeCategory === '' ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'">
                            Tất cả
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold"
                                  :class="activeCategory === '' ? 'bg-sky-100 text-sky-600' : 'bg-gray-100 text-gray-500'"
                                  x-text="globalTotal"></span>
                        </button>

                        @foreach($categories as $cat)
                            <button @click="filterCategory('{{ $cat->slug }}')"
                               class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium transition-all"
                               :class="activeCategory === '{{ $cat->slug }}' ? 'bg-sky-50 text-sky-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'">
                                {{ $cat->name }}
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold"
                                      :class="activeCategory === '{{ $cat->slug }}' ? 'bg-sky-100 text-sky-600' : 'bg-gray-100 text-gray-500'">
                                    {{ $cat->threads_count }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- ══ Thread List ═══════════════════════════════════════ --}}
            <div class="lg:col-span-3 space-y-4">

                {{-- Search bar --}}
                <form @submit.prevent="searchThreads()" class="flex gap-3">
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="searchQuery" placeholder="Tìm kiếm bài viết..."
                               class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition-all shadow-sm">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-sky-500 text-white text-sm font-semibold rounded-xl
                                   hover:bg-sky-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        Tìm
                    </button>
                </form>

                {{-- Loading skeleton --}}
                <template x-if="loading">
                    <div class="space-y-4">
                        <template x-for="i in 4" :key="i">
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 animate-pulse">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 shrink-0"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Thread list (AJAX-rendered) --}}
                <template x-if="!loading">
                    <div>
                        <template x-if="threads.length === 0">
                            <div class="text-center py-16">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <p class="text-gray-600 text-lg font-medium">Chưa có bài viết nào</p>
                                <p class="text-gray-400 text-sm mt-1">Hãy là người đầu tiên tạo bài viết!</p>
                            </div>
                        </template>

                        <template x-for="thread in threads" :key="thread.id">
                            <a :href="thread.url"
                               class="block bg-white rounded-2xl border border-gray-200 shadow-sm p-5 group
                                      hover:shadow-md hover:border-gray-300 transition-all duration-200 mb-4">
                                <div class="flex items-start gap-4">
                                    {{-- Avatar --}}
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center overflow-hidden shrink-0 ring-2 ring-white shadow-sm">
                                        <template x-if="thread.user.avatar">
                                            <img :src="thread.user.avatar" alt="" class="w-full h-full object-cover" loading="lazy">
                                        </template>
                                        <template x-if="!thread.user.avatar">
                                            <span class="text-xs font-bold text-white" x-text="thread.user.initial"></span>
                                        </template>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        {{-- Title + badges --}}
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <template x-if="thread.is_pinned">
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                                    📌 Ghim
                                                </span>
                                            </template>
                                            <template x-if="thread.is_locked">
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                                    🔒 Khóa
                                                </span>
                                            </template>
                                            <h3 class="text-base font-semibold text-gray-900 group-hover:text-sky-600 transition-colors truncate" x-text="thread.title"></h3>
                                        </div>

                                        {{-- Meta --}}
                                        <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500">
                                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium" x-text="thread.category.name"></span>
                                            <span class="font-medium text-gray-700" x-text="thread.user.name"></span>
                                            <template x-if="thread.user.active_title">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold border"
                                                      :style="`color: ${thread.user.active_title.color_hex}; border-color: ${thread.user.active_title.color_hex}40; background-color: ${thread.user.active_title.color_hex}15;`"
                                                      x-text="thread.user.active_title.name"></span>
                                            </template>
                                            <span>·</span>
                                            <span x-text="thread.created_at"></span>
                                            <span>·</span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span x-text="thread.views_count"></span>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                <span x-text="thread.replies_count"></span>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Latest reply --}}
                                    <template x-if="thread.latest_reply">
                                        <div class="hidden sm:block text-right shrink-0">
                                            <p class="text-xs text-gray-600 truncate max-w-[120px] font-medium" x-text="thread.latest_reply.user_name"></p>
                                            <p class="text-xs text-gray-400" x-text="thread.latest_reply.created_at"></p>
                                        </div>
                                    </template>
                                </div>
                            </a>
                        </template>

                        {{-- Pagination --}}
                        <template x-if="pagination.lastPage > 1">
                            <div class="mt-6 flex items-center justify-center gap-2">
                                <button @click="goToPage(pagination.currentPage - 1)"
                                        :disabled="pagination.currentPage <= 1"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg
                                               hover:bg-gray-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    ←
                                </button>
                                <template x-for="page in paginationPages()" :key="page">
                                    <button @click="if(page !== '...') goToPage(page)"
                                            class="min-w-[36px] px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                                            :class="page === pagination.currentPage
                                                ? 'bg-sky-500 text-white shadow-sm'
                                                : (page === '...' ? 'text-gray-400 cursor-default' : 'text-gray-600 bg-white border border-gray-200 hover:bg-gray-50')"
                                            x-text="page">
                                    </button>
                                </template>
                                <button @click="goToPage(pagination.currentPage + 1)"
                                        :disabled="pagination.currentPage >= pagination.lastPage"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg
                                               hover:bg-gray-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    →
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function forumIndex() {
    return {
        threads: [],
        loading: false,
        activeCategory: '{{ request('category', '') }}',
        searchQuery: '{{ request('q', '') }}',
        globalTotal: {{ \App\Models\ForumThread::count() }},
        totalThreads: {{ $threads->total() }},
        currentPage: {{ $threads->currentPage() }},
        pagination: {
            currentPage: {{ $threads->currentPage() }},
            lastPage: {{ $threads->lastPage() }},
            total: {{ $threads->total() }},
        },
        baseUrl: '{{ route('forum.index') }}',

        init() {
            // Hydrate from server-rendered data on first load
            @php
                $threadsJson = $threads->through(function ($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'slug' => $thread->slug,
                        'url' => route('forum.show', $thread),
                        'is_pinned' => $thread->is_pinned,
                        'is_locked' => $thread->is_locked,
                        'views_count' => $thread->views_count,
                        'replies_count' => $thread->replies_count,
                        'created_at' => $thread->created_at->diffForHumans(),
                        'user' => [
                            'id' => $thread->user->id,
                            'name' => $thread->user->name,
                            'avatar' => $thread->user->avatar,
                            'initial' => strtoupper(substr($thread->user->name, 0, 1)),
                            'active_title' => $thread->user->activeTitle ? [
                                'name' => $thread->user->activeTitle->name,
                                'color_hex' => $thread->user->activeTitle->color_hex,
                            ] : null,
                        ],
                        'category' => [
                            'name' => $thread->category->name,
                        ],
                        'latest_reply' => $thread->latestReply ? [
                            'user_name' => $thread->latestReply->user->name ?? '—',
                            'created_at' => $thread->latestReply->created_at->diffForHumans(),
                        ] : null,
                    ];
                })->values();
            @endphp
            this.threads = {!! json_encode($threadsJson) !!};
        },

        async fetchThreads(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.activeCategory) params.set('category', this.activeCategory);
                if (this.searchQuery.trim()) params.set('q', this.searchQuery.trim());
                params.set('page', page);

                const res = await fetch(this.baseUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                this.threads = data.threads.data;
                this.totalThreads = data.total;
                this.pagination = {
                    currentPage: data.threads.current_page,
                    lastPage: data.threads.last_page,
                    total: data.threads.total,
                };

                // Update URL without reload
                const url = new URL(window.location);
                url.searchParams.delete('category');
                url.searchParams.delete('q');
                url.searchParams.delete('page');
                if (this.activeCategory) url.searchParams.set('category', this.activeCategory);
                if (this.searchQuery.trim()) url.searchParams.set('q', this.searchQuery.trim());
                if (page > 1) url.searchParams.set('page', page);
                window.history.replaceState({}, '', url);

            } catch (err) {
                console.error('Error fetching threads:', err);
            } finally {
                this.loading = false;
            }
        },

        filterCategory(slug) {
            if (this.activeCategory === slug) return;
            this.activeCategory = slug;
            this.fetchThreads(1);
        },

        searchThreads() {
            this.fetchThreads(1);
        },

        goToPage(page) {
            if (page < 1 || page > this.pagination.lastPage) return;
            this.fetchThreads(page);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        paginationPages() {
            const current = this.pagination.currentPage;
            const last = this.pagination.lastPage;
            const pages = [];
            if (last <= 7) {
                for (let i = 1; i <= last; i++) pages.push(i);
            } else {
                pages.push(1);
                if (current > 3) pages.push('...');
                for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) {
                    pages.push(i);
                }
                if (current < last - 2) pages.push('...');
                pages.push(last);
            }
            return pages;
        }
    };
}
</script>

</x-app-layout>
