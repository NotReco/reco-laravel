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
        // Hero carousel — 5 phim có backdrop + trailer, view cao nhất
        $heroMovies = Movie::with('genres')
            ->whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->orderByDesc('view_count')
            ->take(5)
            ->get();

        // Fallback: nếu không đủ phim có trailer, bổ sung phim có backdrop
        if ($heroMovies->count() < 3) {
            $heroMovies = Movie::with('genres')
                ->whereNotNull('backdrop')
                ->whereNotNull('poster')
                ->orderByDesc('view_count')
                ->take(5)
                ->get();
        }

        // 🔥 Trending — 10 phim xem nhiều nhất
        $trendingMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->orderByDesc('view_count')
            ->take(10)
            ->get();

        // 🎬 Đang chiếu — 8 phim mới nhất theo release_date
        $nowPlayingMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->orderByDesc('release_date')
            ->take(8)
            ->get();

        // ⭐ Đánh giá cao nhất — 10 phim có avg_rating cao nhất (tối thiểu 2 đánh giá)
        $topRatedMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->where('rating_count', '>=', 2)
            ->orderByDesc('avg_rating')
            ->take(10)
            ->get();

        // 🎭 Sắp ra mắt — phim có release_date trong tương lai
        $upcomingMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->where('release_date', '>', now())
            ->orderBy('release_date')
            ->take(8)
            ->get();

        // 💬 Review mới nhất từ cộng đồng
        $latestReviews = Review::with(['user', 'movie'])
            ->published()
            ->fullReview()
            ->latest('published_at')
            ->take(6)
            ->get();

        // 🎭 Thể loại (cho genre chips)
        $genres = Genre::withCount('movies')
            ->having('movies_count', '>', 0)
            ->orderByDesc('movies_count')
            ->take(12)
            ->get();

        return view('home', compact(
            'heroMovies',
            'trendingMovies',
            'nowPlayingMovies',
            'topRatedMovies',
            'upcomingMovies',
            'latestReviews',
            'genres',
        ));
    }
}
