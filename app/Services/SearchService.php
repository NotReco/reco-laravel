<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\TvShow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

/**
 * SearchService – Ngày 4: Search Optimization / Fuzzy Search
 *
 * Hỗ trợ:
 *  - Tìm kiếm cả Movie và TV Show theo title, original_title, synopsis, genre.
 *  - Sắp xếp kết quả theo độ liên quan (relevance score).
 *  - Fuzzy search nhẹ bằng PHP similar_text() / levenshtein() – không cần Elasticsearch.
 *  - Cache ngắn hạn (5 phút) cho keyword phổ biến bằng Laravel Cache Facade.
 *  - Giữ nguyên format response tương thích với frontend hiện tại.
 */
class SearchService
{
    /**
     * Số ký tự tối thiểu để cache search result.
     */
    private const CACHE_MIN_LENGTH = 3;

    /**
     * TTL cache (phút).
     */
    private const CACHE_TTL_MINUTES = 5;

    /**
     * Số kết quả tối đa trả về.
     */
    private const MAX_RESULTS = 8;

    /**
     * Số kết quả lấy từ DB trước khi rank (mỗi loại).
     */
    private const DB_FETCH_LIMIT = 20;

    /**
     * Ngưỡng similar_text (%) để coi là fuzzy match.
     * 70% trở lên là gần đúng.
     */
    private const FUZZY_THRESHOLD = 70;

    // ──────────────────────────────────────────────────────────────
    //  PUBLIC API
    // ──────────────────────────────────────────────────────────────

    /**
     * Tìm kiếm phim & TV show theo keyword.
     *
     * Trả về Collection<array> với các key:
     *   id, title, url, poster, release_year, type
     *
     * @param  string  $rawKeyword  Keyword thô từ user input
     * @return \Illuminate\Support\Collection
     */
    public function search(string $rawKeyword): \Illuminate\Support\Collection
    {
        // 1. Chuẩn hoá keyword
        $keyword = $this->normalizeKeyword($rawKeyword);

        if (mb_strlen($keyword) < 2) {
            return collect();
        }

        // 2. Cache ngắn hạn (chỉ cache keyword đủ dài)
        if (mb_strlen($keyword) >= self::CACHE_MIN_LENGTH) {
            $cacheKey = $this->buildCacheKey($keyword);
            return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($keyword) {
                return $this->doSearch($keyword);
            });
        }

        return $this->doSearch($keyword);
    }

    // ──────────────────────────────────────────────────────────────
    //  INTERNAL SEARCH LOGIC
    // ──────────────────────────────────────────────────────────────

    /**
     * Thực hiện tìm kiếm thật sự (không cache).
     */
    private function doSearch(string $keyword): \Illuminate\Support\Collection
    {
        // 3. Lấy fuzzy variants để mở rộng truy vấn DB
        $variants = $this->buildFuzzyVariants($keyword);

        // 4. Query DB (Movie + TvShow)
        $movies  = $this->queryMovies($keyword, $variants);
        $tvShows = $this->queryTvShows($keyword, $variants);

        // 5. Gộp, tính điểm, sắp xếp
        $all = $movies->concat($tvShows);
        $all = $this->rankResults($all, $keyword);

        // 6. Chỉ giữ lại self::MAX_RESULTS kết quả tốt nhất
        return $all->take(self::MAX_RESULTS)->values();
    }

    // ──────────────────────────────────────────────────────────────
    //  DB QUERIES
    // ──────────────────────────────────────────────────────────────

    /**
     * Truy vấn Movie theo keyword (và fuzzy variants).
     */
    private function queryMovies(string $keyword, array $variants): \Illuminate\Support\Collection
    {
        $query = Movie::with('genres')
            ->select('id', 'slug', 'title', 'original_title', 'synopsis', 'poster', 'release_date', 'view_count', 'avg_rating', 'rating_count')
            ->where(function ($q) use ($keyword, $variants) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('original_title', 'like', "%{$keyword}%")
                  ->orWhere('synopsis', 'like', "%{$keyword}%")
                  ->orWhereHas('genres', function ($gq) use ($keyword) {
                      $gq->where('name', 'like', "%{$keyword}%");
                  });

                // Fuzzy variants (thêm các biến thể sai chính tả)
                foreach ($variants as $v) {
                    $q->orWhere('title', 'like', "%{$v}%")
                      ->orWhere('original_title', 'like', "%{$v}%");
                }
            })
            ->limit(self::DB_FETCH_LIMIT)
            ->get();

        return $query->map(function ($m) {
            $m->type         = 'movie';
            $m->url          = route('movies.show', $m->slug);
            $m->release_year = $m->release_date ? \Carbon\Carbon::parse($m->release_date)->format('Y') : '';
            // Đảm bảo poster URL nhất quán
            $m->poster = $m->poster && !str_starts_with($m->poster, 'http')
                ? '/storage/' . $m->poster
                : $m->poster;
            return $m;
        });
    }

    /**
     * Truy vấn TvShow theo keyword (và fuzzy variants).
     */
    private function queryTvShows(string $keyword, array $variants): \Illuminate\Support\Collection
    {
        $query = TvShow::with('genres')
            ->select('id', 'slug', 'title', 'original_title', 'synopsis', 'poster', 'first_air_date as release_date', 'view_count', 'avg_rating', 'rating_count')
            ->where(function ($q) use ($keyword, $variants) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('original_title', 'like', "%{$keyword}%")
                  ->orWhere('synopsis', 'like', "%{$keyword}%")
                  ->orWhereHas('genres', function ($gq) use ($keyword) {
                      $gq->where('name', 'like', "%{$keyword}%");
                  });

                foreach ($variants as $v) {
                    $q->orWhere('title', 'like', "%{$v}%")
                      ->orWhere('original_title', 'like', "%{$v}%");
                }
            })
            ->limit(self::DB_FETCH_LIMIT)
            ->get();

        return $query->map(function ($t) {
            $t->type         = 'tv';
            $t->url          = route('tv-shows.show', $t->slug);
            $t->release_year = $t->release_date ? \Carbon\Carbon::parse($t->release_date)->format('Y') : '';
            $t->poster = $t->poster && !str_starts_with($t->poster, 'http')
                ? '/storage/' . $t->poster
                : $t->poster;
            return $t;
        });
    }

    // ──────────────────────────────────────────────────────────────
    //  RELEVANCE SCORING & RANKING
    // ──────────────────────────────────────────────────────────────

    /**
     * Tính điểm liên quan và sắp xếp kết quả.
     *
     * Thang điểm (thấp hơn = liên quan hơn):
     *  1  – title khớp chính xác (exact match)
     *  2  – title bắt đầu bằng keyword + khoảng trắng (word prefix)
     *  3  – keyword là từ riêng trong title (word boundary)
     *  4  – title bắt đầu bằng keyword (prefix)
     *  5  – title chứa keyword (substring)
     *  6  – original_title khớp chính xác
     *  7  – original_title bắt đầu bằng keyword
     *  8  – original_title chứa keyword
     *  9  – synopsis chứa keyword
     * 10  – genre chứa keyword
     * 11  – fuzzy match (similar_text >= threshold)
     * 12  – không khớp (fallback, ít xảy ra vì đã lọc DB)
     *
     * Sau đó secondary sort: avg_rating DESC, view_count DESC
     */
    private function rankResults(\Illuminate\Support\Collection $results, string $keyword): \Illuminate\Support\Collection
    {
        $q = mb_strtolower(Str::ascii($keyword));

        $results = $results->map(function ($item) use ($q) {
            $title    = mb_strtolower(Str::ascii($item->title ?? ''));
            $orig     = mb_strtolower(Str::ascii($item->original_title ?? ''));
            $synopsis = mb_strtolower(Str::ascii($item->synopsis ?? ''));
            $genres   = $item->genres ? implode(' ', $item->genres->pluck('name')->map(fn($g) => mb_strtolower(Str::ascii($g)))->toArray()) : '';

            $score = $this->calculateScore($q, $title, $orig, $synopsis, $genres);

            // Bonus score: thưởng cho nội dung phổ biến (giảm score = tăng thứ hạng)
            // Mỗi 1.0 avg_rating trừ đi 0.05 điểm, view_count / 1000000 trừ thêm 0.1 điểm
            $popularityBonus = (($item->avg_rating ?? 0) * 0.05)
                             + (min($item->view_count ?? 0, 1000000) / 1000000 * 0.1);

            $item->relevance_score   = $score - $popularityBonus;
            $item->relevance_tier    = $score; // tier nguyên để sort chắc chắn
            return $item;
        });

        // Sắp xếp: primary = relevance_tier ASC, secondary = avg_rating DESC, tertiary = view_count DESC
        return $results
            ->sortBy([
                ['relevance_tier',   'asc'],
                ['avg_rating',       'desc'],
                ['view_count',       'desc'],
            ])
            ->values();
    }

    /**
     * Tính điểm liên quan cho 1 item.
     */
    private function calculateScore(
        string $q,
        string $title,
        string $orig,
        string $synopsis,
        string $genres
    ): int {
        // ── Title ──
        if ($title === $q) {
            return 1;
        }
        if (str_starts_with($title, $q . ' ')) {
            return 2;
        }
        if (str_contains($title, ' ' . $q . ' ') || str_ends_with($title, ' ' . $q)) {
            return 3;
        }
        if (str_starts_with($title, $q)) {
            return 4;
        }
        if (str_contains($title, $q)) {
            return 5;
        }

        // ── Original Title ──
        if ($orig !== '' && $orig === $q) {
            return 6;
        }
        if ($orig !== '' && str_starts_with($orig, $q)) {
            return 7;
        }
        if ($orig !== '' && str_contains($orig, $q)) {
            return 8;
        }

        // ── Synopsis ──
        if ($synopsis !== '' && str_contains($synopsis, $q)) {
            return 9;
        }

        // ── Genre ──
        if ($genres !== '' && str_contains($genres, $q)) {
            return 10;
        }

        // ── Fuzzy match (similar_text trên title hoặc original_title) ──
        if ($this->isFuzzyMatch($q, $title) || ($orig !== '' && $this->isFuzzyMatch($q, $orig))) {
            return 11;
        }

        return 12; // Không khớp rõ (hiếm xảy ra do đã filter DB)
    }

    // ──────────────────────────────────────────────────────────────
    //  FUZZY SEARCH HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Kiểm tra xem keyword có "gần giống" với target không
     * bằng cách dùng similar_text() PHP built-in.
     *
     * similar_text($a, $b, $percent) → tính % giống nhau.
     * Nếu % >= FUZZY_THRESHOLD thì coi là khớp.
     *
     * Ví dụ:
     *   "interstelar" vs "interstellar" → ~93% → match
     *   "avenger"     vs "avengers"     → ~93% → match
     */
    private function isFuzzyMatch(string $keyword, string $target): bool
    {
        if ($keyword === '' || $target === '') {
            return false;
        }

        // Kiểm tra với từng từ trong target (tránh bị ảnh hưởng bởi toàn bộ chuỗi dài)
        $words = explode(' ', $target);
        foreach ($words as $word) {
            if (mb_strlen($word) < 3) continue;

            similar_text($keyword, $word, $percent);
            if ($percent >= self::FUZZY_THRESHOLD) {
                return true;
            }

            // Levenshtein bổ sung cho keyword ngắn (≤ 8 ký tự)
            if (mb_strlen($keyword) <= 8) {
                $distance = levenshtein($keyword, $word);
                // Cho phép edit distance 1–2 tuỳ độ dài keyword
                $maxDistance = mb_strlen($keyword) <= 5 ? 1 : 2;
                if ($distance <= $maxDistance) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Tạo danh sách biến thể fuzzy để mở rộng truy vấn SQL.
     *
     * Chiến lược:
     *  - Nếu keyword kết thúc bằng 's', thêm bản không có 's' (avengers → avenger)
     *  - Nếu keyword KHÔNG kết thúc bằng 's', thêm bản có 's' (avenger → avengers)
     *  - Loại bỏ ký tự đôi ở cuối (interstelar → interstellar không bắt được,
     *    nhưng ta dùng similar_text trong post-processing để bắt được)
     *
     * Lưu ý: Các variant này chỉ được dùng cho LIKE query trên DB,
     * không dùng để thay thế keyword gốc.
     */
    private function buildFuzzyVariants(string $keyword): array
    {
        $variants = [];

        // Biến thể plural/singular
        if (str_ends_with($keyword, 's') && mb_strlen($keyword) > 3) {
            $variants[] = mb_substr($keyword, 0, -1); // bỏ s
        } else {
            $variants[] = $keyword . 's'; // thêm s
        }

        // Biến thể bỏ ký tự cuối (catch typo như "avengr" → "avenger")
        if (mb_strlen($keyword) > 4) {
            $variants[] = mb_substr($keyword, 0, -1); // truncate 1 ký tự
        }

        // Bỏ duplicate và trả về
        return array_unique(array_filter($variants, fn($v) => $v !== $keyword && mb_strlen($v) >= 2));
    }

    // ──────────────────────────────────────────────────────────────
    //  KEYWORD NORMALISATION
    // ──────────────────────────────────────────────────────────────

    /**
     * Chuẩn hoá keyword:
     *  - Trim khoảng trắng
     *  - Lowercase
     *  - Loại bỏ ký tự SQL wildcard (%, _)
     *  - Giữ lại dấu tiếng Việt (không strip accent) để tìm tên phim Việt đúng hơn
     */
    public function normalizeKeyword(string $raw): string
    {
        $kw = trim($raw);
        $kw = str_replace(['%', '_'], '', $kw); // Strip SQL wildcards
        $kw = mb_strtolower($kw, 'UTF-8');
        return $kw;
    }

    /**
     * Cache key cho search results.
     * Normalize thêm bước ascii để key nhất quán.
     */
    private function buildCacheKey(string $normalizedKeyword): string
    {
        $safeKey = Str::slug($normalizedKeyword, '_');
        return "search.result.{$safeKey}";
    }

    // ──────────────────────────────────────────────────────────────
    //  FORMAT OUTPUT (tương thích frontend)
    // ──────────────────────────────────────────────────────────────

    /**
     * Format kết quả để trả về JSON, tương thích với frontend hiện tại.
     * Thêm field "type" để frontend phân biệt movie / tv.
     *
     * @param  \Illuminate\Support\Collection  $results
     * @return \Illuminate\Support\Collection
     */
    public function formatForResponse(\Illuminate\Support\Collection $results): \Illuminate\Support\Collection
    {
        return $results->map(function ($item) {
            return [
                'id'           => $item->id,
                'title'        => $item->title,
                'url'          => $item->url,
                'poster'       => $item->poster,
                'release_year' => $item->release_year,
                'type'         => $item->type, // 'movie' | 'tv'
            ];
        });
    }
}
