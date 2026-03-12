<x-app-layout>
    <x-slot:title>Khám phá phim</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
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

            {{-- Sort --}}
            <div class="flex items-center gap-2">
                @foreach([
                    'latest' => 'Mới nhất',
                    'top_rated' => 'Đánh giá cao',
                    'popular' => 'Phổ biến',
                    'title' => 'A-Z',
                ] as $key => $label)
                    <a href="{{ route('explore', array_merge(request()->query(), ['sort' => $key])) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $sort === $key ? 'bg-accent-500 text-white' : 'bg-dark-800 text-dark-300 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Genre Pills --}}
        <div class="flex items-center gap-2 flex-wrap mb-8">
            <a href="{{ route('explore', ['sort' => $sort]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ !request('genre') ? 'bg-accent-500 text-white' : 'bg-dark-800 text-dark-300 border border-dark-700 hover:text-white' }}">
                Tất cả
            </a>
            @foreach($genres as $genre)
                <a href="{{ route('explore', ['genre' => $genre->id, 'sort' => $sort]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ request('genre') == $genre->id ? 'bg-accent-500 text-white' : 'bg-dark-800 text-dark-300 border border-dark-700 hover:text-white' }}">
                    {{ $genre->name }}
                </a>
            @endforeach
        </div>

        {{-- Grid --}}
        @if($movies->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
                @foreach($movies as $movie)
                    <x-movie-card :movie="$movie" />
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
                <a href="{{ route('explore') }}" class="text-accent-400 hover:text-accent-300 text-sm mt-2 inline-block">← Xem tất cả phim</a>
            </div>
        @endif
    </div>
</x-app-layout>
