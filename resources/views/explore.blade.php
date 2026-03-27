<x-app-layout>
    <x-slot:title>Khám phá</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="exploreFilter()" @popstate.window="handlePopstate">
        
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- Left Sidebar: Filters & Sort --}}
            <div class="w-full lg:w-72 flex-shrink-0">
                <div class="mb-4 flex items-center justify-between">
                    <h1 class="text-2xl font-display font-bold text-white">Khám phá</h1>
                </div>

                <form action="{{ route('explore') }}" method="GET" id="explore-filter-form" class="space-y-4" @submit.prevent="fetchResults()">
                    {{-- Retain text search parameter if exists --}}
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    {{-- Sort Panel --}}
                    <div class="bg-dark-800 rounded-xl border border-dark-700 overflow-hidden" x-data="{ open: true }">
                        <button type="button" @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between bg-dark-800 hover:bg-dark-750 transition-colors">
                            <span class="font-semibold text-white">Sắp xếp</span>
                            <svg class="w-5 h-5 text-dark-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="px-5 pb-5 pt-1 border-t border-dark-700/50">
                                <label class="block text-sm text-dark-300 font-medium mb-2">Sắp xếp kết quả theo</label>
                                <select name="sort" class="input-dark w-full text-sm" @change="fetchResults()">
                                    <option value="popularity_desc" {{ $sort === 'popularity_desc' ? 'selected' : '' }}>Phổ biến nhất</option>
                                    <option value="rating_desc" {{ $sort === 'rating_desc' ? 'selected' : '' }}>Đánh giá cao nhất</option>
                                    <option value="release_date_desc" {{ in_array($sort, ['latest', 'release_date_desc']) ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="release_date_asc" {{ $sort === 'release_date_asc' ? 'selected' : '' }}>Cũ nhất</option>
                                    <option value="title_asc" {{ $sort === 'title_asc' ? 'selected' : '' }}>Tên phim (A-Z)</option>
                                    <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Tên phim (Z-A)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Filters Panel --}}
                    <div class="bg-dark-800 rounded-xl border border-dark-700 overflow-hidden" x-data="{ open: true }">
                        <button type="button" @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between bg-dark-800 hover:bg-dark-750 transition-colors">
                            <span class="font-semibold text-white">Bộ lọc</span>
                            <svg class="w-5 h-5 text-dark-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="px-5 pb-5 pt-1 space-y-6 border-t border-dark-700/50 pt-4">
                                
                                {{-- Genres --}}
                                <div>
                                    <label class="block text-sm font-medium text-white mb-3">Thể loại</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($genres as $genre)
                                            <label class="cursor-pointer">
                                                <input type="checkbox" name="genres[]" value="{{ $genre->id }}" class="peer sr-only" {{ in_array($genre->id, request('genres', [])) || request('genre') == $genre->id ? 'checked' : '' }} @change="fetchResults()">
                                                <span class="inline-block px-3 py-1 text-sm rounded-full border border-dark-600 text-dark-300 peer-checked:bg-rose-600 peer-checked:border-rose-600 peer-checked:text-white transition-all hover:border-dark-400">
                                                    {{ $genre->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <hr class="border-dark-700">

                                {{-- Release Year --}}
                                <div>
                                    <label class="block text-sm font-medium text-white mb-3">Năm phát hành</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="year_from" value="{{ request('year_from') }}" placeholder="Từ năm" class="input-dark w-full text-sm" min="1800" max="{{ date('Y')+5 }}">
                                        <span class="text-dark-500">-</span>
                                        <input type="number" name="year_to" value="{{ request('year_to') }}" placeholder="Đến năm" class="input-dark w-full text-sm" min="1800" max="{{ date('Y')+5 }}">
                                    </div>
                                </div>

                                <hr class="border-dark-700">

                                {{-- Country --}}
                                <div>
                                    <label class="block text-sm font-medium text-white mb-3">Quốc gia</label>
                                    <select name="country" class="input-dark w-full text-sm" @change="fetchResults()">
                                        <option value="">Tất cả quốc gia</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr class="border-dark-700">

                                {{-- Min Rating --}}
                                <div x-data="{ minRating: {{ request('min_rating', 0) }} }">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-sm font-medium text-white">Điểm người dùng</label>
                                        <span class="text-xs font-bold bg-dark-700 text-rose-400 px-2 py-0.5 rounded" x-text="minRating > 0 ? '≥ ' + minRating : 'Tất cả'"></span>
                                    </div>
                                    <input type="range" name="min_rating" min="0" max="10" step="1" x-model="minRating" class="w-full accent-rose-600" @change="fetchResults()">
                                    <div class="flex justify-between text-xs text-dark-500 mt-1">
                                        <span>0</span>
                                        <span>10</span>
                                    </div>
                                </div>

                                <hr class="border-dark-700">

                                {{-- Runtime --}}
                                <div>
                                    <label class="block text-sm font-medium text-white mb-3">Thời lượng (phút)</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="min_runtime" value="{{ request('min_runtime') }}" placeholder="Tối thiểu" class="input-dark w-full text-sm" min="0">
                                        <span class="text-dark-500">-</span>
                                        <input type="number" name="max_runtime" value="{{ request('max_runtime') }}" placeholder="Tối đa" class="input-dark w-full text-sm" min="0">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-rose-600/20 transition-all flex justify-center items-center gap-2" :class="{'opacity-75 cursor-not-allowed': loading}" :disabled="loading">
                        <span x-text="loading ? 'Đang tải...' : 'Lọc kết quả'"></span>
                        <svg x-show="!loading" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <svg x-show="loading" class="animate-spin w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                    
                    {{-- Reset Filters if any active --}}
                    <button type="button" @click="resetFilters()" x-show="Object.fromEntries(new FormData(document.getElementById('explore-filter-form'))).sort !== 'latest' || Object.keys(Object.fromEntries(new FormData(document.getElementById('explore-filter-form')))).length > 1" class="w-full text-dark-300 hover:text-white text-center block text-sm py-2" style="display: none;">
                        Xóa tất cả bộ lọc
                    </button>

                </form>
            </div>

            {{-- Right Content: Results Wrapper --}}
            <div id="explore-results-wrapper" class="flex-1 relative" @click="handlePaginationClick($event)">
                @include('partials.explore-results')
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('exploreFilter', () => ({
                loading: false,

                init() {
                    // Check if filters are applied on load to show reset button properly
                    this.$el.querySelector('button[type="button"][x-show]').style.display = '';
                },

                fetchResults(url = null, pushState = true) {
                    if (this.loading) return;
                    this.loading = true;

                    const form = document.getElementById('explore-filter-form');
                    let targetUrl = url;

                    if (!targetUrl) {
                        const formData = new FormData(form);
                        const params = new URLSearchParams();
                        for (const pair of formData.entries()) {
                            if (pair[1].trim() !== '') {
                                params.append(pair[0], pair[1]);
                            }
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
                            window.history.pushState({ path: targetUrl }, '', targetUrl);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching results:', error);
                    })
                    .finally(() => {
                        this.loading = false;
                        window.scrollTo({ top: 0, behavior: 'smooth' }); // Optional smooth scroll
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
                        this.fetchResults(link.href, true);
                    }
                },

                resetFilters() {
                    const form = document.getElementById('explore-filter-form');
                    // Reset all inputs except sort to default
                    form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                    form.querySelectorAll('input[type="number"], input[type="text"]').forEach(input => input.value = '');
                    form.querySelectorAll('select').forEach(select => select.value = select.name === 'sort' ? 'latest' : '');
                    form.querySelectorAll('input[type="range"]').forEach(range => {
                        range.value = 0;
                        range.dispatchEvent(new Event('input')) // Trigger x-model update
                    });
                    
                    this.fetchResults();
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
