<x-app-layout>
    <x-slot:title>Khám phá Phim bộ</x-slot:title>

    <div class="bg-gray-50 min-h-screen pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="exploreFilter()" @popstate.window="handlePopstate">

            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Left Sidebar: Filters & Sort --}}
                <div class="w-full lg:w-72 flex-shrink-0">
                    <div class="mb-4 flex items-center justify-between">
                        <h1 class="text-2xl font-display font-bold text-gray-900">Bộ lọc</h1>
                    </div>

                    <form action="{{ route('tv-shows.index') }}" method="GET" id="explore-filter-form" class="space-y-4"
                        @submit.prevent="fetchResults()">
                        {{-- Retain text search parameter if exists --}}
                        <template x-if="searchQuery">
                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-700 mb-2">Kết quả tìm kiếm cho:</div>
                                <div class="flex items-center justify-between bg-sky-50 border border-sky-200 text-sky-700 px-3 py-2 rounded-lg">
                                    <span class="font-semibold truncate">
                                        "<span x-text="searchQuery"></span>"
                                    </span>
                                    <button type="button" @click="clearSearchQuery()" class="text-sky-500 hover:text-sky-700 focus:outline-none ml-2 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <input type="hidden" name="q" x-model="searchQuery" :disabled="!searchQuery">

                        {{-- Unified Sort & Filters Panel --}}
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                            {{-- Sort Section --}}
                            <div x-data="{ open: true }" class="border-b border-gray-100">
                                <button type="button" @click="open = !open"
                                    class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-semibold text-gray-900">Sắp xếp</span>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse>
                                    <div class="px-5 pb-5 pt-1">
                                        <label class="block text-sm text-gray-700 font-medium mb-2">Sắp xếp kết quả
                                            theo</label>
                                        <select name="sort"
                                            class="w-full text-sm py-2.5 pr-8 rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                                            @change="fetchResults()">
                                            <option value="popularity_desc"
                                                {{ in_array($sort, ['popularity_desc', 'latest']) ? 'selected' : '' }}>Phổ biến nhất
                                            </option>
                                            <option value="rating_desc" {{ $sort === 'rating_desc' ? 'selected' : '' }}>
                                                Đánh giá cao nhất</option>
                                            <option value="release_date_desc"
                                                {{ $sort === 'release_date_desc' ? 'selected' : '' }}>
                                                Mới nhất</option>
                                            <option value="release_date_asc"
                                                {{ $sort === 'release_date_asc' ? 'selected' : '' }}>Cũ nhất</option>
                                            <option value="title_asc" {{ $sort === 'title_asc' ? 'selected' : '' }}>Tên
                                                phim bộ (A-Z)</option>
                                            <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>
                                                Tên phim bộ (Z-A)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Filters Section --}}
                            <div x-data="{ open: true }">
                                <button type="button" @click="open = !open"
                                    class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <span class="font-semibold text-gray-900">Bộ lọc</span>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse>
                                    <div class="px-5 pb-5 pt-1 space-y-5">

                                        {{-- Genres --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <label class="block text-sm font-bold text-gray-900 uppercase tracking-wider">Thể loại phổ biến</label>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($genres as $genre)
                                                    <label class="cursor-pointer group">
                                                        <input type="checkbox" name="genres[]"
                                                            value="{{ $genre->id }}" class="peer sr-only"
                                                            {{ in_array($genre->id, request('genres', [])) || request('genre') == $genre->id ? 'checked' : '' }}
                                                            @change="fetchResults()">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[13px] rounded-full border border-gray-200 bg-white text-gray-700 font-medium transition-all duration-200 group-hover:border-gray-300 group-hover:bg-gray-50 peer-checked:bg-sky-500 peer-checked:border-sky-500 peer-checked:text-white shadow-sm hover:shadow peer-checked:shadow-sky-200">
                                                            {{ $genre->name }}
                                                            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-500 transition-colors peer-checked:bg-white/20 peer-checked:text-white">
                                                                {{ $genre->tv_shows_count }}
                                                            </span>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <hr class="border-gray-100">

                                        {{-- Release Year --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-3">Năm phát
                                                hành</label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" name="year_from"
                                                    value="{{ request('year_from') }}" placeholder="Từ năm"
                                                    class="w-full text-sm py-2.5 rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                                                    min="1800" max="{{ date('Y') + 5 }}" step="1" onkeydown="if(['-','+','e','E','.'].includes(event.key)) event.preventDefault();" @change="fetchResults()">
                                                <span class="text-gray-500">-</span>
                                                <input type="number" name="year_to" value="{{ request('year_to') }}"
                                                    placeholder="Đến năm"
                                                    class="w-full text-sm py-2.5 rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                                                    min="1800" max="{{ date('Y') + 5 }}" step="1" onkeydown="if(['-','+','e','E','.'].includes(event.key)) event.preventDefault();" @change="fetchResults()">
                                            </div>
                                        </div>

                                        <hr class="border-gray-100">

                                        {{-- Country --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-3">Quốc gia</label>
                                            <select name="country"
                                                class="w-full text-sm py-2.5 pr-8 rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                                                @change="fetchResults()">
                                                <option value="">Tất cả quốc gia</option>
                                                @foreach ($countries as $code => $name)
                                                    <option value="{{ $code }}"
                                                        {{ request('country') === $code ? 'selected' : '' }}>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <hr class="border-gray-100">

                                        {{-- Min Rating --}}
                                        <div x-data="{ minRating: {{ request('min_rating', 0) }} }">
                                            <div class="flex justify-between items-center mb-3">
                                                <label class="block text-sm font-medium text-gray-900">Điểm người
                                                    dùng</label>
                                                <span
                                                    class="text-xs font-bold bg-gray-100 text-gray-800 px-2 py-0.5 rounded"
                                                    x-text="minRating > 0 ? '≥ ' + minRating : 'Tất cả'"></span>
                                            </div>
                                                <input type="range" name="min_rating" min="0" max="10"
                                                    step="1" x-model="minRating" class="w-full accent-sky-600"
                                                    @change="fetchResults()">
                                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                    <span>0</span>
                                                    <span>10</span>
                                                </div>
                                            </div>

                                            <hr class="border-gray-100">

                                            {{-- Adult Content --}}
                                            <div>
                                                <label class="flex items-center cursor-pointer group">
                                                    <div class="relative flex items-center justify-center w-5 h-5 mr-3">
                                                        <input type="checkbox" name="adult_content" value="1"
                                                            class="peer sr-only"
                                                            x-model="includeAdult"
                                                            @change="handleAdultFilterChange($event)">
                                                        <div class="w-5 h-5 bg-white border-2 border-gray-300 rounded peer-checked:bg-red-500 peer-checked:border-red-500 transition-colors"></div>
                                                        <svg class="absolute w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition-colors">Bao gồm nội dung 18+</span>
                                                </label>
                                                
                                                <div class="mt-3 pl-8">
                                                    <button type="button" @click="$dispatch('open-age-explanation')" class="inline-flex items-center gap-1.5 text-xs text-sky-600 hover:text-sky-700 font-medium transition-colors focus:outline-none">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Giải thích ký hiệu độ tuổi
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reset Filters always visible --}}
                        <button type="button" @click="resetFilters()" class="w-full text-gray-500 hover:text-gray-800 text-center block text-sm py-2 font-medium mt-4">
                            Xóa tất cả bộ lọc
                        </button>

                    </form>
                </div>

                {{-- Right Content: Results Wrapper --}}
                <div id="explore-results-wrapper" class="flex-1 relative" @click="handlePaginationClick($event)">
                    @include('tv-shows.partials.explore-results')
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('exploreFilter', () => ({
                    loading: false,
                    searchQuery: '{{ request('q', '') }}',
                    includeAdult: {{ request('adult_content') ? 'true' : 'false' }},

                    init() {
                        window.addEventListener('age-confirmed', () => {
                            this.includeAdult = true;
                            this.fetchResults();
                        });
                    },

                    handleAdultFilterChange(e) {
                        if (this.includeAdult) {
                            if (localStorage.getItem('reco_age_confirmed') !== 'true') {
                                this.includeAdult = false;
                                window.dispatchEvent(new CustomEvent('open-age-modal', {
                                    detail: {
                                        onConfirm: () => {
                                            // The age-confirmed listener will handle fetchResults
                                        }
                                    }
                                }));
                            } else {
                                this.fetchResults();
                            }
                        } else {
                            this.fetchResults();
                        }
                    },

                    fetchResults(url = null, pushState = true, scrollToTop = false) {
                        if (this.loading) return;
                        this.loading = true;

                        const form = document.getElementById('explore-filter-form');
                        let targetUrl = url;

                        if (!targetUrl) {
                            const formData = new FormData(form);
                            const params = new URLSearchParams();
                            for (const pair of formData.entries()) {
                                if (pair[1].trim() === '') continue;
                                if (pair[0] === 'min_rating' && pair[1] === '0') continue;
                                if (pair[0] === 'sort' && pair[1] === 'popularity_desc') continue;
                                params.append(pair[0], pair[1]);
                            }
                            targetUrl = form.action + '?' + params.toString();
                        }

                        // Show loader in grid if it exists
                        const spinner = document.getElementById('explore-loading-spinner');
                        const grid = document.getElementById('explore-movie-grid');
                        if (spinner) spinner.classList.remove('hidden');
                        if (grid) grid.classList.add('opacity-50');

                        fetch(targetUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'text/html'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                document.getElementById('explore-results-wrapper').innerHTML = html;

                                // Update URL without reloading
                                if (pushState) {
                                    window.history.pushState({
                                        path: targetUrl
                                    }, '', targetUrl);
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching results:', error);
                            })
                            .finally(() => {
                                this.loading = false;
                                if (scrollToTop) {
                                    const gridTop = document.getElementById('explore-results-wrapper').getBoundingClientRect().top + window.scrollY - 100;
                                    window.scrollTo({
                                        top: gridTop,
                                        behavior: 'smooth'
                                    });
                                }
                            });
                    },

                    handlePopstate(event) {
                        // Triggers when user clicks browser back/forward buttons
                        this.fetchResults(window.location.href, false);
                    },

                    handlePaginationClick(event) {
                        // Delegate click event for pagination links within the wrapper
                        const link = event.target.closest('a');
                        if (link && link.href && link.closest('#explore-pagination') !== null) {
                            event.preventDefault();
                            this.fetchResults(link.href, true, true);
                        }
                    },

                    resetFilters() {
                        const form = document.getElementById('explore-filter-form');
                        // Reset all inputs except sort to default
                        form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                        form.querySelectorAll('input[type="number"], input[type="text"]').forEach(input =>
                            input.value = '');
                        form.querySelectorAll('select').forEach(select => select.value = select.name ===
                            'sort' ? 'popularity_desc' : '');
                        form.querySelectorAll('input[type="range"]').forEach(range => {
                            range.value = 0;
                            range.dispatchEvent(new Event('input')) // Trigger x-model update
                        });
                        
                        this.searchQuery = '';

                        this.$nextTick(() => {
                            this.fetchResults();
                        });
                    },

                    clearSearchQuery() {
                        this.searchQuery = '';
                        this.$nextTick(() => {
                            this.fetchResults();
                        });
                    }
                }));
            });
        </script>
    @endpush

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

    {{-- Age Rating Explanation Modal --}}
    <div x-data="{ show: false }" 
         @open-age-explanation.window="show = true" 
         @keydown.escape.window="show = false"
         @ai-opened.window="show = false"
         @community-opened.window="show = false"
         class="relative z-[100]" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true" 
         x-show="show" 
         style="display: none;">
        
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="show" 
                     @click.outside="show = false"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-gray-900 font-heading" id="modal-title">Giải thích ký hiệu độ tuổi</h3>
                                <div class="mt-5 space-y-4">
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-11 shrink-0 text-xs font-bold px-2 py-1 rounded bg-green-700 text-white shadow-sm">P</span>
                                        <p class="text-sm text-gray-600"><strong class="text-gray-900 block mb-0.5">Phù hợp mọi độ tuổi</strong> Nội dung thân thiện, an toàn cho tất cả người xem.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-11 shrink-0 text-xs font-bold px-2 py-1 rounded bg-amber-600 text-white shadow-sm">T13</span>
                                        <p class="text-sm text-gray-600"><strong class="text-gray-900 block mb-0.5">Từ 13 tuổi trở lên</strong> Phù hợp với người xem từ 13 tuổi.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-11 shrink-0 text-xs font-bold px-2 py-1 rounded bg-orange-600 text-white shadow-sm">C16</span>
                                        <p class="text-sm text-gray-600"><strong class="text-gray-900 block mb-0.5">Từ 16 tuổi trở lên</strong> Phù hợp với người xem từ 16 tuổi.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-11 shrink-0 text-xs font-bold px-2 py-1 rounded bg-red-700 text-white shadow-sm">18+</span>
                                        <p class="text-sm text-gray-600"><strong class="text-gray-900 block mb-0.5">Người trưởng thành</strong> Nội dung dành riêng cho người từ 18 tuổi trở lên. Có thể chứa yếu tố nhạy cảm, bạo lực hoặc ngôn ngữ mạnh.</p>
                                    </div>
                                    <div class="flex items-start gap-3 mt-4 pt-4 border-t border-gray-100">
                                        <span class="inline-flex items-center justify-center w-11 shrink-0 text-[11px] font-bold px-1 py-1 rounded bg-gray-800 text-white shadow-sm">R/NC17</span>
                                        <p class="text-sm text-gray-600"><strong class="text-gray-900 block mb-0.5">Phân loại quốc tế</strong> Các nhóm phân loại chuẩn quốc tế tương đương nhóm cần cân nhắc độ tuổi hoặc dành cho người trưởng thành.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-11 shrink-0 text-[10px] font-bold px-1 py-1 rounded bg-gray-100 text-gray-500 border border-gray-200">N/A</span>
                                        <p class="text-sm text-gray-600"><strong class="text-gray-900 block mb-0.5">Chưa phân loại</strong> Hệ thống chưa có đủ dữ liệu phân loại độ tuổi cho nội dung này.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
