<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Review;
use App\Models\TvShow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * AiContextService – Phase 3
 *
 * Lấy dữ liệu RecoDB phù hợp theo intent trước khi gọi Gemini.
 * Mỗi intent nhận được một snapshot ngắn gọn, chính xác của DB
 * để AI trả lời dựa trên dữ liệu thật thay vì bịa đặt.
 *
 * Nguyên tắc an toàn:
 *  - Không gửi dữ liệu nhạy cảm của user (email, password, IP...).
 *  - Không gửi full text dài – chỉ excerpt có giới hạn ký tự.
 *  - Không log API key.
 *  - Nếu DB không có dữ liệu → trả rỗng, AI sẽ nói thật.
 */
class AiContextService
{
    // ──────────────────────────────────────────────────────────────────────
    // Genre keyword → DB name mapping
    // Normalized (no diacritic) → possible DB names to try (LIKE search)
    // ──────────────────────────────────────────────────────────────────────
    private const GENRE_MAP = [
        'kinh di'              => ['Kinh Dị', 'Horror', 'Kinh di'],
        'hanh dong'            => ['Hành Động', 'Action', 'Hanh dong'],
        'hai huoc'             => ['Hài Hước', 'Comedy', 'Hai'],
        'phim hai'             => ['Hài Hước', 'Comedy'],
        'tinh cam'             => ['Tình Cảm', 'Romance', 'Tinh cam'],
        'romantic'             => ['Tình Cảm', 'Romance'],
        'anime'                => ['Anime', 'Hoạt Hình', 'Animation'],
        'hoat hinh'            => ['Hoạt Hình', 'Animation', 'Anime'],
        'vien tuong'           => ['Viễn Tưởng', 'Fantasy', 'Vien tuong'],
        'khoa hoc vien tuong'  => ['Khoa Học Viễn Tưởng', 'Sci-Fi', 'Science Fiction'],
        'sci-fi'               => ['Khoa Học Viễn Tưởng', 'Sci-Fi', 'Science Fiction'],
        'scifi'                => ['Khoa Học Viễn Tưởng', 'Sci-Fi', 'Science Fiction'],
        'tam ly'               => ['Tâm Lý', 'Drama'],
        'toi pham'             => ['Tội Phạm', 'Crime', 'Toi pham'],
        'phieu luu'            => ['Phiêu Lưu', 'Adventure', 'Phieu luu'],
        'chien tranh'          => ['Chiến Tranh', 'War'],
        'tai lieu'             => ['Tài Liệu', 'Documentary'],
        'gia dinh'             => ['Gia Đình', 'Family'],
        'horror'               => ['Kinh Dị', 'Horror'],
        'comedy'               => ['Hài Hước', 'Comedy'],
        'drama'                => ['Tâm Lý', 'Drama'],
        'fantasy'              => ['Viễn Tưởng', 'Fantasy'],
        'action'               => ['Hành Động', 'Action'],
        'thriller'             => ['Hồi Hộp', 'Thriller'],
    ];

    // ──────────────────────────────────────────────────────────────────────
    // Limits (from config, cached in constructor)
    // ──────────────────────────────────────────────────────────────────────
    private int $maxItems;
    private int $maxReviews;
    private int $reviewExcerptLen;
    private int $synopsisLen;

    public function __construct()
    {
        $this->maxItems          = config('ai_assistant.max_context_items', 8);
        $this->maxReviews        = config('ai_assistant.max_review_excerpts', 5);
        $this->reviewExcerptLen  = config('ai_assistant.review_excerpt_length', 150);
        $this->synopsisLen       = config('ai_assistant.synopsis_length', 180);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Build the DB context snapshot for a given intent.
     *
     * @param  string      $message   Raw user message.
     * @param  string      $intent    One of AiIntentService::INTENT_* constants.
     * @param  array       $keywords  Matched keywords from AiIntentService::classify().
     * @param  mixed|null  $user      Authenticated user model or null.
     * @return array{summary: string, items: array, reviews: array, genres: array, raw_count: int}
     */
    public function buildContext(
        string $message,
        string $intent,
        array  $keywords = [],
        mixed  $user = null,
        array  $userProfile = [],
        array  $recentItems = [],
        ?string $wantsType = null
    ): array {
        try {
            return match ($intent) {
                'movie.recommend' => $this->contextRecommend($message, $keywords, $user, $userProfile, $recentItems, $wantsType),
                'movie.genre'     => $this->contextGenre($message, $keywords, $userProfile, $recentItems, $wantsType),
                'movie.review'    => $this->contextReview($message),
                'movie.popular'   => $this->contextPopular($recentItems, $wantsType),
                'movie.detail'    => $this->contextDetail($message),
                'movie.search'    => $this->contextSearch($message),
                'movie.person'    => $this->contextPerson($message, $keywords),
                'site.help'       => $this->contextSiteHelp(),
                default           => $this->contextFallback(),
            };
        } catch (\Exception $e) {
            Log::warning('AiContextService: failed to build context', [
                'intent' => $intent,
                'error'  => $e->getMessage(),
            ]);
            return $this->emptyContext('Không thể tải dữ liệu context.');
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Per-intent context builders
    // ──────────────────────────────────────────────────────────────────────

    /** movie.recommend – Top phim kết hợp genre hint từ message */
    private function contextRecommend(string $message, array $keywords, mixed $user, array $userProfile, array $recentItems = [], ?string $wantsType = null): array
    {
        $limit = $this->maxItems;

        $genreNamesToFilter = [];

        // If user profile is available, extract preferred genres (favorite + recent)
        if (!empty($userProfile['available'])) {
            $favGenres = $userProfile['favorite_genres'] ?? [];
            $recGenres = $userProfile['recent_genres'] ?? [];
            $genreNamesToFilter = array_unique(array_merge($favGenres, $recGenres));
        }

        // Also detect genre keyword from message to refine
        $detectedGenreNames = $this->detectGenreNamesFromMessage($message);

        $movieQuery = Movie::with('genres')
            ->active()
            ->select(['poster', 'id', 'slug', 'title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'release_date'])
            ->whereNotNull('poster');

        $tvQuery = TvShow::with('genres')
            ->active()
            ->select(['poster', 'id', 'slug', 'title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'first_air_date'])
            ->whereNotNull('poster');

        // Apply genre filter if available
        if (!empty($detectedGenreNames)) {
            // Message keywords take precedence
            $filterNames = $detectedGenreNames;
        } else {
            $filterNames = $genreNamesToFilter;
        }

        if (!empty($filterNames)) {
            $movieQuery->whereHas('genres', fn($q) => $q->where(function ($sub) use ($filterNames) {
                foreach ($filterNames as $name) {
                    $sub->orWhere('genres.name', 'like', "%{$name}%");
                }
            }));
            $tvQuery->whereHas('genres', fn($q) => $q->where(function ($sub) use ($filterNames) {
                foreach ($filterNames as $name) {
                    $sub->orWhere('genres.name', 'like', "%{$name}%");
                }
            }));
        }

        $movieQuery->orderByDesc('avg_rating')->orderByDesc('view_count');
        $tvQuery->orderByDesc('avg_rating')->orderByDesc('view_count');

        $all = $this->queryWithPoolAndDiversity($movieQuery, $tvQuery, $limit, $recentItems, $wantsType);

        // If genre-filtered results are thin, supplement with top popular
        if ($all->count() < 3) {
            $all = $this->fetchTopItems($limit, 'rating', $recentItems, $wantsType);
        }

        $items = $this->formatItems($all, 'movie');
        return [
            'summary'   => 'Danh sách phim gợi ý từ RecoDB',
            'items'     => $items,
            'reviews'   => [],
            'genres'    => [],
            'raw_count' => count($items),
        ];
    }

    /** movie.genre – Lọc phim theo thể loại phát hiện trong message */
    private function contextGenre(string $message, array $keywords, array $userProfile, array $recentItems = [], ?string $wantsType = null): array
    {
        $genreNames = $this->detectGenreNamesFromMessage($message);

        if (empty($genreNames)) {
            // Cannot detect genre in message. Check profile
            if (!empty($userProfile['available'])) {
                $favGenres = $userProfile['favorite_genres'] ?? [];
                $recGenres = $userProfile['recent_genres'] ?? [];
                $genreNames = array_unique(array_merge($favGenres, $recGenres));
            }
            
            // Still empty? Fallback to popular
            if (empty($genreNames)) {
                $items = $this->formatItems($this->fetchTopItems($this->maxItems, 'rating', $recentItems, $wantsType), 'movie');
                return [
                    'summary'   => 'Không xác định được thể loại cụ thể, đây là các phim nổi bật',
                    'items'     => $items,
                    'reviews'   => [],
                    'genres'    => [],
                    'raw_count' => count($items),
                ];
            }
        }

        $movieQuery = Movie::with('genres')
            ->active()
            ->select(['poster', 'id', 'slug', 'title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'release_date'])
            ->whereNotNull('poster')
            ->whereHas('genres', fn($q) => $q->where(function ($sub) use ($genreNames) {
                foreach ($genreNames as $name) {
                    $sub->orWhere('genres.name', 'like', "%{$name}%");
                }
            }))
            ->orderByDesc('avg_rating')
            ->orderByDesc('view_count');

        $tvQuery = TvShow::with('genres')
            ->active()
            ->select(['poster', 'id', 'slug', 'title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'first_air_date'])
            ->whereNotNull('poster')
            ->whereHas('genres', fn($q) => $q->where(function ($sub) use ($genreNames) {
                foreach ($genreNames as $name) {
                    $sub->orWhere('genres.name', 'like', "%{$name}%");
                }
            }))
            ->orderByDesc('avg_rating')
            ->orderByDesc('view_count');

        $all = $this->queryWithPoolAndDiversity($movieQuery, $tvQuery, $this->maxItems, $recentItems, $wantsType);
        $items = $this->formatItems($all, 'movie');

        return [
            'summary'   => 'Phim theo thể loại: ' . implode(', ', $genreNames),
            'items'     => $items,
            'reviews'   => [],
            'genres'    => $genreNames,
            'raw_count' => count($items),
        ];
    }

    /** movie.review – Tìm phim trong message, lấy các review published */
    private function contextReview(string $message): array
    {
        $titleHint = $this->extractTitleHint($message);

        if (empty($titleHint)) {
            return $this->emptyContext('Không xác định được tên phim. AI sẽ hỏi lại người dùng.');
        }

        // Try Movie first, then TvShow
        $item = $this->findItemByTitle($titleHint, 'movie')
             ?? $this->findItemByTitle($titleHint, 'tv');

        if (!$item) {
            return $this->emptyContext("Không tìm thấy phim '{$titleHint}' trong RecoDB.");
        }

        // Fetch published reviews (non-spoiler preferred, limit reviews)
        $reviewQuery = Review::query()
            ->published()
            ->fullReview()
            ->select(['poster', 'id', 'title', 'excerpt', 'content', 'rating', 'is_spoiler'])
            ->orderByDesc('likes_count')
            ->limit($this->maxReviews);

        if ($item['type'] === 'movie') {
            $reviewQuery->where('movie_id', $item['id']);
        } else {
            $reviewQuery->where('tv_show_id', $item['id']);
        }

        $reviews = $reviewQuery->get()->map(function ($r) {
            $text = $r->excerpt ?: Str::limit(strip_tags($r->content ?? ''), $this->reviewExcerptLen);
            return [
                'rating'    => $r->rating,
                'is_spoiler'=> $r->is_spoiler,
                'excerpt'   => $text,
            ];
        })->values()->toArray();

        $items = [$item];
        return [
            'summary'   => "Thông tin và review về \"{$item['title']}\"",
            'items'     => $items,
            'reviews'   => $reviews,
            'genres'    => [],
            'raw_count' => count($reviews),
        ];
    }

    /** movie.popular – Phim đang hot theo view_count + avg_rating */
    private function contextPopular(array $recentItems = [], ?string $wantsType = null): array
    {
        $all   = $this->fetchTopItems($this->maxItems, 'popular', $recentItems, $wantsType);
        $items = $this->formatItems($all, 'movie');
        return [
            'summary'   => 'Phim đang hot/nổi bật trên RecoDB',
            'items'     => $items,
            'reviews'   => [],
            'genres'    => [],
            'raw_count' => count($items),
        ];
    }

    /** movie.detail – Tìm phim cụ thể theo tên, trả chi tiết */
    private function contextDetail(string $message): array
    {
        $titleHint = $this->extractTitleHint($message);

        if (empty($titleHint)) {
            return $this->emptyContext('Không xác định được tên phim. AI sẽ hỏi lại.');
        }

        $item = $this->findItemByTitle($titleHint, 'movie')
             ?? $this->findItemByTitle($titleHint, 'tv');

        if (!$item) {
            return $this->emptyContext("Không tìm thấy phim '{$titleHint}' trong RecoDB.");
        }

        // Add directors/actors for detail context
        $this->enrichWithPeople($item);

        return [
            'summary'   => "Chi tiết phim: {$item['title']}",
            'items'     => [$item],
            'reviews'   => [],
            'genres'    => [],
            'raw_count' => 1,
        ];
    }

    /** movie.person - Phim của diễn viên / đạo diễn */
    private function contextPerson(string $message, array $keywords): array
    {
        // Trích xuất tên người từ message.
        $patterns = [
            '/\b(có phim nào của|phim của|đạo diễn bởi|đạo diễn|diễn viên|đóng phim gì|đóng phim|tham gia|có mặt|bởi|tên là|không|ai đóng)\b/iu',
            '/[?!.,;:]/u',
        ];
        $cleaned = preg_replace($patterns, ' ', $message);
        $nameHint = trim(preg_replace('/\s+/', ' ', $cleaned));

        if (mb_strlen($nameHint) < 2) {
            return $this->emptyContext('Bạn đang tìm phim của diễn viên/đạo diễn nào? Hãy cho mình biết tên nhé.');
        }

        // Tìm Person
        $person = Person::where('name', 'like', "%{$nameHint}%")->first();
        if (!$person) {
             return $this->emptyContext("Mình chưa tìm thấy {$nameHint} trong dữ liệu RecoDB. Bạn có thể thử tên diễn viên/đạo diễn khác nhé.");
        }

        // Lấy phim có người này
        $movies = Movie::with('genres')
            ->active()
            ->whereHas('people', function($q) use ($person) {
                $q->where('people.id', $person->id);
            })
            ->orderByDesc('avg_rating')
            ->limit(4)->get();

        $tvs = TvShow::with('genres')
            ->active()
            ->whereHas('people', function($q) use ($person) {
                $q->where('people.id', $person->id);
            })
            ->orderByDesc('avg_rating')
            ->limit(4)->get();

        $all = $movies->concat($tvs);
        if ($all->isEmpty()) {
            return $this->emptyContext("Mình tìm thấy người này nhưng chưa có phim liên quan trong RecoDB.");
        }

        $items = $this->formatItems($all, 'movie');
        
        $roleInfo = $person->known_for ? " (Được biết đến với vai trò: {$person->known_for})" : "";

        return [
            'summary'   => "Các phim liên quan đến {$person->name}{$roleInfo}",
            'items'     => $items,
            'reviews'   => [],
            'genres'    => [],
            'raw_count' => count($items),
        ];
    }

    /** movie.search – Dùng SearchService fuzzy search */
    private function contextSearch(string $message): array
    {
        // Strip common search prefixes to get the actual search term
        $query = $this->extractSearchQuery($message);

        if (empty($query)) {
            return $this->emptyContext('Không xác định được từ khoá tìm kiếm.');
        }

        try {
            $searchService = app(SearchService::class);
            $results       = $searchService->search($query)->take($this->maxItems);

            $items = $results->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'type'         => $item->type ?? 'movie',
                    'title'        => $item->title,
                    'genres'       => $item->genres ? $item->genres->pluck('name')->join(', ') : '',
                    'vote_average' => $item->avg_rating ?? 0,
                    'view_count'   => $item->view_count ?? 0,
                    'age_rating'   => $item->age_rating ?? '',
                    'synopsis'     => Str::limit(strip_tags($item->synopsis ?? ''), $this->synopsisLen),
                    'url'          => $item->url ?? '',
                ];
            })->values()->toArray();

        } catch (\Exception $e) {
            Log::warning('AiContextService: search failed', ['error' => $e->getMessage()]);
            $items = [];
        }

        return [
            'summary'   => empty($items)
                ? "Không tìm thấy kết quả cho '{$query}' trong RecoDB."
                : "Kết quả tìm kiếm cho '{$query}'",
            'items'     => $items,
            'reviews'   => [],
            'genres'    => [],
            'raw_count' => count($items),
        ];
    }

    /** site.help – Không cần DB; trả hướng dẫn website cố định */
    private function contextSiteHelp(): array
    {
        return [
            'summary' => 'Hướng dẫn sử dụng RecoDB',
            'items'   => [],
            'reviews' => [],
            'genres'  => [],
            'raw_count' => 0,
            'help_text' => <<<HELP
            RecoDB là nền tảng khám phá, đánh giá và chia sẻ phim.
            Các tính năng chính:
            - 🔍 Tìm kiếm phim: Dùng thanh tìm kiếm ở đầu trang.
            - ⭐ Đánh giá phim: Đăng nhập → vào trang phim → bấm "Viết Review".
            - 💛 Yêu thích / Watchlist: Bấm biểu tượng tim hoặc "+" trên card phim.
            - 👤 Tài khoản: Đăng ký miễn phí, xác minh email để mở đầy đủ tính năng.
            - 🎬 Gợi ý cá nhân: Xem mục "Gợi ý dành cho bạn" sau khi đăng nhập và xem một số phim.
            HELP,
        ];
    }

    /** Fallback khi intent không xác định */
    private function contextFallback(): array
    {
        $all   = $this->fetchTopItems(5);
        $items = $this->formatItems($all, 'movie');
        return [
            'summary'   => 'Các phim nổi bật trên RecoDB',
            'items'     => $items,
            'reviews'   => [],
            'genres'    => [],
            'raw_count' => count($items),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Fetch top items (movies + tv shows) sorted by view_count + avg_rating.
     */
    private function fetchTopItems(int $limit, string $mode = 'rating', array $recentItems = [], ?string $wantsType = null): \Illuminate\Support\Collection
    {
        $order = $mode === 'popular' ? 'view_count' : 'avg_rating';

        $movieQuery = Movie::with('genres')
            ->active()
            ->select(['poster', 'id', 'slug', 'title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'release_date'])
            ->whereNotNull('poster')
            ->orderByDesc($order)
            ->orderByDesc('view_count');

        $tvQuery = TvShow::with('genres')
            ->active()
            ->select(['poster', 'id', 'slug', 'title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'first_air_date'])
            ->whereNotNull('poster')
            ->orderByDesc($order)
            ->orderByDesc('view_count');

        return $this->queryWithPoolAndDiversity($movieQuery, $tvQuery, $limit, $recentItems, $wantsType);
    }

    private function queryWithPoolAndDiversity($movieQuery, $tvQuery, int $limit, array $recentItems, ?string $wantsType): \Illuminate\Support\Collection
    {
        $poolSize = config('ai_assistant.recommendation_pool', 40);
        $half = (int) ceil($poolSize / 2);

        $movies = collect();
        if ($wantsType !== 'tv' && $movieQuery) {
            $movies = (clone $movieQuery)->limit($wantsType === 'movie' ? $poolSize : $half)->get();
        }

        $tvs = collect();
        if ($wantsType !== 'movie' && $tvQuery) {
            $tvs = (clone $tvQuery)->limit($wantsType === 'tv' ? $poolSize : ($poolSize - $half))->get();
        }

        $all = $movies->concat($tvs);
        $filtered = $this->excludeRecentItems($all, $recentItems);
        
        // Fallback: if exclusion removes too many, allow them back but put at the end
        if ($filtered->count() < $limit) {
            $filtered = $filtered->concat($all->diff($filtered))->unique();
        }
        
        return $this->diversifyItems($filtered, $limit, $wantsType);
    }

    private function excludeRecentItems(\Illuminate\Support\Collection $items, array $recentItems): \Illuminate\Support\Collection
    {
        if (empty($recentItems)) return $items;
        
        $recentMovieIds = [];
        $recentTvIds = [];
        foreach ($recentItems as $ri) {
            if ($ri['type'] === 'movie') $recentMovieIds[] = (int)$ri['id'];
            elseif ($ri['type'] === 'tv') $recentTvIds[] = (int)$ri['id'];
        }
        
        return $items->reject(function ($item) use ($recentMovieIds, $recentTvIds) {
            $type = $item instanceof \App\Models\TvShow ? 'tv' : 'movie';
            if ($type === 'movie' && in_array($item->id, $recentMovieIds)) return true;
            if ($type === 'tv' && in_array($item->id, $recentTvIds)) return true;
            return false;
        });
    }

    private function diversifyItems(\Illuminate\Support\Collection $pool, int $limit, ?string $wantsType = null): \Illuminate\Support\Collection
    {
        if ($wantsType === 'movie') {
            $pool = $pool->filter(fn($item) => $item instanceof \App\Models\Movie);
        } elseif ($wantsType === 'tv') {
            $pool = $pool->filter(fn($item) => $item instanceof \App\Models\TvShow);
        }

        $pool = $pool->shuffle();
        
        $selected = collect();
        
        // Try to add at least 1 movie and 1 tv if no wantsType
        if (!$wantsType) {
            $movie = $pool->first(fn($item) => $item instanceof \App\Models\Movie);
            if ($movie) {
                $selected->push($movie);
                $pool = $pool->reject(fn($i) => $i->id === $movie->id && $i instanceof \App\Models\Movie);
            }
            $tv = $pool->first(fn($item) => $item instanceof \App\Models\TvShow);
            if ($tv) {
                $selected->push($tv);
                $pool = $pool->reject(fn($i) => $i->id === $tv->id && $i instanceof \App\Models\TvShow);
            }
        }
        
        // Fill the rest
        foreach ($pool as $item) {
            if ($selected->count() >= $limit) break;
            $selected->push($item);
        }
        
        return $selected;
    }

    /**
     * Format a collection of Movie/TvShow models to a lightweight array for AI context.
     */
    private function formatItems(\Illuminate\Support\Collection $collection, string $defaultType = 'movie'): array
    {
        return $collection->map(function ($item) use ($defaultType) {
            $type = $item instanceof \App\Models\TvShow ? 'tv' : 'movie';

            $urlRoute = $type === 'tv'
                ? (method_exists($item, 'getRouteKey') ? route('tv-shows.show', $item->slug ?? $item->id) : '')
                : (method_exists($item, 'getRouteKey') ? route('movies.show', $item->slug ?? $item->id) : '');

            $poster = null;
            if (!empty($item->poster)) {
                if (str_starts_with($item->poster, 'http')) {
                    $poster = $item->poster;
                } elseif (str_starts_with($item->poster, '/')) {
                    $poster = 'https://image.tmdb.org/t/p/w342' . $item->poster;
                } else {
                    $poster = asset('storage/' . $item->poster);
                }
            }
            $year = $type === 'tv' ? ($item->first_air_date ? substr($item->first_air_date, 0, 4) : null) : ($item->release_date ? substr($item->release_date, 0, 4) : null);

            $formatted = [
                'id'           => $item->id,
                'type'         => $type,
                'title'        => $item->title,
                'genres'       => $item->genres ? $item->genres->pluck('name')->join(', ') : '',
                'vote_average' => (float) ($item->avg_rating ?? 0),
                'view_count'   => (int) ($item->view_count ?? 0),
                'age_rating'   => $item->age_rating ?? '',
                'synopsis'     => Str::limit(strip_tags($item->synopsis ?? ''), $this->synopsisLen),
                'url'          => $urlRoute,
                'poster'       => $poster,
                'year'         => $year,
            ];

            // enrich actors/directors if they were loaded
            if ($item->relationLoaded('actors') && $item->actors) {
                $formatted['actors'] = $item->actors->pluck('name')->take(3)->join(', ');
            }
            if ($item->relationLoaded('directors') && $item->directors) {
                $formatted['directors'] = $item->directors->pluck('name')->take(2)->join(', ');
            }

            return $formatted;
        })->values()->toArray();
    }

    /**
     * Try to find a Movie or TvShow by a partial title match (case-insensitive LIKE).
     * Returns a formatted array or null.
     */
    private function findItemByTitle(string $hint, string $type): ?array
    {
        if ($type === 'movie') {
            $model = Movie::with('genres')
                ->active()
                ->select(['poster', 'id', 'slug', 'title', 'original_title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'release_date', 'runtime'])
                ->where(function ($q) use ($hint) {
                    $q->where('title', 'like', "%{$hint}%")
                      ->orWhere('original_title', 'like', "%{$hint}%");
                })
                ->orderByDesc('avg_rating')
                ->first();

            if (!$model) return null;

            return [
                'id'            => $model->id,
                'type'          => 'movie',
                'title'         => $model->title,
                'original_title'=> $model->original_title,
                'genres'        => $model->genres->pluck('name')->join(', '),
                'vote_average'  => (float) ($model->avg_rating ?? 0),
                'view_count'    => (int) ($model->view_count ?? 0),
                'age_rating'    => $model->age_rating ?? '',
                'runtime'       => $model->runtime ?? null,
                'release_year'  => $model->release_date?->format('Y'),
                'synopsis'      => Str::limit(strip_tags($model->synopsis ?? ''), $this->synopsisLen),
                'url'           => route('movies.show', $model->slug ?? $model->id),
            ];
        }

        // tv
        $model = TvShow::with('genres')
            ->active()
            ->select(['poster', 'id', 'slug', 'title', 'original_title', 'synopsis', 'avg_rating', 'view_count', 'age_rating', 'first_air_date', 'number_of_seasons'])
            ->where(function ($q) use ($hint) {
                $q->where('title', 'like', "%{$hint}%")
                  ->orWhere('original_title', 'like', "%{$hint}%");
            })
            ->orderByDesc('avg_rating')
            ->first();

        if (!$model) return null;

        return [
            'id'            => $model->id,
            'type'          => 'tv',
            'title'         => $model->title,
            'original_title'=> $model->original_title,
            'genres'        => $model->genres->pluck('name')->join(', '),
            'vote_average'  => (float) ($model->avg_rating ?? 0),
            'view_count'    => (int) ($model->view_count ?? 0),
            'age_rating'    => $model->age_rating ?? '',
            'seasons'       => $model->number_of_seasons ?? null,
            'release_year'  => $model->first_air_date?->format('Y'),
            'synopsis'      => Str::limit(strip_tags($model->synopsis ?? ''), $this->synopsisLen),
            'url'           => route('tv-shows.show', $model->slug ?? $model->id),
        ];
    }

    /**
     * Try to add directors/actors to a formatted item array (best-effort).
     */
    private function enrichWithPeople(array &$item): void
    {
        try {
            if ($item['type'] === 'movie') {
                $model = Movie::with(['directors', 'actors' => fn($q) => $q->limit(5)])
                    ->find($item['id']);
            } else {
                $model = TvShow::with(['directors', 'actors' => fn($q) => $q->limit(5)])
                    ->find($item['id']);
            }

            if (!$model) return;

            $item['directors'] = $model->directors->pluck('name')->join(', ');
            $item['actors']    = $model->actors->pluck('name')->join(', ');
        } catch (\Exception) {
            // Non-critical, skip silently
        }
    }

    /**
     * Detect genre DB names from user message using GENRE_MAP + normalization.
     *
     * @return string[]  List of genre name strings to use in LIKE queries
     */
    private function detectGenreNamesFromMessage(string $message): array
    {
        $normalized = $this->normalizeText($message);
        $found      = [];

        foreach (self::GENRE_MAP as $keyword => $dbNames) {
            if (str_contains($normalized, $keyword)) {
                foreach ($dbNames as $name) {
                    $found[] = $name;
                }
                break; // First match wins (most specific keyword first in map)
            }
        }

        return array_unique($found);
    }

    /**
     * Extract a plausible movie/show title hint from the message.
     * Strips common Vietnamese question words so only the proper noun remains.
     */
    private function extractTitleHint(string $message): string
    {
        // Remove common question prefixes/suffixes
        $patterns = [
            '/\b(review|đánh giá|nội dung|phim này nói về|phim nói về|tìm|kiếm|chi tiết|thông tin về|xem phim|có phim)\b/iu',
            '/\b(thế nào|như thế nào|hay không|có hay không|là gì|ra sao)\b/iu',
            '/\b(phim|movie|tv show|series|bộ phim|bộ)\b/iu',
            '/[?!.,;:]/u',
        ];

        $cleaned = preg_replace($patterns, ' ', $message);
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned ?? ''));

        // Must be at least 2 chars to be a useful hint
        return mb_strlen($cleaned) >= 2 ? $cleaned : '';
    }

    /**
     * Extract search query from message (strip "tìm phim", "kiếm" etc.).
     */
    private function extractSearchQuery(string $message): string
    {
        $patterns = [
            '/\b(tìm phim|tìm|kiếm phim|kiếm|tim phim|tim|search|kiem)\b/iu',
            '/\b(giúp tôi|giúp mình|giup toi|giup minh|cho tôi|cho mình)\b/iu',
            '/[?!.,;:]/u',
        ];

        $cleaned = preg_replace($patterns, ' ', $message);
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned ?? ''));

        return mb_strlen($cleaned) >= 2 ? $cleaned : '';
    }

    /**
     * Lowercase + strip Vietnamese diacritics for keyword matching.
     */
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        $diacritics = [
            'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
            'â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
            'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
            'ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
            'ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
            'ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
            'ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
            'đ'=>'d',
        ];

        return strtr($text, $diacritics);
    }

    /**
     * Empty context with an explanatory summary (AI uses this to say "not found").
     */
    private function emptyContext(string $summary): array
    {
        return [
            'summary'   => $summary,
            'items'     => [],
            'reviews'   => [],
            'genres'    => [],
            'raw_count' => 0,
        ];
    }

    // ──────────────────────────────────────────────────────────────────────
    // Context → plain text formatter (for injection into system prompt)
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Convert the structured context array to a compact text block
     * suitable for embedding in the Gemini system prompt.
     *
     * @param array $context  Result of buildContext().
     * @return string
     */
    public function toPromptText(array $context): string
    {
        $lines = [];
        $lines[] = '=== DỮ LIỆU NGỮ CẢNH TỪ RECODB ===';
        $lines[] = $context['summary'];

        // help_text shortcut
        if (!empty($context['help_text'])) {
            $lines[] = $context['help_text'];
            return implode("\n", $lines);
        }

        // Items
        if (!empty($context['items'])) {
            $lines[] = '';
            $lines[] = 'Danh sách phim/series:';
            foreach ($context['items'] as $item) {
                $rating  = $item['vote_average'] ? "⭐{$item['vote_average']}/10" : '';
                $genre   = $item['genres'] ? "[{$item['genres']}]" : '';
                $age     = $item['age_rating'] ? "({$item['age_rating']})" : '';
                $line    = "- {$item['title']} {$genre} {$rating} {$age}";

                if (!empty($item['directors'])) {
                    $line .= " | Đạo diễn: {$item['directors']}";
                }
                if (!empty($item['actors'])) {
                    $line .= " | Diễn viên: {$item['actors']}";
                }
                if (!empty($item['release_year'])) {
                    $line .= " | Năm: {$item['release_year']}";
                }
                if (!empty($item['synopsis'])) {
                    $line .= "\n  Tóm tắt: {$item['synopsis']}";
                }
                if (!empty($item['url'])) {
                    $line .= "\n  Link: {$item['url']}";
                }

                $lines[] = trim($line);
            }
        }

        // Reviews
        if (!empty($context['reviews'])) {
            $lines[] = '';
            $lines[] = 'Một số review từ người dùng:';
            foreach ($context['reviews'] as $i => $rev) {
                $rating  = $rev['rating'] ? "({$rev['rating']}/10)" : '';
                $spoiler = $rev['is_spoiler'] ? '[SPOILER] ' : '';
                $lines[] = "- Review " . ($i + 1) . " {$rating} {$spoiler}: {$rev['excerpt']}";
            }
        }

        // If nothing found
        if (empty($context['items']) && empty($context['reviews'])) {
            $lines[] = '';
            $lines[] = '⚠️ Không có dữ liệu phù hợp trong RecoDB. Hãy thông báo với người dùng và hỏi thêm thông tin.';
        }

        $lines[] = '=== HẾT DỮ LIỆU ===';
        return implode("\n", $lines);
    }

    /**
     * Format a direct PHP fallback response when Gemini is bypassed (profile missing but conditioned).
     *
     * @param array  $context The context built by buildContext().
     * @param string $message The user's original message.
     * @return string
     */
    public function formatConditionedFallbackResponse(array $context, string $message): string
    {
        if (empty($context['items'])) {
            return "Mình chưa tìm thấy phim phù hợp với yêu cầu này trong RecoDB. Bạn có thể thử thể loại khác như hành động, hài, hoạt hình hoặc viễn tưởng.";
        }

        $lines = [];
        $lines[] = "Mình chưa có đủ dữ liệu để cá nhân hóa sâu theo gu của bạn, nhưng dựa trên yêu cầu bạn vừa nêu, mình gợi ý vài phim phù hợp trong RecoDB:\n";

        $count = 0;
        foreach ($context['items'] as $item) {
            if ($count >= 3) break;
            
            $title  = $item['title'];
            $genre  = $item['genres'] ?: 'Đang cập nhật';
            
            if (!empty($item['synopsis'])) {
                $sentences = preg_split('/(?<=[.?!])\s+/', strip_tags($item['synopsis']));
                $reason = trim($sentences[0]);
                if (!preg_match('/[.?!]$/', $reason)) {
                    $reason .= '.';
                }
            } else {
                $typeStr = $item['type'] === 'movie' ? 'phim lẻ' : 'phim bộ';
                $reason = "Phù hợp nếu bạn thích {$typeStr}";
                if (!empty($item['genres'])) {
                    $reason .= " thể loại " . $item['genres'];
                }
                $reason .= ".";
            }
            
            $lines[] = "🎬 {$title}";
            $lines[] = "Thể loại: {$genre}";
            $lines[] = "Lý do: {$reason}\n";
            $count++;
        }

        $lines[] = "Bạn có thể xem thêm, tìm kiếm hoặc thêm phim vào watchlist để RecoDB hiểu gu của bạn tốt hơn nhé.";

        return implode("\n", $lines);
    }

    /**
     * Format a fallback response using standard Context DB items when Gemini is truncated, fails or times out.
     *
     * @param array  $context The context built by buildContext().
     * @param string|null $intro Optional intro message.
     * @return string
     */
    public function formatContextItemsResponse(array $context, ?string $intro = null): string
    {
        if (empty($context['items'])) {
            return "Trợ lý AI đang bận. Bạn hãy dùng thanh tìm kiếm để khám phá RecoDB nhé!";
        }

        $lines = [];
        $lines[] = $intro ?? "RecoDB có một vài gợi ý bạn có thể tham khảo:\n";

        $count = 0;
        foreach ($context['items'] as $item) {
            if ($count >= 3) break;
            
            $title  = $item['title'];
            $genre  = $item['genres'] ?: 'Đang cập nhật';
            
            if (!empty($item['synopsis'])) {
                $sentences = preg_split('/(?<=[.?!])\s+/', strip_tags($item['synopsis']));
                $reason = trim($sentences[0]);
                if (!preg_match('/[.?!]$/', $reason)) {
                    $reason .= '.';
                }
            } else {
                $typeStr = $item['type'] === 'movie' ? 'Phim lẻ' : 'Phim bộ';
                $reason = "Phù hợp nếu bạn thích {$typeStr}";
                if (!empty($item['genres'])) {
                    $reason .= " thể loại " . $item['genres'];
                }
                $reason .= ".";
            }

            $typeStr = $item['type'] === 'movie' ? 'Phim lẻ' : 'Phim bộ';
            
            $lines[] = "🎬 {$title}";
            $lines[] = "Loại: {$typeStr}";
            $lines[] = "Thể loại: {$genre}";

            if (!empty($item['actors'])) {
                $lines[] = "Diễn viên: {$item['actors']}";
            } elseif (!empty($item['directors'])) {
                $lines[] = "Đạo diễn: {$item['directors']}";
            }

            $lines[] = "Lý do: {$reason}\n";
            $count++;
        }

        $lines[] = "Bạn có thể bấm vào thẻ phim bên dưới để xem chi tiết.";

        return implode("\n", $lines);
    }
}
