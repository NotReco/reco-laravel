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
        // Phim mới nhất (carousel)
        $latestMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->whereNotNull('backdrop')
            ->orderByDesc('release_date')
            ->take(8)
            ->get();

        // Phim đánh giá cao nhất
        $topRatedMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->withAvg('reviews', 'rating')
            ->having('reviews_avg_rating', '>', 0)
            ->orderByDesc('reviews_avg_rating')
            ->take(12)
            ->get();

        // Phim phổ biến (nhiều review nhất)
        $popularMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->withCount('reviews')
            ->orderByDesc('reviews_count')
            ->take(12)
            ->get();

        // Review mới nhất
        $latestReviews = Review::with(['user', 'movie'])
            ->published()
            ->fullReview()
            ->latest('published_at')
            ->take(6)
            ->get();

        // Thể loại
        $genres = Genre::withCount('movies')
            ->having('movies_count', '>', 0)
            ->orderByDesc('movies_count')
            ->take(10)
            ->get();

        return view('home', compact(
            'latestMovies',
            'topRatedMovies',
            'popularMovies',
            'latestReviews',
            'genres',
        ));
    }
}
