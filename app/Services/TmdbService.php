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

        // Nếu title/name rỗng, thử lại với English
        if ($result && $this->fallbackLanguage !== $this->language) {
            $needsFallback = false;

            if (isset($result['title']) && empty($result['title'])) {
                $needsFallback = true;
            }
            if (isset($result['name']) && empty($result['name'])) {
                $needsFallback = true;
            }

            if ($needsFallback) {
                $params['language'] = $this->fallbackLanguage;
                return $this->get($endpoint, $params);
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
            'append_to_response' => 'credits,videos',
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
    //  People
    // ═══════════════════════════════════════

    /**
     * Lấy chi tiết người theo TMDb ID.
     */
    public function getPersonDetail(int $tmdbId): ?array
    {
        return $this->getWithFallback("/person/{$tmdbId}");
    }

    /**
     * Lấy danh sách người nổi tiếng.
     */
    public function getPopularPeople(int $page = 1): ?array
    {
        return $this->get('/person/popular', ['page' => $page]);
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
