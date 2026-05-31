<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $language;
    protected string $fallbackLanguage;
    protected string $imageBaseUrl;

    public function __construct()
    {
        $this->baseUrl = config('tmdb.base_url');
        $this->apiKey = config('tmdb.api_key');
        $this->language = config('tmdb.language');
        $this->fallbackLanguage = config('tmdb.fallback_language');
        $this->imageBaseUrl = config('tmdb.image_base_url');
    }

    // ═══════════════════════════════════════
    //  HTTP Helper
    // ═══════════════════════════════════════

    /**
     * Gọi TMDb API GET request.
     */
    protected function get(string $endpoint, array $params = []): ?array
    {
        $params = array_merge([
            'api_key' => $this->apiKey,
            'language' => $this->language,
        ], $params);

        try {
            $response = Http::timeout(15)
                ->retry(3, 500)
                ->get("{$this->baseUrl}{$endpoint}", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("TMDb API error: {$response->status()} for {$endpoint}");
            return null;
        } catch (\Exception $e) {
            Log::error("TMDb API exception: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Gọi API với fallback language nếu kết quả tiếng Việt rỗng.
     */
    protected function getWithFallback(string $endpoint, array $params = []): ?array
    {
        $result = $this->get($endpoint, $params);

        // Nếu title/name rỗng HOẶC title giống hệt original_title mà tiếng gốc không phải tiếng Anh -> fallback sang tiếng Anh
        if ($result && $this->fallbackLanguage !== $this->language) {
            $needsFallback = false;

            if (isset($result['title'])) {
                if (empty($result['title'])) {
                    $needsFallback = true;
                } elseif (isset($result['original_title'], $result['original_language']) 
                          && $result['title'] === $result['original_title'] 
                          && $result['original_language'] !== 'en') {
                    $needsFallback = true;
                }
            }
            if (isset($result['name'])) {
                if (empty($result['name'])) {
                    $needsFallback = true;
                } elseif (isset($result['original_name'], $result['original_language']) 
                          && $result['name'] === $result['original_name'] 
                          && $result['original_language'] !== 'en') {
                    $needsFallback = true;
                }
            }

            if ($needsFallback) {
                $params['language'] = $this->fallbackLanguage;
                $fallbackResult = $this->get($endpoint, $params);
                // Gộp lại: dùng title/name tiếng Anh nhưng vẫn giữ các thông tin khác
                if ($fallbackResult) {
                    if (isset($fallbackResult['title'])) {
                        $result['title'] = $fallbackResult['title'];
                    }
                    if (isset($fallbackResult['name'])) {
                        $result['name'] = $fallbackResult['name'];
                    }
                    if (empty($result['overview']) && !empty($fallbackResult['overview'])) {
                        $result['overview'] = $fallbackResult['overview'];
                    }
                }
            }
        }

        return $result;
    }

    // ═══════════════════════════════════════
    //  Genres
    // ═══════════════════════════════════════

    /**
     * Lấy danh sách thể loại phim.
     */
    public function getGenres(): ?array
    {
        $data = $this->get('/genre/movie/list');
        return $data['genres'] ?? null;
    }

    // ═══════════════════════════════════════
    //  Movies
    // ═══════════════════════════════════════

    /**
     * Lấy phim phổ biến (phân trang).
     */
    public function getPopularMovies(int $page = 1): ?array
    {
        return $this->get('/movie/popular', ['page' => $page]);
    }

    /**
     * Lấy phim đang chiếu.
     */
    public function getNowPlayingMovies(int $page = 1): ?array
    {
        return $this->get('/movie/now_playing', ['page' => $page]);
    }

    /**
     * Lấy phim được đánh giá cao.
     */
    public function getTopRatedMovies(int $page = 1): ?array
    {
        return $this->get('/movie/top_rated', ['page' => $page]);
    }

    /**
     * Lấy phim sắp chiếu.
     */
    public function getUpcomingMovies(int $page = 1): ?array
    {
        return $this->get('/movie/upcoming', ['page' => $page]);
    }

    /**
     * Lấy chi tiết phim theo TMDb ID.
     */
    public function getMovieDetail(int $tmdbId): ?array
    {
        return $this->getWithFallback("/movie/{$tmdbId}", [
            'append_to_response' => 'credits,videos,keywords,images,release_dates',
            'include_image_language' => 'vi,en,null',
        ]);
    }

    /**
     * Tìm kiếm phim.
     */
    public function searchMovies(string $query, int $page = 1): ?array
    {
        return $this->get('/search/movie', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    /**
     * Discover phim theo bộ lọc.
     */
    public function discoverMovies(array $filters = [], int $page = 1): ?array
    {
        return $this->get('/discover/movie', array_merge($filters, ['page' => $page]));
    }

    // ═══════════════════════════════════════
    //  TV Shows
    // ═══════════════════════════════════════

    /**
     * Lấy danh sách thể loại TV series.
     */
    public function getTvGenres(): ?array
    {
        $data = $this->get('/genre/tv/list');
        return $data['genres'] ?? null;
    }

    /**
     * Lấy TV series phổ biến.
     */
    public function getPopularTvShows(int $page = 1): ?array
    {
        return $this->get('/tv/popular', ['page' => $page]);
    }

    /**
     * Lấy TV series được đánh giá cao.
     */
    public function getTopRatedTvShows(int $page = 1): ?array
    {
        return $this->get('/tv/top_rated', ['page' => $page]);
    }

    /**
     * Lấy TV series đang chiếu hôm nay.
     */
    public function getAiringTodayTvShows(int $page = 1): ?array
    {
        return $this->get('/tv/airing_today', ['page' => $page]);
    }

    /**
     * Lấy TV series đang phát sóng tuần này.
     */
    public function getOnTheAirTvShows(int $page = 1): ?array
    {
        return $this->get('/tv/on_the_air', ['page' => $page]);
    }

    /**
     * Lấy chi tiết TV series theo TMDb ID (bao gồm credits, videos).
     */
    public function getTvShowDetail(int $tmdbId): ?array
    {
        return $this->getWithFallback("/tv/{$tmdbId}", [
            'append_to_response' => 'credits,videos,keywords,images,content_ratings',
            'include_image_language' => 'vi,en,null',
        ]);
    }

    /**
     * Tìm kiếm TV series.
     */
    public function searchTvShows(string $query, int $page = 1): ?array
    {
        return $this->get('/search/tv', [
            'query' => $query,
            'page'  => $page,
        ]);
    }

    // ═══════════════════════════════════════
    //  People
    // ═══════════════════════════════════════


    /**
     * Lấy chi tiết người theo TMDb ID.
     */
    public function getPersonDetail(int $tmdbId): ?array
    {
        $data = $this->get("/person/{$tmdbId}", [
            'append_to_response' => 'external_ids',
        ]);

        if (!$data) {
            return null;
        }

        // Nếu tiểu sử rỗng (TMDB không có bản dịch tiếng Việt),
        // tự động lấy lại bằng tiếng Anh.
        if (empty($data['biography']) && $this->fallbackLanguage !== $this->language) {
            $en = $this->get("/person/{$tmdbId}", [
                'language'           => $this->fallbackLanguage,
                'append_to_response' => 'external_ids',
            ]);
            if (!empty($en['biography'])) {
                $data['biography'] = $en['biography'];
            }
        }

        return $data;
    }

    /**
     * Lấy danh sách người nổi tiếng.
     */
    public function getPopularPeople(int $page = 1): ?array
    {
        return $this->get('/person/popular', ['page' => $page]);
    }

    // ═══════════════════════════════════════
    //  Media
    // ═══════════════════════════════════════

    /**
     * Lấy danh sách các ứng viên trailer phù hợp từ mảng videos của TMDB.
     */
    public function getTrailerCandidates(array $videos): array
    {
        if (empty($videos)) {
            return [];
        }

        $candidates = [];
        
        foreach ($videos as $video) {
            if ($video['site'] !== 'YouTube') {
                continue;
            }

            $name = mb_strtolower($video['name'] ?? '', 'UTF-8');
            
            // Tránh các video không phải là trailer thực sự
            if (preg_match('/(clip|featurette|interview|recap|behind the scenes|making of)/', $name)) {
                continue;
            }

            $isOfficial = ($video['official'] ?? false) === true;
            $isTrailer = in_array($video['type'], ['Trailer', 'Teaser']);
            $isGoodName = preg_match('/(official trailer|trailer|teaser)/', $name);

            if ($isTrailer || $isGoodName) {
                // Tính điểm ưu tiên (cao hơn = ưu tiên hơn)
                $score = 0;
                if ($isOfficial) $score += 10;
                if ($video['type'] === 'Trailer') $score += 5;
                if ($video['type'] === 'Teaser') $score += 2;
                if (str_contains($name, 'official trailer')) $score += 5;

                $candidates[] = [
                    'key' => $video['key'],
                    'name' => $video['name'],
                    'type' => $video['type'],
                    'score' => $score,
                    'url' => "https://www.youtube.com/watch?v={$video['key']}",
                    'embed_url' => "https://www.youtube.com/embed/{$video['key']}?autoplay=1&rel=0",
                ];
            }
        }

        // Sắp xếp theo score giảm dần
        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);

        // Tuỳ chọn kiểm tra YouTube API nếu có YOUTUBE_API_KEY
        $apiKey = env('YOUTUBE_API_KEY');
        if ($apiKey && !empty($candidates)) {
            $videoIds = implode(',', array_column($candidates, 'key'));
            try {
                $response = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'status',
                    'id' => $videoIds,
                    'key' => $apiKey,
                ]);

                if ($response->successful()) {
                    $items = $response->json('items') ?? [];
                    $validIds = [];
                    foreach ($items as $item) {
                        $status = $item['status'] ?? [];
                        if (($status['embeddable'] ?? false) === true && ($status['privacyStatus'] ?? '') === 'public') {
                            $validIds[] = $item['id'];
                        }
                    }
                    // Lọc những candidates hợp lệ
                    $candidates = array_filter($candidates, fn($c) => in_array($c['key'], $validIds));
                }
            } catch (\Exception $e) {
                Log::warning("YouTube API error in getTrailerCandidates: " . $e->getMessage());
            }
        }

        return array_values($candidates);
    }


    /**
     * Lấy media (videos, backdrops, posters) từ TMDb (có cache).
     */
    public function getMedia(int $tmdbId, string $type = 'movie'): array
    {
        $cacheKey = "tmdb_media_{$type}_{$tmdbId}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDays(7), function () use ($tmdbId, $type) {
            $data = $this->getWithFallback("/{$type}/{$tmdbId}", [
                'append_to_response' => 'videos,images',
                'include_image_language' => 'vi,en,null',
            ]);

            if (!$data) {
                return ['videos' => [], 'backdrops' => [], 'posters' => []];
            }

            return [
                'videos' => $data['videos']['results'] ?? [],
                'backdrops' => $data['images']['backdrops'] ?? [],
                'posters' => $data['images']['posters'] ?? [],
            ];
        });
    }

    // ═══════════════════════════════════════
    //  Image URL Helpers
    // ═══════════════════════════════════════

    /**
     * Tạo URL đầy đủ cho poster.
     */
    public function posterUrl(?string $path, string $size = 'medium'): ?string
    {
        if (!$path)
            return null;
        $sizeCode = config("tmdb.poster_sizes.{$size}", 'w342');
        return $this->imageBaseUrl . $sizeCode . $path;
    }

    /**
     * Tạo URL đầy đủ cho backdrop.
     */
    public function backdropUrl(?string $path, string $size = 'large'): ?string
    {
        if (!$path)
            return null;
        $sizeCode = config("tmdb.backdrop_sizes.{$size}", 'w1280');
        return $this->imageBaseUrl . $sizeCode . $path;
    }

    /**
     * Tạo URL đầy đủ cho ảnh người.
     */
    public function profileUrl(?string $path, string $size = 'medium'): ?string
    {
        if (!$path)
            return null;
        $sizeCode = config("tmdb.profile_sizes.{$size}", 'w185');
        return $this->imageBaseUrl . $sizeCode . $path;
    }
}
