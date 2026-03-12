<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    /**
     * Danh sách phim (lọc, tìm kiếm, phân trang).
     */
    public function index(Request $request)
    {
        $query = Movie::with('genres')->whereNotNull('poster');

        // Tìm kiếm
        if ($q = $request->input('q')) {
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                    ->orWhere('original_title', 'like', "%{$q}%")
                    ->orWhere('synopsis', 'like', "%{$q}%");
            });
        }

        // Lọc theo thể loại
        if ($genreId = $request->input('genre')) {
            $query->whereHas('genres', fn($qb) => $qb->where('genres.id', $genreId));
        }

        // Sắp xếp
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'top_rated' => $query->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating'),
            'popular' => $query->withCount('reviews')
                ->orderByDesc('reviews_count'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('release_date'),
        };

        $movies = $query->paginate(20)->withQueryString();

        $genres = Genre::withCount('movies')
            ->having('movies_count', '>', 0)
            ->orderByDesc('movies_count')
            ->get();

        return view('explore', compact('movies', 'genres', 'sort'));
    }

    /**
     * Chi tiết phim.
     */
    public function show(Movie $movie)
    {
        $movie->load([
            'genres',
            'people' => fn($q) => $q->orderBy('display_order'),
            'reviews' => fn($q) => $q->published()
                ->fullReview()
                ->with('user')
                ->latest('published_at')
                ->take(10),
        ]);

        // Tính rating trung bình
        $avgRating = $movie->reviews()->whereNotNull('rating')->avg('rating');
        $ratingCount = $movie->reviews()->whereNotNull('rating')->count();

        // Phim liên quan (cùng thể loại)
        $relatedMovies = Movie::with('genres')
            ->whereNotNull('poster')
            ->where('id', '!=', $movie->id)
            ->whereHas('genres', fn($q) => $q->whereIn('genres.id', $movie->genres->pluck('id')))
            ->inRandomOrder()
            ->take(6)
            ->get();

        // Cast & Crew tách riêng
        $cast = $movie->people->where('pivot.role', 'actor');
        $directors = $movie->people->where('pivot.role', 'director');
        $writers = $movie->people->where('pivot.role', 'writer');

        return view('movies.show', compact(
            'movie',
            'avgRating',
            'ratingCount',
            'relatedMovies',
            'cast',
            'directors',
            'writers',
        ));
    }
}
