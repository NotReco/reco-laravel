<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * AiIntentService
 *
 * Rule-based intent classifier for the AI Assistant.
 * Zero external API calls – pure PHP pattern matching.
 * Supports Vietnamese with and without diacritics.
 */
class AiIntentService
{
    // -----------------------------------------------------------------------
    // Intent constants
    // -----------------------------------------------------------------------
    public const INTENT_RECOMMEND  = 'movie.recommend';
    public const INTENT_SEARCH     = 'movie.search';
    public const INTENT_REVIEW     = 'movie.review';
    public const INTENT_GENRE      = 'movie.genre';
    public const INTENT_POPULAR    = 'movie.popular';
    public const INTENT_DETAIL     = 'movie.detail';
    public const INTENT_PERSON     = 'movie.person';
    public const INTENT_MOOD       = 'movie.mood';  // phim theo tâm trạng / mood
    public const INTENT_SITE_HELP  = 'site.help';
    public const INTENT_IRRELEVANT = 'irrelevant';
    public const INTENT_SENSITIVE  = 'sensitive';   // cực đoan, chính trị nhạy cảm
    public const INTENT_ADULT_MOVIE_REQUEST = 'adult.movie_request';
    public const INTENT_ADULT_EXPLICIT_VIOLATION = 'adult.explicit_violation';
    public const INTENT_UNKNOWN    = 'unknown';
    public const INTENT_GREETING   = 'greeting';
    public const INTENT_ACK        = 'ack';         // cảm ơn / ok / được rồi
    public const INTENT_SMALLTALK  = 'smalltalk';   // câu hỏi linh tinh không liên quan phim

    /**
     * Intents that are allowed to trigger local-first movie recommendations.
     */
    public const MOVIE_INTENTS = [
        self::INTENT_RECOMMEND,
        self::INTENT_SEARCH,
        self::INTENT_GENRE,
        self::INTENT_POPULAR,
        self::INTENT_DETAIL,
        self::INTENT_PERSON,
        self::INTENT_MOOD,
        self::INTENT_ADULT_MOVIE_REQUEST,
    ];

    /**
     * Intents that ALWAYS carry an implicit recommendation intent.
     * Genre/detail intents require an explicit recommendation keyword.
     */
    private const ALWAYS_RECOMMEND_INTENTS = [
        self::INTENT_RECOMMEND,
        self::INTENT_SEARCH,
        self::INTENT_POPULAR,
        self::INTENT_MOOD,
        self::INTENT_ADULT_MOVIE_REQUEST,
    ];

    /**
     * Explicit recommendation/search keywords (normalized, no diacritics).
     * Message MUST contain at least one of these to trigger card display
     * for genre/detail/person intents.
     */
    private const RECOMMEND_INTENT_KEYWORDS = [
        'goi y',       // gợi ý
        'de xuat',     // đề xuất
        'cho toi phim',// cho tôi phim
        'cho minh phim',
        'tim phim',    // tìm phim
        'kiem phim',   // kiếm phim
        'co phim nao', // có phim nào
        'phim nao',    // phim nào
        'recommend',
        'nen xem',     // nên xem
        'muon xem phim',
        'phim giong',  // phim giống
        'co phim',     // có phim
        'phim gi hay', // phim gì hay
        'xem phim gi', // xem phim gì
        'phim hay',    // phim hay
        'phim nao hay',
        'muon xem',
        'tim kiem',
    ];

    /**
     * Return true when the given intent should trigger local DB recommendation lookup.
     * For genre/detail/person intents, also requires has_recommend_intent = true.
     */
    public static function isMovieRelatedIntent(string $intent, ?string $wantsType = null): bool
    {
        return in_array($intent, self::MOVIE_INTENTS, true) || !empty($wantsType);
    }

    /**
     * Return true if we should actually show movie cards for this classified intent.
     * Genre/topic-only intents without explicit recommend language should NOT show cards.
     */
    public static function shouldShowMovieCards(array $classifyResult): bool
    {
        $intent = $classifyResult['intent'] ?? '';
        $hasRecommendIntent = $classifyResult['has_recommend_intent'] ?? false;
        $wantsType = $classifyResult['wants_type'] ?? null;

        // Intents that always show cards (user clearly wants recommendations)
        if (in_array($intent, self::ALWAYS_RECOMMEND_INTENTS, true)) {
            return true;
        }

        // For wantsType explicitly set (phim bộ/phim lẻ), needs recommend intent
        if (!empty($wantsType)) {
            return $hasRecommendIntent;
        }

        // Genre/detail/person: only show cards if user explicitly asked for recommendations
        if (in_array($intent, [self::INTENT_GENRE, self::INTENT_DETAIL, self::INTENT_PERSON], true)) {
            return $hasRecommendIntent;
        }

        return in_array($intent, self::MOVIE_INTENTS, true) && $hasRecommendIntent;
    }

    // -----------------------------------------------------------------------
    // Keyword maps  (normalized/no-diacritic → real weight applied later)
    // Each group: [ 'normalized_keyword' => confidence_weight ]
    // -----------------------------------------------------------------------
    private const RULES = [
        self::INTENT_RECOMMEND => [
            'goi y phim bo'  => 0.95,
            'goi y phim le'  => 0.95,
            'co phim bo nao hay khong' => 0.95,
            'co phim bo nao khong' => 0.90,
            'phim bo nao hay' => 0.90,
            'phim bo hay'    => 0.85,
            'series nao hay' => 0.90,
            'tv show nao hay'=> 0.90,
            'co phim le nao hay khong' => 0.95,
            'co phim le nao khong' => 0.90,
            'phim le nao hay' => 0.90,
            'phim le hay'    => 0.85,
            'movie nao hay'  => 0.90,
            'goi y phim'     => 0.95,   // composite: beats bare genre match
            'de xuat phim'   => 0.95,   // composite: beats bare genre match
            'goi y'          => 0.90,
            'de xuat'        => 0.90,
            'nen xem'        => 0.85,
            'recommend'      => 0.90,
            'phim nao hay'   => 0.85,
            'tu van phim'    => 0.85,
            'xem gi'         => 0.80,
            'xem phim gi'    => 0.85,
            'phim hay'       => 0.75,
            'muon xem'       => 0.75,
            'nen xem phim'   => 0.85,
            'cho toi biet'   => 0.50,
        ],

        self::INTENT_SEARCH => [
            'tim phim'       => 0.95,
            'tim kiem'       => 0.85,
            'co phim'        => 0.80,
            'search'         => 0.85,
            'tim giup'       => 0.90,
            'kiem giup'      => 0.90,
            'kiem'           => 0.70,
            'ban co biet'    => 0.60,
            'muon tim'       => 0.80,
        ],

        self::INTENT_REVIEW => [
            'review'         => 0.95,
            'danh gia'       => 0.90,
            'binh luan'      => 0.85,
            'nhan xet'       => 0.85,
            'cam nhan'       => 0.80,
            'che'            => 0.70,
            'khen'           => 0.70,
            'hay khong'      => 0.65,
            'co hay khong'   => 0.70,
            'tot khong'      => 0.65,
        ],

        self::INTENT_GENRE => [
            'kinh di'        => 0.92,
            'hanh dong'      => 0.92,
            'tinh cam'       => 0.92,
            'hai huoc'       => 0.88,
            'phim hai'       => 0.85,
            'anime'          => 0.92,
            'hoat hinh'      => 0.90,
            'vien tuong'     => 0.90,
            'khoa hoc vien tuong' => 0.92,
            'tam ly'         => 0.88,
            'toi pham'       => 0.90,
            'phieu luu'      => 0.90,
            'chien tranh'    => 0.88,
            'tai lieu'       => 0.85,
            'gia dinh'       => 0.80,
            'the loai'       => 0.75,
            'phim kinh'      => 0.85,
            'phim hanh'      => 0.80,
            'phim tinh'      => 0.80,
            'romantic'       => 0.85,
            'thriller'       => 0.85,
            'horror'         => 0.88,
            'comedy'         => 0.85,
            'drama'          => 0.80,
            'fantasy'        => 0.85,
            'sci-fi'         => 0.88,
            'scifi'          => 0.88,
            'action'         => 0.85,
        ],

        self::INTENT_POPULAR => [
            'phim hot'       => 0.90,
            'noi bat'        => 0.85,
            'thinh hanh'     => 0.90,
            'pho bien'       => 0.85,
            'nhieu nguoi xem'=> 0.88,
            'top phim'       => 0.88,
            'trending'       => 0.92,
            'dang hot'       => 0.90,
            'xem nhieu nhat' => 0.85,
            'duoc yeu thich' => 0.80,
            'diem cao nhat'  => 0.80,
            'hay nhat'       => 0.70,
            'hang dau'       => 0.80,
        ],

        self::INTENT_DETAIL => [
            'noi dung phim'  => 0.90,
            'dien vien'      => 0.88,
            'dao dien'       => 0.88,
            'nam phat hanh'  => 0.90,
            'thoi luong'     => 0.88,
            'phim nay noi ve' => 0.92,
            'phim noi ve'    => 0.85,
            'phim ke ve'     => 0.85,
            'chi tiet'       => 0.80,
            'thong tin phim' => 0.88,
            'cast'           => 0.80,
            'plot'           => 0.80,
            'synopsis'       => 0.85,
        ],

        self::INTENT_PERSON => [
            'phim cua'       => 0.95,
            'phim do'        => 0.85,
            'dao dien boi'   => 0.95,
            'dien vien'      => 0.80,
            'dao dien'       => 0.80,
            'dong phim gi'   => 0.95,
            'co phim nao cua'=> 0.98,
            'actor'          => 0.80,
            'director'       => 0.80,
            'cast'           => 0.80,
            'ai dong'        => 0.95,
            'phim co'        => 0.85,
        ],

        self::INTENT_SITE_HELP => [
            'web nay co gi'  => 0.98,
            'recodb co gi'   => 0.98,
            'trang nay co gi' => 0.98,
            'website nay dung lam gi' => 0.98,
            'trang nay dung lam gi' => 0.98,
            'tinh nang cua web' => 0.98,
            'chuc nang cua web' => 0.98,
            'toi lam duoc gi o day' => 0.98,
            'dung web'       => 0.90,
            'web nay'        => 0.85,
            'dung sao'       => 0.85,
            'dung the nao'   => 0.88,
            'tai khoan'      => 0.85,
            'dang nhap'      => 0.88,
            'dang ky'        => 0.85,
            'yeu thich'      => 0.75,
            'watchlist'      => 0.90,
            'danh sach xem'  => 0.85,
            'danh sach yeu thich' => 0.85,
            'cach viet review'=> 0.98,
            'viet review sao' => 0.98,
            'review hay'     => 0.98,
            'huong dan review'=> 0.98,
            'lam sao danh gia' => 0.98,
            'cach danh gia'  => 0.98,
            'viet review'    => 0.88,
            'tim kiem tren web'   => 0.85,
            'huong dan'      => 0.80,
            'cach dung'      => 0.85,
            'cach su dung'   => 0.85,
            'recodb'         => 0.75,
            'trang web'      => 0.70,
            'website'        => 0.70,
        ],

        self::INTENT_IRRELEVANT => [
            // Mathematics / programming
            'giai phuong trinh' => 0.98,
            'giai toan'         => 0.98,
            'tinh toan giup'    => 0.95,
            'tinh toan'         => 0.90,
            'viet code'      => 0.98,
            'lap trinh'      => 0.95,
            'debug'          => 0.90,
            'javascript'     => 0.88,
            'python'         => 0.85,
            'html'           => 0.85,
            // Weather / Finance
            'thoi tiet'      => 0.98,
            'nhiet do'       => 0.90,
            'crypto'         => 0.98,
            'bitcoin'        => 0.98,
            'coin'           => 0.85,
            'gia vang'       => 0.95,
            'chung khoan'    => 0.95,
            // Sports / Politics
            'bong da'        => 0.95,
            'bong ro'        => 0.95,
            'chinh tri'      => 0.95,
            'chung cu'       => 0.90,
            // Shopping / Daily life
            'mua dien thoai' => 0.95,
            'mua xe'         => 0.90,
            'sua xe'         => 0.90,
            'nha hang'       => 0.88,
            'recipe'         => 0.88,
            'cong thuc nau an' => 0.90,
            'du lich'        => 0.82,  // could be travel movies, lower confidence
            'visa'           => 0.85,
            'khach san'      => 0.85,
            // Medical
            'benh'           => 0.80,
            'thuoc'          => 0.85,
            'bac si'         => 0.85,
        ],

        // ── Smalltalk: personal / off-topic questions ──────────────────────
        self::INTENT_SMALLTALK => [
            'dep trai khong'    => 0.99,
            'toi dep trai'      => 0.99,
            'ban co yeu toi'    => 0.99,
            'yeu toi khong'     => 0.99,
            'hom nay toi buon'  => 0.99,
            'toi buon qua'      => 0.99,
            'buon qua'          => 0.95,
            'toi chan qua'      => 0.99,
            'chan qua'          => 0.95,
            'ke chuyen cuoi'    => 0.99,
            'chuyen cuoi'       => 0.95,
            'ban la ai'         => 0.95,
            'ban lam duoc gi'   => 0.95,
        ],
    ];

    // -----------------------------------------------------------------------
    // Diacritic normalization map
    // -----------------------------------------------------------------------
    private const DIACRITICS = [
        // a
        'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
        'â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
        // e
        'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
        'ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        // i
        'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
        // o
        'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
        'ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
        'ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        // u
        'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
        'ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        // y
        'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        // d
        'đ'=>'d',
        // uppercase (cover edge cases)
        'À'=>'a','Á'=>'a','Ả'=>'a','Ã'=>'a','Ạ'=>'a',
        'Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ẩ'=>'a','Ẫ'=>'a','Ậ'=>'a',
        'Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ẳ'=>'a','Ẵ'=>'a','Ặ'=>'a',
        'È'=>'e','É'=>'e','Ẻ'=>'e','Ẽ'=>'e','Ẹ'=>'e',
        'Ê'=>'e','Ề'=>'e','Ế'=>'e','Ể'=>'e','Ễ'=>'e','Ệ'=>'e',
        'Ì'=>'i','Í'=>'i','Ỉ'=>'i','Ĩ'=>'i','Ị'=>'i',
        'Ò'=>'o','Ó'=>'o','Ỏ'=>'o','Õ'=>'o','Ọ'=>'o',
        'Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ổ'=>'o','Ỗ'=>'o','Ộ'=>'o',
        'Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ở'=>'o','Ỡ'=>'o','Ợ'=>'o',
        'Ù'=>'u','Ú'=>'u','Ủ'=>'u','Ũ'=>'u','Ụ'=>'u',
        'Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ử'=>'u','Ữ'=>'u','Ự'=>'u',
        'Ỳ'=>'y','Ý'=>'y','Ỷ'=>'y','Ỹ'=>'y','Ỵ'=>'y',
        'Đ'=>'d',
    ];

    // -----------------------------------------------------------------------
    // Public API
    // -----------------------------------------------------------------------

    /**
     * Classify the user's message into one of the supported intents.
     *
     * @return array{intent: string, confidence: float, keywords: array<string>}
     */
    public function classify(string $message): array
    {
        $normalized = $this->normalize($message);

        // ── Fast-path: Explicit Adult Violation ────────
        $explicitKeywords = ['phim sex', 'gui phim sex', 'xxx', 'khieu dam', 'chat sex', 'mo ta canh sex', 'nude', 'thu dam', 'clip nong'];
        foreach ($explicitKeywords as $kw) {
            if (str_contains($normalized, $kw)) {
                return [
                    'intent'                 => self::INTENT_ADULT_EXPLICIT_VIOLATION,
                    'confidence'             => 1.0,
                    'keywords'               => [$kw],
                    'is_personalized'        => false,
                    'has_explicit_condition' => false,
                    'has_recommend_intent'   => false,
                    'wants_type'             => null,
                    'mood'                   => null,
                ];
            }
        }

        // ── Fast-path: Adult Movie Request ────────
        $adultRequestKeywords = ['toi muon phim co lien quan den quan he nam nu 18+', 'goi y phim 18+', 'phim 18+', 'phim nguoi lon', 'phim tinh cam truong thanh', 'phim tam ly tinh cam', 'phim danh cho nguoi truong thanh'];
        foreach ($adultRequestKeywords as $kw) {
            if (str_contains($normalized, $kw)) {
                return [
                    'intent'                 => self::INTENT_ADULT_MOVIE_REQUEST,
                    'confidence'             => 1.0,
                    'keywords'               => [$kw],
                    'is_personalized'        => false,
                    'has_explicit_condition' => false,
                    'has_recommend_intent'   => true,
                    'wants_type'             => null,
                    'mood'                   => null,
                ];
            }
        }

        // ── Fast-path: Sensitive (chính trị cực đoan, phân biệt chủng tộc) ────────
        // Nếu chứa từ khóa nhạy cảm NHƯNG không nhắc tới "phim", "movie", "review"... thì chặn ngay.
        // Nếu có chữ "phim", ta cho qua để search phim lịch sử.
        $sensitiveKeywords = ['hitler', 'phat xit', 'nazi', 'quoc xa', 'diet chung', 'cuc doan'];
        $isSensitive = false;
        foreach ($sensitiveKeywords as $kw) {
            if (str_contains($normalized, $kw)) {
                $isSensitive = true;
                break;
            }
        }

        if ($isSensitive) {
            $movieSignals = ['phim', 'goi y', 'de xuat', 'movie', 'review', 'the loai'];
            $isAskingAboutMovie = false;
            foreach ($movieSignals as $signal) {
                if (str_contains($normalized, $signal)) {
                    $isAskingAboutMovie = true;
                    break;
                }
            }

            if (!$isAskingAboutMovie) {
                return [
                    'intent'                 => self::INTENT_SENSITIVE,
                    'confidence'             => 1.0,
                    'keywords'               => [],
                    'is_personalized'        => false,
                    'has_explicit_condition' => false,
                    'has_recommend_intent'   => false,
                    'wants_type'             => null,
                    'mood'                   => null,
                ];
            }
        }

        $isPersonalized = $this->isPersonalizedRequest($normalized);
        $hasExplicitCondition = $this->hasExplicitMovieCondition($normalized);

        // ── Fast-path: greeting (rule-based, before keyword loop) ────────────
        if ($this->isGreeting($normalized)) {
            return [
                'intent'                 => self::INTENT_GREETING,
                'confidence'             => 1.0,
                'keywords'               => [],
                'is_personalized'        => false,
                'has_explicit_condition' => false,
                'has_recommend_intent'   => false,
                'wants_type'             => null,
                'mood'                   => null,
            ];
        }

        // ── Fast-path: acknowledgement (rule-based) ──────────────────────────
        if ($this->isAck($normalized)) {
            return [
                'intent'                 => self::INTENT_ACK,
                'confidence'             => 1.0,
                'keywords'               => [],
                'is_personalized'        => false,
                'has_explicit_condition' => false,
                'has_recommend_intent'   => false,
                'wants_type'             => null,
                'mood'                   => null,
            ];
        }

        // ── Fast-path: movie mood (phim + mood keyword) ─────────────────────
        $moodResult = $this->detectMoodIntent($normalized);
        if ($moodResult !== null) {
            return [
                'intent'                 => self::INTENT_MOOD,
                'confidence'             => 0.95,
                'keywords'               => [$moodResult['matched_keyword']],
                'is_personalized'        => $isPersonalized,
                'has_explicit_condition' => true,
                'has_recommend_intent'   => true, // mood intent already requires a movie signal
                'wants_type'             => $this->detectWantsType($message),
                'mood'                   => $moodResult['mood'],
            ];
        }

        $scores   = [];   // intent → best confidence found
        $matched  = [];   // intent → matched keyword list

        foreach (self::RULES as $intent => $patterns) {
            foreach ($patterns as $keyword => $weight) {
                if (str_contains($normalized, $keyword)) {
                    $scores[$intent]  = max($scores[$intent] ?? 0.0, $weight);
                    $matched[$intent][] = $keyword;
                }
            }
        }

        // No match at all → unknown
        if (empty($scores)) {
            return [
                'intent'                 => self::INTENT_UNKNOWN,
                'confidence'             => 0.0,
                'keywords'               => [],
                'is_personalized'        => $isPersonalized,
                'has_explicit_condition' => $hasExplicitCondition,
                'has_recommend_intent'   => false,
                'wants_type'             => null,
                'mood'                   => null,
            ];
        }

        // smalltalk wins over everything except irrelevant
        if (isset($scores[self::INTENT_SMALLTALK]) && !isset($scores[self::INTENT_IRRELEVANT])) {
            return [
                'intent'                 => self::INTENT_SMALLTALK,
                'confidence'             => $scores[self::INTENT_SMALLTALK],
                'keywords'               => array_unique($matched[self::INTENT_SMALLTALK] ?? []),
                'is_personalized'        => false,
                'has_explicit_condition' => false,
                'has_recommend_intent'   => false,
                'wants_type'             => null,
                'mood'                   => null,
            ];
        }

        // irrelevant always wins if detected (safety gate)
        if (isset($scores[self::INTENT_IRRELEVANT])) {
            return [
                'intent'                 => self::INTENT_IRRELEVANT,
                'confidence'             => $scores[self::INTENT_IRRELEVANT],
                'keywords'               => array_unique($matched[self::INTENT_IRRELEVANT] ?? []),
                'is_personalized'        => $isPersonalized,
                'has_explicit_condition' => $hasExplicitCondition,
                'has_recommend_intent'   => false,
                'wants_type'             => null,
                'mood'                   => null,
            ];
        }

        // Pick the intent with the highest confidence
        arsort($scores);
        $topIntent     = array_key_first($scores);
        $topConfidence = $scores[$topIntent];

        // If best confidence is too low (< 0.40) → unknown
        if ($topConfidence < 0.40) {
            return [
                'intent'                 => self::INTENT_UNKNOWN,
                'confidence'             => $topConfidence,
                'keywords'               => [],
                'is_personalized'        => $isPersonalized,
                'has_explicit_condition' => $hasExplicitCondition,
                'wants_type'             => null,
                'mood'                   => null,
            ];
        }

        return [
            'intent'                 => $topIntent,
            'confidence'             => round($topConfidence, 2),
            'keywords'               => array_unique($matched[$topIntent] ?? []),
            'is_personalized'        => $isPersonalized,
            'has_explicit_condition' => $hasExplicitCondition,
            'has_recommend_intent'   => $this->hasRecommendIntent($normalized),
            'wants_type'             => $this->detectWantsType($message),
            'mood'                   => null,
        ];
    }

    /**
     * Returns a human-readable label for display / logging.
     */
    public function label(string $intent): string
    {
        return match ($intent) {
            self::INTENT_RECOMMEND  => 'Gợi ý phim',
            self::INTENT_SEARCH     => 'Tìm phim',
            self::INTENT_REVIEW     => 'Xem đánh giá',
            self::INTENT_GENRE      => 'Theo thể loại',
            self::INTENT_POPULAR    => 'Phim nổi bật',
            self::INTENT_DETAIL     => 'Chi tiết phim',
            self::INTENT_PERSON     => 'Phim của người',
            self::INTENT_MOOD       => 'Phim theo tâm trạng',
            self::INTENT_SITE_HELP  => 'Hỗ trợ web',
            self::INTENT_IRRELEVANT => 'Không liên quan',
            self::INTENT_SENSITIVE  => 'Chủ đề nhạy cảm',
            self::INTENT_ADULT_MOVIE_REQUEST => 'Phim 18+',
            self::INTENT_ADULT_EXPLICIT_VIOLATION => 'Vi phạm nội dung',
            self::INTENT_GREETING   => 'Chào hỏi',
            self::INTENT_ACK        => 'Xác nhận',
            self::INTENT_SMALLTALK  => 'Ngoài chủ đề',
            default                 => 'Không xác định',
        };
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    /**
     * Detect if the user is asking for personalized recommendations.
     */
    private function isPersonalizedRequest(string $normalized): bool
    {
        $patterns = [
            'hop gu cua toi',
            'gu cua toi',
            'theo so thich cua toi',
            'phu hop voi toi',
            'dua tren lich su cua toi',
            'phim nao hop voi toi',
            'recommend theo gu toi',
            'hop gu toi',
            'hop voi toi',
            'danh rieng cho toi'
        ];

        foreach ($patterns as $p) {
            if (str_contains($normalized, $p)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Detect if the user explicitly requested movie recommendations/search.
     * This separates "phim gay" (topic mention) from "gợi ý phim gay" (recommend intent).
     */
    private function hasRecommendIntent(string $normalized): bool
    {
        foreach (self::RECOMMEND_INTENT_KEYWORDS as $kw) {
            if (str_contains($normalized, $kw)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Detect if the user specified an explicit genre or mood condition.
     */
    private function hasExplicitMovieCondition(string $normalized): bool
    {
        $conditions = [
            'kinh di', 'hanh dong', 'hai huoc', 'phim hai', 'hoat hinh', 
            'anime', 'tinh cam', 'vien tuong', 'nhe nhang', 'kich tinh', 
            'giai tri', 'gia dinh', 'tam ly', 'toi pham', 'phieu luu', 
            'chien tranh', 'tai lieu', 'kinh', 'hanh', 'tinh', 'romantic', 
            'thriller', 'horror', 'comedy', 'drama', 'fantasy', 'sci-fi', 'action'
        ];

        foreach ($conditions as $c) {
            if (str_contains($normalized, $c)) {
                return true;
            }
        }
        return false;
    }

    // -----------------------------------------------------------------------
    // Greeting / Ack fast-path helpers
    // -----------------------------------------------------------------------

    /**
     * True if the message is purely a greeting with no movie signal.
     * Checks the normalized (no-diacritic, lowercase) version.
     */
    private function isGreeting(string $normalized): bool
    {
        // Exact-match greetings (short messages only)
        $exactGreetings = [
            'xin chao', 'chao', 'hello', 'hi', 'alo', 'hey',
            'ban oi', 'e', 'ey', 'yo',
        ];

        $trimmed = trim($normalized);

        // Exact match first (highest precision)
        if (in_array($trimmed, $exactGreetings, true)) {
            return true;
        }

        // Starts-with greeting but message is short (≤ 30 chars) and has no movie keyword
        $greetingPrefixes = ['xin chao', 'chao ban', 'chao bot', 'hello ban', 'hi ban', 'hey ban'];
        foreach ($greetingPrefixes as $prefix) {
            if (str_starts_with($trimmed, $prefix) && mb_strlen($trimmed) <= 30) {
                return true;
            }
        }

        return false;
    }

    /**
     * True if the message is purely an acknowledgement (cảm ơn / ok / được rồi).
     */
    private function isAck(string $normalized): bool
    {
        $exactAcks = [
            'cam on', 'cam on ban', 'thanks', 'thank you', 'ok', 'oke', 'okay',
            'duoc roi', 'hay qua', 'tot', 'ngon', 'biet roi', 'ro roi',
        ];

        $trimmed = trim($normalized);
        return in_array($trimmed, $exactAcks, true);
    }

    // -----------------------------------------------------------------------
    // Mood detection (movie + mood keyword)
    // -----------------------------------------------------------------------

    /**
     * Detect if the user is asking for movies by mood.
     * Only matches when message contains a movie signal ('phim', 'goi y', 'de xuat')
     * AND a mood keyword.
     *
     * "có phim nào buồn buồn không" → match (has 'phim' + 'buon')
     * "gợi ý phim buồn"             → match (has 'goi y' + 'phim' + 'buon')
     * "tôi buồn, gợi ý phim cho tôi"→ match (has 'goi y' + 'phim')
     * "tôi buồn quá"                → NO match (no 'phim'/'goi y')
     *
     * @return null|array{mood: string, matched_keyword: string}
     */
    private function detectMoodIntent(string $normalized): ?array
    {
        // Must have a movie signal — prevents "tôi buồn quá" from matching
        $movieSignals = ['phim', 'goi y', 'de xuat', 'recommend'];
        $hasMovieSignal = false;
        foreach ($movieSignals as $signal) {
            if (str_contains($normalized, $signal)) {
                $hasMovieSignal = true;
                break;
            }
        }

        if (!$hasMovieSignal) {
            return null;
        }

        // Mood keywords → mood key (longer patterns first for greedy match)
        $moodKeywords = [
            'buon buon'   => 'buon',
            'cam dong'    => 'cam_dong',
            'nhe nhang'   => 'nhe_nhang',
            'chua lanh'   => 'chua_lanh',
            'am ap'       => 'am_ap',
            'vui ve'      => 'vui',
            'kich tinh'   => 'kich_tinh',
            'cang nao'    => 'cang_nao',
            'phieu luu'   => 'phieu_luu',
            'hao hung'    => 'hao_hung',
            'tinh cam'    => 'tinh_cam',
            'lang man'    => 'tinh_cam',
            'giai toa'    => 'giai_toa',
            'buon'        => 'buon',
            'khoc'        => 'khoc',
            'chill'       => 'chill',
            'vui'         => 'vui',
            'hai'         => 'hai',
        ];

        foreach ($moodKeywords as $keyword => $mood) {
            if (str_contains($normalized, $keyword)) {
                return ['mood' => $mood, 'matched_keyword' => $keyword];
            }
        }

        return null;
    }

    /**
     * Extra heuristic for wants_type (movie vs tv)
     */
    private function detectWantsType(string $message): ?string
    {
        $normalized = Str::ascii(Str::lower($message));
        
        $tvPatterns = ['phim bo', 'series', 'tv show', 'truyen hinh', 'nhieu tap'];
        foreach ($tvPatterns as $pat) {
            if (str_contains($normalized, $pat)) {
                return 'tv';
            }
        }

        $moviePatterns = ['phim le', 'movie', 'chieu rap', 'phim rap', 'phim don'];
        foreach ($moviePatterns as $pat) {
            if (str_contains($normalized, $pat)) {
                return 'movie';
            }
        }

        return null;
    }

    /**
     * Lowercase + trim + strip Vietnamese diacritics → plain ASCII-ish string.
     */
    private function normalize(string $text): string
    {
        // Lowercase first (handles ASCII quickly)
        $text = mb_strtolower(trim($text), 'UTF-8');

        // Replace diacritics character by character
        $text = strtr($text, self::DIACRITICS);

        // Collapse multiple spaces
        $text = preg_replace('/\s+/', ' ', $text);

        return $text;
    }
}
