<x-app-layout>
    <x-slot:title>Trang chủ</x-slot:title>

    {{-- ═══ Section 1: Hero Banner Carousel ═══ --}}
    <x-hero-carousel :movies="$heroMovies" />

    {{-- ═══ Main Content ═══ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

        {{-- Section 2: 🔥 Trending --}}
        <x-movie-section
            title="🔥 Trending Ngay Lúc Này"
            subtitle="Các tác phẩm điện ảnh đang được quan tâm nhất hôm nay."
            :items="$trendingMovies"
            :seeAllUrl="route('explore', ['sort' => 'popular'])"
        />

        {{-- Section 3: 🎬 Đang Chiếu --}}
        <x-movie-section
            title="🎬 Đang Chiếu Rạp"
            subtitle="Những bộ phim mới nhất vừa ra rạp."
            :items="$nowPlayingMovies"
            :seeAllUrl="route('explore', ['sort' => 'latest'])"
            layout="grid"
        />

        {{-- Section 4: ⭐ Đánh Giá Cao Nhất --}}
        <x-movie-section
            title="⭐ Đánh Giá Cao Nhất"
            subtitle="Những bộ phim được cộng đồng đánh giá xuất sắc."
            :items="$topRatedMovies"
            :seeAllUrl="route('explore', ['sort' => 'top_rated'])"
        />

        {{-- Section 5: 🎭 Sắp Ra Mắt --}}
        @if($upcomingMovies->isNotEmpty())
            <x-movie-section
                title="🎭 Sắp Ra Mắt"
                subtitle="Những bộ phim đáng mong đợi sắp tới."
                :items="$upcomingMovies"
            />
        @endif

        {{-- Section 6: 💬 Cộng Đồng Đánh Giá --}}
        @if($latestReviews->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-2">
                    <h2 class="section-title">💬 Cộng Đồng Đánh Giá</h2>
                </div>
                <p class="section-subtitle">Những bài đánh giá mới nhất từ cộng đồng RecoDB.</p>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($latestReviews as $review)
                        <x-review-card :review="$review" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 7: 🎭 Khám Phá Theo Thể Loại --}}
        @if($genres->isNotEmpty())
            <section>
                <h2 class="section-title mb-4">🎭 Khám Phá Theo Thể Loại</h2>
                <x-genre-pills :genres="$genres" />
            </section>
        @endif

    </div>
</x-app-layout>