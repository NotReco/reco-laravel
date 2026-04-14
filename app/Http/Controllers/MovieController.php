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
        $countryNames = [
            'AR' => 'Argentina',
            'AU' => 'Úc',
            'BR' => 'Brazil',
            'CA' => 'Canada',
            'CN' => 'Trung Quốc',
            'ES' => 'Tây Ban Nha',
            'FR' => 'Pháp',
            'GB' => 'Anh',
            'IE' => 'Ireland',
            'IN' => 'Ấn Độ',
            'JP' => 'Nhật Bản',
            'KR' => 'Hàn Quốc',
            'NO' => 'Na Uy',
            'PH' => 'Philippines',
            'RU' => 'Nga',
            'US' => 'Mỹ',
        ];

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
