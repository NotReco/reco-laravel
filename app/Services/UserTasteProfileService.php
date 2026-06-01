<?php

namespace App\Services;

use App\Models\User;
use App\Models\ViewHistory;
use App\Models\SearchHistory;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * UserTasteProfileService – Phase 3B
 *
 * Tổng hợp thông tin sở thích (Taste Profile) của người dùng dựa trên hành vi:
 * - Lịch sử xem (View History) -> Thể loại gần đây
 * - Tương tác (Favorite, Watchlist, Review) -> Phim đã thích/quan tâm
 * - Tìm kiếm (Search History) -> Keyword tìm kiếm
 *
 * Chỉ trích xuất thông tin tóm tắt an toàn (summary profile), không gửi
 * thông tin nhạy cảm của user cho Gemini.
 */
class UserTasteProfileService
{
    private int $tasteProfileDays;
    private int $maxGenres;
    private int $maxTitles;
    private int $maxKeywords;

    public function __construct()
    {
        $this->tasteProfileDays = config('ai_assistant.taste_profile_days', 60);
        $this->maxGenres        = config('ai_assistant.max_profile_genres', 5);
        $this->maxTitles        = config('ai_assistant.max_profile_titles', 5);
        $this->maxKeywords      = config('ai_assistant.max_profile_keywords', 5);
    }

    /**
     * Xây dựng Taste Profile cho một user.
     *
     * @param User|null $user
     * @return array
     */
    public function buildForUser(?User $user): array
    {
        $defaultProfile = [
            'available'              => false,
            'summary'                => null,
            'favorite_genres'        => [],
            'recent_genres'          => [],
            'liked_titles'           => [],
            'watchlisted_titles'     => [],
            'recent_search_keywords' => [],
            'rating_tendency'        => null,
            'content_tendency'       => null,
        ];

        if (!$user) {
            return $defaultProfile;
        }

        try {
            $recentGenres   = $this->getRecentGenres($user);
            $favoriteGenres = $this->getFavoriteGenres($user);
            $likedTitles    = $this->getLikedTitles($user);
            $watchlisted    = $this->getWatchlistedTitles($user);
            $searches       = $this->getRecentSearches($user);
            $tendencies     = $this->analyzeTendency($user, $recentGenres);

            $hasData = !empty($recentGenres) || !empty($favoriteGenres) || 
                       !empty($likedTitles) || !empty($watchlisted) || !empty($searches);

            if (!$hasData) {
                return $defaultProfile; // User chưa có tương tác
            }

            // Tạo chuỗi summary nhẹ nhàng
            $summaryParts = [];
            if (!empty($favoriteGenres)) {
                $summaryParts[] = 'thường quan tâm phim ' . implode(', ', array_slice($favoriteGenres, 0, 2));
            }
            if (!empty($tendencies['content_tendency'])) {
                $summaryParts[] = 'gần đây ' . $tendencies['content_tendency'];
            }
            
            $summary = empty($summaryParts) 
                ? 'Người dùng đã có một vài tương tác với RecoDB.'
                : 'Người dùng ' . implode(' và ', $summaryParts) . '.';

            return [
                'available'              => true,
                'summary'                => $summary,
                'favorite_genres'        => $favoriteGenres,
                'recent_genres'          => $recentGenres,
                'liked_titles'           => $likedTitles,
                'watchlisted_titles'     => $watchlisted,
                'recent_search_keywords' => $searches,
                'rating_tendency'        => $tendencies['rating_tendency'],
                'content_tendency'       => $tendencies['content_tendency'],
            ];

        } catch (\Exception $e) {
            Log::warning('UserTasteProfileService: error building profile', [
                'user_id' => $user->id,
                'error'   => $e->getMessage()
            ]);
            return $defaultProfile;
        }
    }

    /**
     * Lấy các thể loại yêu thích (từ view histories chung)
     */
    private function getFavoriteGenres(User $user): array
    {
        try {
            $recService = app(RecommendationService::class);
            $genreIds = $recService->getFavoriteGenreIds($user); // Returns top 3 genre IDs
            
            if (empty($genreIds)) return [];

            return \App\Models\Genre::whereIn('id', $genreIds)
                ->pluck('name')
                ->take($this->maxGenres)
                ->toArray();
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Lấy các thể loại xem gần đây (trong X ngày)
     */
    private function getRecentGenres(User $user): array
    {
        $since = Carbon::now()->subDays($this->tasteProfileDays);

        $views = ViewHistory::with('viewable.genres')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $genreCounts = [];
        foreach ($views as $view) {
            $item = $view->viewable;
            if ($item && $item->relationLoaded('genres')) {
                foreach ($item->genres as $genre) {
                    $genreCounts[$genre->name] = ($genreCounts[$genre->name] ?? 0) + 1;
                }
            }
        }

        arsort($genreCounts);
        return array_slice(array_keys($genreCounts), 0, $this->maxGenres);
    }

    /**
     * Lấy danh sách phim đã favorite (cả movie + tv show)
     */
    private function getLikedTitles(User $user): array
    {
        $movies = $user->favoriteMovies()->select('title')->orderByDesc('favorites.created_at')->limit($this->maxTitles)->pluck('title')->toArray();
        $tvs = $user->favoriteTvShows()->select('title')->orderByDesc('favorites.created_at')->limit($this->maxTitles)->pluck('title')->toArray();
        
        $merged = array_merge($movies, $tvs);
        return array_slice($merged, 0, $this->maxTitles);
    }

    /**
     * Lấy danh sách phim trong watchlist
     */
    private function getWatchlistedTitles(User $user): array
    {
        $movies = $user->watchlistedMovies()->select('title')->orderByDesc('watchlists.created_at')->limit($this->maxTitles)->pluck('title')->toArray();
        $tvs = $user->watchlistedTvShows()->select('title')->orderByDesc('watchlists.created_at')->limit($this->maxTitles)->pluck('title')->toArray();
        
        $merged = array_merge($movies, $tvs);
        return array_slice($merged, 0, $this->maxTitles);
    }

    /**
     * Lấy từ khóa tìm kiếm gần đây
     */
    private function getRecentSearches(User $user): array
    {
        $since = Carbon::now()->subDays($this->tasteProfileDays);
        
        $keywords = SearchHistory::where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->whereRaw('LENGTH(keyword) >= 3')
            ->orderByDesc('created_at')
            ->limit(20)
            ->pluck('keyword')
            ->unique()
            ->values()
            ->take($this->maxKeywords)
            ->toArray();
            
        return $keywords;
    }

    /**
     * Phân tích xu hướng (nhẹ nhàng, không phán xét mạnh)
     */
    private function analyzeTendency(User $user, array $recentGenres): array
    {
        $ratingTendency = null;
        $contentTendency = null;

        // Phân tích Rating
        $highRatings = Review::where('user_id', $user->id)
            ->whereNotNull('rating')
            ->where('rating', '>=', 8)
            ->count();
            
        $lowRatings = Review::where('user_id', $user->id)
            ->whereNotNull('rating')
            ->where('rating', '<=', 4)
            ->count();

        if ($highRatings > 5 && $highRatings > $lowRatings * 2) {
            $ratingTendency = 'Có xu hướng đánh giá tích cực các phim đã xem.';
        } elseif ($lowRatings > 5 && $lowRatings > $highRatings * 2) {
            $ratingTendency = 'Có gu đánh giá khá khắt khe.';
        }

        // Phân tích Content (Dựa trên recent genres)
        $lightGenres = ['Hài Hước', 'Hoạt Hình', 'Gia Đình', 'Tình Cảm', 'Animation', 'Comedy', 'Family', 'Romance'];
        $heavyGenres = ['Kinh Dị', 'Tâm Lý', 'Tội Phạm', 'Chiến Tranh', 'Horror', 'Drama', 'Crime', 'War'];

        $lightCount = count(array_intersect($recentGenres, $lightGenres));
        $heavyCount = count(array_intersect($recentGenres, $heavyGenres));

        if ($lightCount >= 2 && $lightCount > $heavyCount) {
            $contentTendency = 'có xu hướng xem nội dung nhẹ nhàng, giải trí';
        } elseif ($heavyCount >= 2 && $heavyCount > $lightCount) {
            $contentTendency = 'có xu hướng xem nội dung kịch tính, tâm lý sâu sắc';
        }

        return [
            'rating_tendency'  => $ratingTendency,
            'content_tendency' => $contentTendency,
        ];
    }
}
