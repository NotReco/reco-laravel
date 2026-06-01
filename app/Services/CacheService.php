<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Closure;

class CacheService
{
    /**
     * Cache remember wrapper.
     */
    public function remember(string $key, int $minutes, Closure $callback)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    /**
     * Forget cache keys.
     */
    public function forget($keys)
    {
        if (is_array($keys)) {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        } else {
            Cache::forget($keys);
        }
    }

    // Key helpers

    public function homeKey(string $section): string
    {
        return "home.{$section}";
    }

    public function movieDetailKey(int $movieId): string
    {
        return "movie.detail.{$movieId}";
    }

    public function tvShowDetailKey(int $tvShowId): string
    {
        return "tv.detail.{$tvShowId}";
    }

    public function userRecommendationKey(int $userId): string
    {
        return "recommendations.user.{$userId}";
    }

    /**
     * Cache key cho search results theo keyword đã normalize.
     * TTL ngắn ~5 phút để kết quả tươi nhưng không gây quá tải DB.
     */
    public function searchKey(string $normalizedKeyword): string
    {
        $safeKey = \Illuminate\Support\Str::slug($normalizedKeyword, '_');
        return "search.result.{$safeKey}";
    }

    /**
     * Clear home page cache.
     */
    public function clearHomeCache()
    {
        $this->forget([
            $this->homeKey('hero'),
            $this->homeKey('movies.trending'),
            $this->homeKey('tv.trending'),
            $this->homeKey('movies.now_playing'),
            $this->homeKey('top_rated'),
            $this->homeKey('movies.upcoming'),
            $this->homeKey('reviews.latest'),
        ]);
    }
}
