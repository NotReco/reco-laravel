<x-app-layout>
    <x-slot:title>Khám phá TV Series</x-slot:title>

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
                                            class="w-full text-sm rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
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
                                                series (A-Z)</option>
                                            <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>
                                                Tên series (Z-A)</option>
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
                                                    class="w-full text-sm rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                                                    min="1800" max="{{ date('Y') + 5 }}" step="1" onkeydown="if(['-','+','e','E','.'].includes(event.key)) event.preventDefault();" @change="fetchResults()">
                                                <span class="text-gray-500">-</span>
                                                <input type="number" name="year_to" value="{{ request('year_to') }}"
                                                    placeholder="Đến năm"
                                                    class="w-full text-sm rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                                                    min="1800" max="{{ date('Y') + 5 }}" step="1" onkeydown="if(['-','+','e','E','.'].includes(event.key)) event.preventDefault();" @change="fetchResults()">
                                            </div>
                                        </div>

                                        <hr class="border-gray-100">

                                        {{-- Country --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-3">Quốc gia</label>
                                            <select name="country"
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
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

                    init() {
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
                                    window.scrollTo({
                                        top: 0,
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
</x-app-layout>
