<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $cache = app(\App\Services\CacheService::class);

        // Hero carousel — Lấy danh sách phim/TV show đã được ghim trong Admin Carousel (tối đa 20)
        $heroItems = $cache->remember($cache->homeKey('hero'), 30, function () {
            $featuredMovies = Movie::with('genres')->where('is_featured', true)->get();
            $featuredTvShows = \App\Models\TvShow::with('genres')->where('is_featured', true)->get();

            return $featuredMovies->concat($featuredTvShows)
                ->sortBy('featured_order')
                ->take(20)
                ->values();
        });

        // Gợi ý dành cho bạn
        $recommendationService = app(\App\Services\RecommendationService::class);
        if (auth()->check()) {
            $recommendedItems = $recommendationService->getRecommendationsForUser(auth()->user(), 12);
        } else {
            $recommendedItems = $recommendationService->getFallbackRecommendations(12);
        }

        // 🔥 Trending — 10 phim xem nhiều nhất
        $trendingMovies = $cache->remember($cache->homeKey('movies.trending'), 30, function () {
            return Movie::with('genres')
                ->whereNotNull('poster')
                ->orderByDesc('view_count')
                ->take(10)
                ->get();
        });

        // 📺 Phim bộ nổi bật — 10 phim bộ xem nhiều nhất
        $trendingTvShows = $cache->remember($cache->homeKey('tv.trending'), 30, function () {
            return \App\Models\TvShow::with('genres')
                ->whereNotNull('poster')
                ->orderByDesc('view_count')
                ->take(10)
                ->get();
        });

        // 🎬 Đang chiếu — 8 phim mới nhất theo release_date
        $nowPlayingMovies = $cache->remember($cache->homeKey('movies.now_playing'), 30, function () {
            return Movie::with('genres')
                ->whereNotNull('poster')
                ->orderByDesc('release_date')
                ->take(8)
                ->get();
        });

        // ⭐ Đánh giá cao nhất — 10 phim có avg_rating cao nhất (tối thiểu 2 đánh giá)
        $topRatedMovies = $cache->remember($cache->homeKey('top_rated'), 30, function () {
            return Movie::with('genres')
                ->whereNotNull('poster')
                ->where('rating_count', '>=', 2)
                ->orderByDesc('avg_rating')
                ->take(10)
                ->get();
        });

        // 🎭 Sắp ra mắt — phim có release_date trong tương lai
        $upcomingMovies = $cache->remember($cache->homeKey('movies.upcoming'), 30, function () {
            return Movie::with('genres')
                ->whereNotNull('poster')
                ->where('release_date', '>', now())
                ->orderBy('release_date')
                ->take(8)
                ->get();
        });

        // 💬 Review mới nhất từ cộng đồng
        $latestReviews = $cache->remember($cache->homeKey('reviews.latest'), 30, function () {
            return Review::with([
                    'user.activeFrame',
                    'movie',
                    'tvShow',
                    'likes',
                    'comments',
                    'reports' => fn($r) => $r->where('is_public', true)->where('status', 'resolved')->with('user')->latest(),
                ])
                ->published()
                ->fullReview()
                ->latest('published_at')
                ->take(6)
                ->get();
        });

        // 🎭 Thể loại (cho genre chips)
        $genres = $cache->remember($cache->homeKey('genres'), 1440, function () {
            return Genre::withCount('movies')
                ->having('movies_count', '>', 0)
                ->orderByDesc('movies_count')
                ->take(12)
                ->get();
        });

        return view('home', compact(
            'heroItems',
            'trendingMovies',
            'trendingTvShows',
            'nowPlayingMovies',
            'topRatedMovies',
            'upcomingMovies',
            'latestReviews',
            'genres',
            'recommendedItems',
        ));
    }
}
