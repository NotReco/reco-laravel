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

        // Lọc theo nhiều thể loại
        if ($request->has('genres') && is_array($request->input('genres'))) {
            foreach ($request->input('genres') as $genreId) {
                $query->whereHas('genres', fn($qb) => $qb->where('genres.id', $genreId));
            }
        } elseif ($genreId = $request->input('genre')) { // Keep for backward compatibility
            $query->whereHas('genres', fn($qb) => $qb->where('genres.id', $genreId));
        }

        // Lọc theo Năm phát hành
        if ($yearFrom = $request->input('year_from')) {
            $query->whereYear('release_date', '>=', $yearFrom);
        }
        if ($yearTo = $request->input('year_to')) {
            $query->whereYear('release_date', '<=', $yearTo);
        }

        // Lọc theo Quốc gia
        if ($country = $request->input('country')) {
            $query->where('country', $country);
        }

        // Lọc theo Điểm đánh giá tối thiểu
        if ($minRating = $request->input('min_rating')) {
            $query->where('avg_rating', '>=', $minRating);
        }

        // Lọc theo Thời lượng (phút)
        if ($minRuntime = $request->input('min_runtime')) {
            $query->where('runtime', '>=', $minRuntime);
        }
        if ($maxRuntime = $request->input('max_runtime')) {
            $query->where('runtime', '<=', $maxRuntime);
        }

        // Sắp xếp
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'rating_desc' => $query->orderByDesc('avg_rating')->orderByDesc('rating_count'),
            'popularity_desc' => $query->orderByDesc('view_count'),
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            'release_date_asc' => $query->orderBy('release_date'),
            'latest', 'release_date_desc' => $query->orderByDesc('release_date'),
            default => $query->orderByDesc('release_date'),
        };

        $movies = $query->paginate(24)->withQueryString();

        $genres = Genre::withCount('movies')
            ->having('movies_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Lấy danh sách quốc gia cho bộ lọc
        $countries = Movie::whereNotNull('country')
            ->where('country', '!=', '')
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        if ($request->ajax()) {
            return view('partials.explore-results', compact('movies'))->render();
        }

        return view('explore', compact('movies', 'genres', 'countries', 'sort'));
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
                ->with(['user', 'likes', 'comments.user'])
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
