{{-- Right Content: Results --}}
<div class="flex-1" id="explore-results-container">
    {{-- Header Result Count --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative">
        <div>
            <h2 class="text-xl font-semibold text-white">
                @if(request('q'))
                    Kết quả tìm kiếm: "{{ request('q') }}"
                @else
                    Danh sách phim
                @endif
            </h2>
            <p class="text-dark-400 text-sm mt-1">Đang hiển thị {{ $movies->firstItem() ?? 0 }}-{{ $movies->lastItem() ?? 0 }} trên tổng số {{ $movies->total() }} kết quả</p>
        </div>
        
        {{-- Loading Spinner (Hidden by default, shown by Alpine) --}}
        <div class="absolute right-0 top-1/2 -translate-y-1/2 hidden" id="explore-loading-spinner">
            <svg class="animate-spin h-6 w-6 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    {{-- Movie Grid --}}
    @if($movies->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8 transition-opacity duration-300" id="explore-movie-grid">
            @foreach($movies as $movie)
                <x-movie-card :movie="$movie" :showGenre="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-10" id="explore-pagination">
            {{ $movies->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-dark-800/50 rounded-2xl border border-dark-700/50" id="explore-movie-grid">
            <svg class="w-16 h-16 text-dark-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
            </svg>
            <p class="text-dark-300 mb-2 font-medium">Không tìm thấy phim nào phù hợp với bộ lọc.</p>
            <p class="text-dark-500 text-sm">Hãy thử thay đổi tiêu chí tìm kiếm hoặc xóa bớt bộ lọc.</p>
            <button type="button" @click="resetFilters()" class="inline-flex items-center gap-2 mt-6 px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white rounded-lg transition-colors text-sm font-medium">
                <span>Xóa tất cả bộ lọc</span>
            </button>
        </div>
    @endif
</div>
