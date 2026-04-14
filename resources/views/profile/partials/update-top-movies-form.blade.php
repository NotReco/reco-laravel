<section>
    <header>
        <h2 class="text-xl font-display font-bold text-gray-900">
            Top 4 Phim Tâm Đắc
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Chọn 4 bộ phim yêu thích nhất của bạn để ghim lên hồ sơ cá nhân.
        </p>
    </header>

    <form id="top-movies-form" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" data-unsaved-bar data-unsaved-title="Top 4 phim tâm đắc">
        @csrf
        @method('patch')
        <input type="hidden" name="top_movies_submitted" value="1">

        <div x-data="topMoviesManager()" class="space-y-6">
            {{-- Selected Movies --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <template x-for="(movie, index) in selected" :key="index">
                    <div class="relative group aspect-[2/3] bg-gray-50 border border-gray-200 shadow-sm rounded-xl overflow-hidden flex flex-col items-center justify-center transition-all hover:border-sky-400 hover:shadow-md">
                        <template x-if="movie">
                            <div class="w-full h-full relative">
                                <img :src="movie.poster" :alt="movie.title" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2">
                                    <button type="button" @click.prevent="removeMovie(index)" class="p-2.5 bg-red-500 text-white hover:bg-red-600 rounded-full shadow-lg transition-colors scale-90 group-hover:scale-100 transform duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                <input type="hidden" name="top_movies[]" :value="movie.id">
                            </div>
                        </template>

                        <template x-if="!movie">
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-4 text-center cursor-pointer hover:bg-gray-100 transition-colors" @click.prevent="openSearch(index)">
                                <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span class="text-xs font-bold uppercase tracking-widest">Trống</span>
                            </div>
                        </template>

                        <div class="absolute top-2 left-2 w-6 h-6 bg-white/90 backdrop-blur border border-gray-200/50 shadow-sm text-gray-700 text-xs font-bold rounded flex items-center justify-center z-10" x-text="index + 1"></div>
                    </div>
                </template>
            </div>

            {{-- Inline Toast --}}
            <div
                x-show="toastMsg"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="flex items-center gap-2.5 px-4 py-3 mb-4 bg-amber-500/10 border border-amber-500/30 rounded-xl"
                x-cloak
            >
                <svg class="w-5 h-5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-amber-200" x-text="toastMsg"></p>
            </div>

            {{-- Modal Search --}}
            <div x-show="searching" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
                <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="closeSearch()"></div>
                
                <div class="relative bg-white border border-gray-100 rounded-3xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden" @click.stop>
                    <div class="p-5 border-b border-gray-100 flex gap-3 relative bg-gray-50/50 rounded-t-3xl">
                        <div class="absolute inset-y-0 left-5 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="query" @input.debounce.500ms="fetchMovies" placeholder="Nhập tên phim..." class="w-full bg-white border border-gray-200 text-gray-900 shadow-sm rounded-xl focus:border-sky-500 focus:ring-sky-500 pl-11 pr-4 py-3 placeholder:text-gray-400">
                        <button type="button" @click="closeSearch()" class="p-3 text-gray-500 hover:text-gray-900 bg-white border border-gray-200 shadow-sm rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-5 overflow-y-auto flex-1 min-h-[300px]">
                        <template x-if="loading">
                            <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                                <svg class="animate-spin h-8 w-8 text-sky-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="font-medium">Đang tìm kiếm...</span>
                            </div>
                        </template>

                        <template x-if="!loading && results.length === 0 && query.length > 0">
                            <div class="text-center py-12 text-gray-500 font-medium">Không tìm thấy phim nào phù hợp.</div>
                        </template>

                        <div class="grid sm:grid-cols-2 gap-3" x-show="!loading && results.length > 0">
                            <template x-for="item in results" :key="item.id">
                                <button type="button" @click.prevent="selectMovie(item)" class="flex gap-3 text-left p-2.5 rounded-2xl hover:bg-sky-50 transition-colors border border-transparent hover:border-sky-100 group">
                                    <img :src="item.poster" class="w-12 h-16 object-cover rounded-xl shadow-sm shrink-0">
                                    <div class="flex flex-col justify-center min-w-0">
                                        <span class="font-bold text-gray-900 truncate text-sm" x-text="item.title"></span>
                                        <span class="text-xs text-gray-500 font-medium mt-0.5" x-text="item.release_date ? item.release_date.split('-')[0] : 'N/A'"></span>
                                    </div>
                                    <div class="ml-auto my-auto opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="px-2.5 py-1 bg-sky-100 text-sky-700 text-xs font-bold rounded-lg shadow-sm">Chọn</span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="hidden" aria-hidden="true" tabindex="-1">Lưu</button>
    </form>
</section>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('topMoviesManager', () => ({
            selected: [null, null, null, null],
            currentIndex: null,
            searching: false,
            query: '',
            results: [],
            loading: false,
            toastMsg: '',
            toastTimer: null,

            init() {
                // Initialize with existing
                @php
                    $topMoviesJson = $user->topMovies->map(function($m) {
                        $poster = $m->poster;
                        if ($poster && !str_starts_with($poster, 'http')) {
                            $poster = '/storage/' . $poster;
                        }
                        return ['id' => $m->id, 'title' => $m->title, 'poster' => $poster];
                    })->values();
                @endphp
                const existing = @json($topMoviesJson);
                existing.forEach((movie, idx) => {
                    if (idx < 4) this.selected[idx] = movie;
                });
            },

            openSearch(index) {
                this.currentIndex = index;
                this.searching = true;
                this.query = '';
                this.results = [];
                // Focus input smoothly after render
                setTimeout(() => {
                    const input = this.$el.querySelector('input[type="text"]');
                    if (input) input.focus();
                }, 100);
            },

            closeSearch() {
                this.searching = false;
                this.currentIndex = null;
            },

            async fetchMovies() {
                if (this.query.trim().length < 2) {
                    this.results = [];
                    return;
                }
                this.loading = true;
                try {
                    const res = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    
                    // Format posters
                    this.results = data.map(m => ({
                        ...m,
                        poster: m.poster.startsWith('http') ? m.poster : `/storage/${m.poster}`
                    }));
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            showToast(msg, duration = 3000) {
                clearTimeout(this.toastTimer);
                this.toastMsg = msg;
                this.toastTimer = setTimeout(() => { this.toastMsg = ''; }, duration);
            },

            selectMovie(movie) {
                // Check if already selected elsewhere
                const exists = this.selected.findIndex(m => m && m.id === movie.id);
                if (exists !== -1 && exists !== this.currentIndex) {
                    this.showToast('Phim này đã có trong Top 4 của bạn!');
                    return;
                }

                this.selected[this.currentIndex] = movie;
                
                // Wait for Alpine to render hidden inputs, then notify unsaved bar
                this.$nextTick(() => {
                    this.$el.dispatchEvent(new Event('change', { bubbles: true }));
                });
                
                this.closeSearch();
            },

            removeMovie(index) {
                this.selected[index] = null;
                // Wait for Alpine to remove hidden inputs, then notify unsaved bar
                this.$nextTick(() => {
                    this.$el.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        }));
    });
</script>
@endpush
