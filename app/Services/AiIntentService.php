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
    public const INTENT_RECOMMEND = 'movie.recommend';
    public const INTENT_SEARCH    = 'movie.search';
    public const INTENT_REVIEW    = 'movie.review';
    public const INTENT_GENRE     = 'movie.genre';
    public const INTENT_POPULAR   = 'movie.popular';
    public const INTENT_DETAIL    = 'movie.detail';
    public const INTENT_PERSON    = 'movie.person';
    public const INTENT_SITE_HELP = 'site.help';
    public const INTENT_IRRELEVANT = 'irrelevant';
    public const INTENT_UNKNOWN   = 'unknown';

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
            'tinh toan'      => 0.90,
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
        $isPersonalized = $this->isPersonalizedRequest($normalized);
        $hasExplicitCondition = $this->hasExplicitMovieCondition($normalized);

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
            ];
        }

        return [
            'intent'                 => $topIntent,
            'confidence'             => round($topConfidence, 2),
            'keywords'               => array_unique($matched[$topIntent] ?? []),
            'is_personalized'        => $isPersonalized,
            'has_explicit_condition' => $hasExplicitCondition,
            'wants_type'             => $this->detectWantsType($message),
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
            self::INTENT_SITE_HELP  => 'Hỗ trợ web',
            self::INTENT_IRRELEVANT => 'Không liên quan',
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
