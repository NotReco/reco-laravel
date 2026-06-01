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

    /**
     * Lấy các id thể loại user quan tâm nhất dựa trên lịch sử xem.
     */
    public function getFavoriteGenreIds(User $user): array
    {
        // Lấy các record view history
        $views = ViewHistory::with('viewable.genres')
            ->where('user_id', $user->id)
            ->get();

        $genreCounts = [];

        foreach ($views as $view) {
            $item = $view->viewable;
            if ($item && $item->relationLoaded('genres')) {
                foreach ($item->genres as $genre) {
                    if (!isset($genreCounts[$genre->id])) {
                        $genreCounts[$genre->id] = 0;
                    }
                    $genreCounts[$genre->id]++;
                }
            }
        }

        if (empty($genreCounts)) {
            return [];
        }

        arsort($genreCounts);

        // Trả về top 3 thể loại
        return array_slice(array_keys($genreCounts), 0, 3);
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
