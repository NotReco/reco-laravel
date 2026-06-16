<?php

namespace App\Services;

use App\Models\User;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\ViewHistory;

class RecommendationService
{
    /**
     * Lấy danh sách gợi ý cho User (kết hợp cả Movie và TvShow).
     */
    public function getRecommendationsForUser(User $user, int $limit = 12)
    {
        $cache = app(\App\Services\CacheService::class);
        $key = $cache->userRecommendationKey($user->id);

        return $cache->remember($key, 10, function () use ($user, $limit) {
            $viewedMovieIds = $this->getViewedItemIds($user, Movie::class);
            $viewedTvShowIds = $this->getViewedItemIds($user, TvShow::class);

            $genreIds = $this->getFavoriteGenreIds($user);

            if (empty($genreIds)) {
                return $this->getFallbackRecommendations($limit);
            }

            $recommendations = collect();
            
            // Phân bổ số lượng theo top genres (VD: Top 1 lấy 6, Top 2 lấy 4, Top 3 lấy 2)
            $allocations = [ceil($limit * 0.5), ceil($limit * 0.3), ceil($limit * 0.2)];

            foreach ($genreIds as $index => $genreId) {
                $takeLimit = $allocations[$index] ?? 2;
                
                // Phân nửa cho Movie, nửa cho TvShow
                $movieLimit = ceil($takeLimit / 2);
                $tvLimit = floor($takeLimit / 2);

                $movies = Movie::with('genres')
                    ->whereNotNull('poster')
                    ->whereNotIn('id', $viewedMovieIds)
                    ->whereHas('genres', function ($q) use ($genreId) {
                        $q->where('genres.id', $genreId);
                    })
                    ->orderByDesc('view_count')
                    ->take($movieLimit)
                    ->get();

                $tvShows = TvShow::with('genres')
                    ->whereNotNull('poster')
                    ->whereNotIn('id', $viewedTvShowIds)
                    ->whereHas('genres', function ($q) use ($genreId) {
                        $q->where('genres.id', $genreId);
                    })
                    ->orderByDesc('view_count')
                    ->take($tvLimit)
                    ->get();

                $recommendations = $recommendations->concat($movies)->concat($tvShows);

                // Cập nhật id đã lấy để không bị trùng lặp ở genre sau
                $viewedMovieIds = array_merge($viewedMovieIds, $movies->pluck('id')->toArray());
                $viewedTvShowIds = array_merge($viewedTvShowIds, $tvShows->pluck('id')->toArray());
            }

            // Shuffle nhẹ hoặc sắp xếp lại theo view_count
            $recommendations = $recommendations->sortByDesc('view_count')->take($limit)->values();

            // Nếu vẫn không đủ (ví dụ lấy thiếu), thì bổ sung fallback
            if ($recommendations->count() < $limit) {
                $fallback = $this->getFallbackRecommendations($limit);
                // Lọc những item chưa có trong recommendations
                foreach ($fallback as $item) {
                    if (!$recommendations->contains('id', $item->id)) {
                        $recommendations->push($item);
                    }
                    if ($recommendations->count() >= $limit) break;
                }
            }

            return $recommendations->isEmpty() ? $this->getFallbackRecommendations($limit) : $recommendations;
        });
    }

    /**
     * Fallback cho user chưa đăng nhập hoặc chưa có đủ dữ liệu.
     */
    public function getFallbackRecommendations(int $limit = 12)
    {
        $cache = app(\App\Services\CacheService::class);
        $key = 'recommendations.fallback';

        return $cache->remember($key, 30, function () use ($limit) {
            $movies = Movie::with('genres')
                ->whereNotNull('poster')
                ->orderByDesc('view_count')
                ->take(ceil($limit / 2))
                ->get();

            $tvShows = TvShow::with('genres')
                ->whereNotNull('poster')
                ->orderByDesc('view_count')
                ->take(floor($limit / 2))
                ->get();

            return $movies->concat($tvShows)
                ->sortByDesc('view_count')
                ->take($limit)
                ->values();
        });
    }

    public function getFavoriteGenreIds(User $user): array
    {
        $genreScores = [];

        $addScore = function ($genres, float $points) use (&$genreScores) {
            if (!$genres) return;
            foreach ($genres as $genre) {
                if (!isset($genreScores[$genre->id])) {
                    $genreScores[$genre->id] = 0.0;
                }
                $genreScores[$genre->id] += $points;
            }
        };

        // 1. Tương tác (Interactions) - 50% trọng số (5.0 điểm)
        // 1.1. Favorites
        $favMovies = $user->favorites()->with('genres')->get();
        foreach ($favMovies as $movie) {
            $addScore($movie->genres, 5.0);
        }
        $favTvShows = $user->tvShowFavorites()->with('genres')->get();
        foreach ($favTvShows as $tvShow) {
            $addScore($tvShow->genres, 5.0);
        }

        // 1.2. Watchlists
        $wlMovies = $user->watchlists()->with('genres')->get();
        foreach ($wlMovies as $movie) {
            $addScore($movie->genres, 5.0);
        }
        $wlTvShows = $user->tvShowWatchlists()->with('genres')->get();
        foreach ($wlTvShows as $tvShow) {
            $addScore($tvShow->genres, 5.0);
        }

        // 1.3. Reviews
        $reviews = $user->reviews()->with(['movie.genres', 'tvShow.genres'])->get();
        foreach ($reviews as $review) {
            // Không tính điểm cho review có rating thấp (<= 4) vì user có thể không thích
            if ($review->rating !== null && $review->rating <= 4) {
                continue;
            }
            if ($review->movie_id && $review->movie) {
                $addScore($review->movie->genres, 5.0);
            }
            if ($review->tv_show_id && $review->tvShow) {
                $addScore($review->tvShow->genres, 5.0);
            }
        }

        // 2. Lịch sử xem (View History) - 30% trọng số (3.0 điểm)
        $views = ViewHistory::with('viewable.genres')
            ->where('user_id', $user->id)
            ->orderByDesc('viewed_at')
            ->limit(50) // Lấy 50 lượt xem gần nhất
            ->get();
            
        foreach ($views as $view) {
            $item = $view->viewable;
            if ($item && $item->relationLoaded('genres')) {
                $addScore($item->genres, 3.0);
            }
        }

        // 3. Lịch sử tìm kiếm (Search History) - 20% trọng số (2.0 điểm)
        $searches = \App\Models\SearchHistory::where('user_id', $user->id)
            ->orderByDesc('searched_at')
            ->limit(20)
            ->pluck('keyword')
            ->unique()
            ->values();

        foreach ($searches as $keyword) {
            $keyword = trim(mb_strtolower($keyword, 'UTF-8'));
            // Bỏ qua keyword quá ngắn
            if (mb_strlen($keyword, 'UTF-8') < 3) {
                continue;
            }

            // Tìm phim/tv show khớp keyword (ưu tiên match title/original_title, lấy tối đa 2 kết quả đầu)
            $movies = Movie::with('genres')
                ->active()
                ->where(function($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('original_title', 'like', "%{$keyword}%");
                })
                ->orderByDesc('view_count')
                ->limit(2)
                ->get();
                
            foreach ($movies as $movie) {
                $addScore($movie->genres, 2.0);
            }

            $tvs = TvShow::with('genres')
                ->active()
                ->where(function($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('original_title', 'like', "%{$keyword}%");
                })
                ->orderByDesc('view_count')
                ->limit(2)
                ->get();
                
            foreach ($tvs as $tv) {
                $addScore($tv->genres, 2.0);
            }
        }

        if (empty($genreScores)) {
            return [];
        }

        // Sắp xếp giảm dần theo điểm số
        arsort($genreScores);

        // Trả về top 3 thể loại
        return array_slice(array_keys($genreScores), 0, 3);
    }

    /**
     * Lấy danh sách ID đã xem của một loại model.
     */
    public function getViewedItemIds(User $user, string $modelClass): array
    {
        return ViewHistory::where('user_id', $user->id)
            ->where('viewable_type', $modelClass)
            ->pluck('viewable_id')
            ->toArray();
    }
}
