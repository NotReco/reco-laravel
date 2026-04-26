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
            $qStr = trim(str_replace(['%', '_'], '', $q));
            if ($qStr !== '') {
                $query->where(function ($qb) use ($qStr) {
                    $qb->where('title', 'like', "%{$qStr}%")
                        ->orWhere('original_title', 'like', "%{$qStr}%");
                });
            }
        }

        // Lọc theo nhiều thể loại (logic OR: phim chứa ít nhất 1 thể loại được chọn)
        if ($request->has('genres') && is_array($request->input('genres')) && count($request->input('genres')) > 0) {
            $genreIds = $request->input('genres');
            $query->whereHas('genres', fn($qb) => $qb->whereIn('genres.id', $genreIds));
        } elseif ($genreId = $request->input('genre')) { // Keep for backward compatibility
            $query->whereHas('genres', fn($qb) => $qb->where('genres.id', $genreId));
        }

        // Lọc theo Năm phát hành
        $yearFrom = $request->input('year_from');
        $yearTo = $request->input('year_to');
        if ($yearFrom && $yearTo && $yearFrom > $yearTo) {
            $tmp = $yearFrom; $yearFrom = $yearTo; $yearTo = $tmp;
        }
        if ($yearFrom) {
            $query->whereYear('release_date', '>=', $yearFrom);
        }
        if ($yearTo) {
            $query->whereYear('release_date', '<=', $yearTo);
        }

        // Lọc theo Quốc gia
        if ($country = $request->input('country')) {
            $query->where('country', $country);
        }

        // Lọc theo Điểm đánh giá tối thiểu
        $minRating = $request->input('min_rating');
        if ($minRating !== null && $minRating !== '' && (int) $minRating > 0) {
            $query->where('avg_rating', '>=', (int) $minRating);
        }

        // Lọc theo Thời lượng (phút)
        $minRuntime = $request->input('min_runtime');
        $maxRuntime = $request->input('max_runtime');
        if ($minRuntime && $maxRuntime && $minRuntime > $maxRuntime) {
            $tmp = $minRuntime; $minRuntime = $maxRuntime; $maxRuntime = $tmp;
        }
        if ($minRuntime) {
            $query->where('runtime', '>=', $minRuntime);
        }
        if ($maxRuntime) {
            $query->where('runtime', '<=', $maxRuntime);
        }

        // Sắp xếp
        $sort = $request->input('sort', 'popularity_desc');
        
        if ($request->filled('q')) {
            $qStr = trim(str_replace(['%', '_'], '', $request->input('q')));
            if ($qStr !== '') {
                $query->orderByRaw(
                    "CASE WHEN title LIKE ? THEN 1 WHEN original_title LIKE ? THEN 2 ELSE 3 END",
                    ["{$qStr}%", "{$qStr}%"]
                );
            }
        }
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
        $countryNames = config('countries');

        $countries = Movie::whereNotNull('country')
            ->where('country', '!=', '')
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->mapWithKeys(fn($code) => [$code => $countryNames[$code] ?? $code]);

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
            'tags',
            'people' => fn($q) => $q->orderBy('display_order'),
            'reviews' => fn($q) => $q->published()
                ->fullReview()
                ->with(['user.activeFrame', 'likes', 'comments.user.activeFrame'])
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

        // Tên quốc gia tiếng Việt
        $countryName = config('countries')[$movie->country] ?? $movie->country;

        // Tên ngôn ngữ gốc tiếng Việt
        $languageName = config('languages')[$movie->language] ?? $movie->language;

        // Phân phối điểm (số lượng đánh giá theo từng điểm 1-10)
        $ratingDistribution = $movie->reviews()
            ->where('status', 'published')
            ->whereNotNull('rating')
            ->selectRaw('ROUND(rating) as score, COUNT(*) as count')
            ->groupBy('score')
            ->orderBy('score')
            ->pluck('count', 'score')
            ->toArray();
        // Đảm bảo đủ 10 mức
        $distribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $distribution[$i] = $ratingDistribution[$i] ?? 0;
        }

        // Lịch sử đánh giá theo tháng (12 tháng gần nhất)
        $ratingHistory = $movie->reviews()
            ->where('status', 'published')
            ->whereNotNull('rating')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, AVG(rating) as avg_score, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        return view('movies.show', compact(
            'movie',
            'avgRating',
            'ratingCount',
            'relatedMovies',
            'cast',
            'directors',
            'writers',
            'countryName',
            'languageName',
            'distribution',
            'ratingHistory',
        ));
    }
}
