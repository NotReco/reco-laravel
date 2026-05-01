{{-- Right Content: Results --}}
<div class="flex-1" id="explore-results-container">
    {{-- Header Result Count --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative">
        @if($tvShows->count() > 0)
            <div class="mb-6 border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-bold text-gray-900">
                    @if(request('q'))
                        Kết quả tìm kiếm: "{{ request('q') }}"
                    @else
                        Danh sách TV Series
                    @endif
                </h2>
            </div>
        @endif
        
        {{-- Loading Spinner (Hidden by default, shown by Alpine) --}}
        <div id="explore-loading-spinner" class="hidden absolute inset-0 z-50 flex items-center justify-center bg-white/70 backdrop-blur-sm rounded-2xl">
            <svg class="animate-spin h-6 w-6 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    {{-- TV Grid --}}
    @if($tvShows->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8 transition-opacity duration-300" id="explore-movie-grid">
            @foreach($tvShows as $tvShow)
                <x-tv-show-card :tvShow="$tvShow" :showGenre="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-10" id="explore-pagination">
            {{ $tvShows->onEachSide(1)->links('vendor.pagination.reco') }}
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-200 shadow-sm" id="explore-movie-grid">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Không tìm thấy series nào</h3>
            <p class="text-gray-500">Thử thay đổi bộ lọc hoặc tìm với từ khóa khác xem sao nhé.</p>
            <button type="button" @click="resetFilters()" class="inline-flex items-center gap-2 mt-6 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors text-sm font-medium">
                <span>Xóa tất cả bộ lọc</span>
            </button>
        </div>
    @endif
</div>
