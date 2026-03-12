<x-app-layout>
    <x-slot:title>Khám phá phim</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header + Search --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-display font-bold text-white">
                    @if(request('q'))
                        Kết quả: "{{ request('q') }}"
                    @elseif(request('genre'))
                        {{ $genres->firstWhere('id', request('genre'))?->name ?? 'Phim' }}
                    @else
                        Khám phá phim
                    @endif
                </h1>
                <p class="text-dark-400 mt-1">{{ $movies->total() }} phim</p>
            </div>

            {{-- Search form --}}
            <form action="{{ route('explore') }}" method="GET" class="flex items-center gap-2">
                @if(request('genre'))
                    <input type="hidden" name="genre" value="{{ request('genre') }}">
                @endif
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm phim..."
                        class="input-dark pl-10 pr-4 py-2.5 w-64 text-sm">
                </div>
            </form>
        </div>

        {{-- Sort Tabs --}}
        <div class="flex items-center gap-2 mb-6">
            @foreach([
                'latest' => 'Mới nhất',
                'top_rated' => 'Đánh giá cao',
                'popular' => 'Phổ biến',
                'title' => 'A-Z',
            ] as $key => $label)
                <a href="{{ route('explore', array_merge(request()->query(), ['sort' => $key])) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ $sort === $key ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/25' : 'bg-dark-800 text-dark-300 hover:text-white hover:bg-dark-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Genre Pills --}}
        <div class="mb-8">
            <x-genre-pills :genres="$genres" :selectedId="request('genre')" />
        </div>

        {{-- Movie Grid --}}
        @if($movies->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
                @foreach($movies as $movie)
                    <x-movie-card :movie="$movie" :showGenre="true" />
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center">
                {{ $movies->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <svg class="w-16 h-16 text-dark-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
                <p class="text-dark-400 text-lg">Không tìm thấy phim nào</p>
                @if(request('q') || request('genre'))
                    <a href="{{ route('explore') }}" class="text-rose-400 hover:text-rose-300 text-sm mt-2 inline-block">← Xem tất cả phim</a>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
