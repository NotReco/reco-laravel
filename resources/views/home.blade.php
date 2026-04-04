<x-app-layout>
    <x-slot:title>Trang chủ</x-slot:title>

    {{-- ═══ Hero Banner Carousel ═══ --}}
    <x-hero-carousel :movies="$heroMovies" />

    {{-- ═══ Section 1: Trending – full-width bg xen kẽ --}}
    <div class="bg-white py-6 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-movie-section title="Tâm Điểm Hiện Tại"
                subtitle="Khám phá những câu chuyện đang làm mưa làm gió phòng vé hôm nay." :items="$trendingMovies" />
        </div>
    </div>

    {{-- ═══ Section 2: Đang Chiếu – bg xám nhạt --}}
    <div class="bg-gray-50 py-6 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-movie-section title="Đang Chiếu Rạp"
                subtitle="Những bom tấn đang đổ bộ và làm mưa làm gió trên màn ảnh rộng." :items="$nowPlayingMovies" />
        </div>
    </div>

    {{-- ═══ Section 3: Top Rated – trắng --}}
    <div class="bg-white py-6 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-movie-section title="Đỉnh Cao Điện Ảnh"
                subtitle="Tuyển tập những kiệt tác màn ảnh đạt điểm số cao chót vót." :items="$topRatedMovies" />
        </div>
    </div>

    {{-- ═══ Section 4: Upcoming – xám nhạt --}}
    @if ($upcomingMovies->isNotEmpty())
        <div class="bg-gray-50 py-6 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-movie-section title="Đếm Ngược Ngày Chiếu"
                    subtitle="Lưu ngay lịch ra mắt của những siêu phẩm đang được ngóng chờ nhất." :items="$upcomingMovies" />
            </div>
        </div>
    @endif

    {{-- ═══ Section 5: Khám Phá Theo Thể Loại – TMDB style full-width --}}
    @if ($genres->isNotEmpty())
        <div class="bg-white py-6 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Header --}}
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-8 bg-sky-500 rounded-full"></div>
                    <h2 class="text-xl lg:text-2xl font-heading font-bold text-gray-900">Khám Phá Theo Thể Loại</h2>
                </div>

                {{-- Genre grid cards (TMDB-inspired) --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @php
                        $genreColors = [
                            '#0284c7',
                            '#7c3aed',
                            '#0891b2',
                            '#059669',
                            '#d97706',
                            '#dc2626',
                            '#2563eb',
                            '#db2777',
                            '#16a34a',
                            '#9333ea',
                            '#0284c7',
                            '#ca8a04',
                        ];
                    @endphp
                    @foreach ($genres as $idx => $genre)
                        <a href="{{ route('explore', ['genre' => $genre->id]) }}"
                            class="group relative rounded-xl overflow-hidden h-16 flex items-center justify-between px-4 transition-all duration-200 hover:scale-[1.03] hover:shadow-md"
                            style="background-color: {{ $genreColors[$idx % count($genreColors)] }}10; border: 1px solid {{ $genreColors[$idx % count($genreColors)] }}30;">
                            <span
                                class="text-sm font-bold text-gray-800 group-hover:text-gray-900">{{ $genre->name }}</span>
                            @if (isset($genre->movies_count))
                                <span class="text-xs font-medium text-gray-400">{{ $genre->movies_count }}</span>
                            @endif
                            {{-- colored right bar --}}
                            <div class="absolute right-0 top-0 bottom-0 w-1 rounded-r-xl transition-all group-hover:w-1.5"
                                style="background-color: {{ $genreColors[$idx % count($genreColors)] }};"></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ Section 6: Cộng Đồng Đánh Giá --}}
    @if ($latestReviews->isNotEmpty())
        <div class="bg-gray-50 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Header --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-1 h-8 bg-sky-500 rounded-full"></div>
                    <div>
                        <h2 class="text-xl lg:text-2xl font-heading font-bold text-gray-900">Cộng Đồng Đánh Giá</h2>
                        <p class="text-gray-500 text-sm mt-0.5">Những bài đánh giá mới nhất từ cộng đồng RecoDB</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($latestReviews as $review)
                        <x-review-card :review="$review" />
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
