<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\TvShow;
use Illuminate\Http\Request;

class TvShowController extends Controller
{
    /**
     * Danh sách TV Series (lọc, tìm kiếm, phân trang).
     */
    public function index(Request $request)
    {
        $query = TvShow::with('genres')->whereNotNull('poster');

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

        // Lọc theo thể loại (logic AND)
        if ($request->has('genres') && is_array($request->input('genres')) && count($request->input('genres')) > 0) {
            $genreIds = $request->input('genres');
            foreach ($genreIds as $id) {
                $query->whereHas('genres', fn($qb) => $qb->where('genres.id', $id));
            }
        }

        // Lọc theo Năm phát hành (dựa trên first_air_date)
        $yearFrom = $request->input('year_from');
        $yearTo = $request->input('year_to');
        if ($yearFrom && $yearTo && $yearFrom > $yearTo) {
            $tmp = $yearFrom; $yearFrom = $yearTo; $yearTo = $tmp;
        }
        if ($yearFrom) {
            $query->whereYear('first_air_date', '>=', $yearFrom);
        }
        if ($yearTo) {
            $query->whereYear('first_air_date', '<=', $yearTo);
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

        // Sắp xếp
        $sort = $request->input('sort', 'popularity_desc');
        
        if ($request->filled('q')) {
            $qStr = trim(str_replace(['%', '_'], '', $request->input('q')));
            if ($qStr !== '') {
                $bindings = [
                    $qStr,
                    "{$qStr} %",
                    "% {$qStr} %", "% {$qStr}",
                    "{$qStr}%",
                    $qStr,
                    "{$qStr} %",
                    "% {$qStr} %", "% {$qStr}",
                    "{$qStr}%"
                ];

                $query->orderByRaw(
                    "CASE 
                        WHEN title = ? THEN 1
                        WHEN title LIKE ? THEN 2
                        WHEN title LIKE ? OR title LIKE ? THEN 3
                        WHEN title LIKE ? THEN 4
                        WHEN original_title = ? THEN 5
                        WHEN original_title LIKE ? THEN 6
                        WHEN original_title LIKE ? OR original_title LIKE ? THEN 7
                        WHEN original_title LIKE ? THEN 8
                        ELSE 9 
                    END",
                    $bindings
                );
            }
        }
        $query = match ($sort) {
            'rating_desc' => $query->orderByDesc('avg_rating')->orderByDesc('rating_count'),
            'popularity_desc' => $query->orderByDesc('view_count'),
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            'release_date_asc' => $query->orderBy('first_air_date'),
            'latest', 'release_date_desc' => $query->orderByDesc('first_air_date'),
            default => $query->orderByDesc('view_count'),
        };

        $tvShows = $query->paginate(24)->withQueryString();

        // Get genres that have tv shows
        $genres = Genre::whereHas('tvShows')->withCount('tvShows')->orderBy('name')->get();

        // Lấy danh sách quốc gia cho bộ lọc
        $countryNames = config('countries');

        $countries = TvShow::whereNotNull('country')
            ->where('country', '!=', '')
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->mapWithKeys(fn($code) => [$code => $countryNames[$code] ?? $code]);

        if ($request->ajax()) {
            return view('tv-shows.partials.explore-results', compact('tvShows'))->render();
        }

        return view('tv-shows.explore', compact('tvShows', 'genres', 'countries', 'sort'));
    }

    /**
     * Chi tiết TV Series.
     */
    public function show(TvShow $tvShow)
    {
        $tvShow->load([
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
        $avgRating = $tvShow->reviews()->whereNotNull('rating')->avg('rating');
        $ratingCount = $tvShow->reviews()->whereNotNull('rating')->count();

        // Lấy media (Videos, Backdrops, Posters)
        $tmdbService = app(\App\Services\TmdbService::class);
        $media = $tmdbService->getMedia($tvShow->tmdb_id, 'tv');

        // Series liên quan (cùng thể loại)
        $relatedTvShows = TvShow::with('genres')
            ->whereNotNull('poster')
            ->where('id', '!=', $tvShow->id)
            ->whereHas('genres', fn($q) => $q->whereIn('genres.id', $tvShow->genres->pluck('id')))
            ->inRandomOrder()
            ->take(6)
            ->get();

        // Cast & Crew tách riêng
        $cast = $tvShow->people->where('pivot.role', 'actor');
        $creators = $tvShow->people->whereIn('pivot.role', ['director', 'writer', 'producer']);

        // Tên quốc gia tiếng Việt
        $countryName = config('countries')[$tvShow->country] ?? $tvShow->country;

        // Tên ngôn ngữ gốc tiếng Việt
        $languageName = config('languages')[$tvShow->language] ?? $tvShow->language;

        // Phân phối điểm
        $ratingDistribution = $tvShow->reviews()
            ->where('status', 'published')
            ->whereNotNull('rating')
            ->selectRaw('ROUND(rating) as score, COUNT(*) as count')
            ->groupBy('score')
            ->orderBy('score')
            ->pluck('count', 'score')
            ->toArray();
            
        $distribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $distribution[$i] = $ratingDistribution[$i] ?? 0;
        }

        // Lịch sử đánh giá
        $ratingHistory = $tvShow->reviews()
            ->where('status', 'published')
            ->whereNotNull('rating')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, AVG(rating) as avg_score, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        return view('tv-shows.show', compact(
            'tvShow',
            'avgRating',
            'ratingCount',
            'relatedTvShows',
            'cast',
            'creators',
            'countryName',
            'languageName',
            'distribution',
            'ratingHistory',
            'media',
        ));
    }
}
