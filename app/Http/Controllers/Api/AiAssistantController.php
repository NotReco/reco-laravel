<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use App\Services\AiIntentService;
use App\Services\AiContextService;
use App\Services\UserTasteProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AiAssistantController extends Controller
{
    protected AiAssistantService $aiService;
    protected AiIntentService $intentService;
    protected UserTasteProfileService $profileService;
    protected AiContextService $contextService;

    public function __construct(AiAssistantService $aiService, AiIntentService $intentService, UserTasteProfileService $profileService, AiContextService $contextService)
    {
        $this->aiService      = $aiService;
        $this->intentService  = $intentService;
        $this->profileService = $profileService;
        $this->contextService = $contextService;
    }

    /**
     * Handle incoming chat requests for AI Assistant.
     *
     * Flow:
     *  1. Validate input
     *  2. Identify caller (user/guest) for rate-limit keys
     *  3. Cooldown check  (Phase 1)
     *  4. Daily limit check  (Phase 1)
     *  5. Intent classification  (Phase 2)
     *  6. Block irrelevant queries  (Phase 2)
     *  7. Rule-based site/review help (Phase 2)
     *  8. Call AI service with intent + user for DB-aware context  (Phase 3)
     *  9. Merge debug metadata into response
     *
     * IMPORTANT UX RULE: suggested_items must always belong to the same response
     * as the message text. Fallback/rate-limit/error responses MUST return
     * suggested_items = [] to prevent stale cards from showing under wrong text.
     */
    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'recent_suggested_items' => 'nullable|array|max:20',
            'recent_suggested_items.*.id' => 'required|integer',
            'recent_suggested_items.*.type' => 'required|string|in:movie,tv',
        ]);

        $message     = $validated['message'];
        $recentItems = $validated['recent_suggested_items'] ?? [];
        $user        = $request->user();   // null for guests

        // ── Identify the caller ──────────────────────────────────────────────
        $isLoggedIn    = $user !== null;
        $cacheIdentity = $isLoggedIn ? "user:{$user->id}" : "ip:{$request->ip()}";

        $dailyLimit = $isLoggedIn
            ? config('ai_assistant.daily_limit_user', 50)
            : config('ai_assistant.daily_limit_guest', 20);

        // ── 1. Intent classification ─────────────────────────────────────────
        $intent = $this->intentService->classify($message);

        // Block irrelevant queries – no Gemini call, no daily usage consumed
        if ($intent['intent'] === AiIntentService::INTENT_IRRELEVANT) {
            return response()->json([
                'message'                   => 'Mình là trợ lý phim của RecoDB nên chỉ hỗ trợ tìm phim, review và gợi ý nội dung trong hệ thống nhé. 🎬',
                'source'                    => 'system',
                'fallback'                  => true,
                'fallback_reason'           => 'irrelevant',
                'called_gemini'             => false,
                'intent'                    => $intent['intent'],
                'intent_confidence'         => $intent['confidence'],
                'context_items_count'       => 0,
                'suggested_items_count'     => 0,
                'has_user_profile'          => false,
                'user_profile_genres_count' => 0,
                'suggested_items'           => [],
            ], 200);
        }

        // ── Rule-based answer for site help / review help ────────────────────
        // These are answered locally – no cooldown consumed, no Gemini called.
        if ($intent['intent'] === AiIntentService::INTENT_SITE_HELP) {
            $reviewHelpKeys = ['cach viet review', 'viet review sao', 'review hay', 'huong dan review', 'lam sao danh gia', 'cach danh gia', 'viet review'];
            $isReviewHelp   = count(array_intersect($intent['keywords'], $reviewHelpKeys)) > 0;

            if ($isReviewHelp) {
                $msg    = "Để viết review hay trên RecoDB, bạn có thể:\n1. Nói ngắn gọn cảm nhận chung.\n2. Nhắc điểm mạnh/yếu như nội dung, diễn xuất, hình ảnh, âm thanh.\n3. Tránh spoil chi tiết quan trọng.\n4. Chấm điểm công bằng theo trải nghiệm của bạn.\n\nBạn có thể vào trang phim và chọn phần Viết review để gửi đánh giá.";
                $source = 'rule_based_review_help';
            } else {
                $msg    = "RecoDB là nền tảng khám phá phim lẻ và phim bộ. Bạn có thể tìm kiếm phim, xem thông tin chi tiết, đọc review cộng đồng, viết đánh giá, thêm phim vào yêu thích/watchlist và nhận gợi ý cá nhân hóa dựa trên lịch sử tương tác của bạn. Bạn cũng có thể dùng Trợ lý RecoDB để hỏi nhanh về phim, thể loại, review hoặc phim đang nổi bật.";
                $source = 'rule_based_site_help';
            }

            return response()->json([
                'message'                   => $msg,
                'source'                    => $source,
                'fallback'                  => false,
                'fallback_reason'           => null,
                'called_gemini'             => false,
                'used_local_formatter'      => false,
                'intent'                    => $intent['intent'],
                'intent_confidence'         => $intent['confidence'],
                'context_items_count'       => 0,
                'suggested_items_count'     => 0,
                'has_user_profile'          => false,
                'user_profile_genres_count' => 0,
                'suggested_items'           => [],
            ], 200);
        }

        // ── 2. Cooldown check ────────────────────────────────────────────────
        $intentName      = $intent['intent'] ?? 'unknown';
        $wantsType       = $intent['wants_type'] ?? null;
        $isListingIntent = in_array($intentName, ['movie.recommend', 'movie.popular', 'movie.genre', 'movie.person']) || !empty($wantsType);

        if ($isListingIntent) {
            $cooldown = (int) config('ai_assistant.local_cooldown_seconds', 3);
            $cooldownKey = "ai_assistant:local_cooldown:{$cacheIdentity}";
        } else {
            $cooldown = (int) config('ai_assistant.cooldown_seconds', 10);
            $cooldownKey = "ai_assistant:cooldown:{$cacheIdentity}";
        }

        if (Cache::has($cooldownKey)) {
            return response()->json([
                'message'                   => 'Bạn hỏi hơi nhanh rồi 😅 Vui lòng đợi vài giây rồi thử lại nhé.',
                'source'                    => 'system',
                'fallback'                  => true,
                'fallback_reason'           => AiAssistantService::FALLBACK_RATE_LIMITED,
                'called_gemini'             => false,
                'intent'                    => $intent['intent'],
                'intent_confidence'         => $intent['confidence'],
                'context_items_count'       => 0,
                'suggested_items_count'     => 0,
                'has_user_profile'          => false,
                'user_profile_genres_count' => 0,
                'suggested_items'           => [],
            ], 429);
        }

        // ── 3. Daily limit check ─────────────────────────────────────────────
        $dailyKey  = 'ai_assistant:daily:' . now()->toDateString() . ":{$cacheIdentity}";
        $usedToday = (int) Cache::get($dailyKey, 0);

        if ($usedToday >= $dailyLimit) {
            return response()->json([
                'message'                   => 'Hôm nay bạn đã dùng hết lượt trò chuyện AI. Bạn có thể quay lại sau nhé. 🌙',
                'source'                    => 'system',
                'fallback'                  => true,
                'fallback_reason'           => AiAssistantService::FALLBACK_RATE_LIMITED,
                'called_gemini'             => false,
                'intent'                    => $intent['intent'],
                'intent_confidence'         => $intent['confidence'],
                'context_items_count'       => 0,
                'suggested_items_count'     => 0,
                'has_user_profile'          => false,
                'user_profile_genres_count' => 0,
                'suggested_items'           => [],
            ], 429);
        }

        // ── 4. Set cooldown ──────────────────────────────────────────────────
        Cache::put($cooldownKey, true, $cooldown);

        // ── 5. Increment daily counter (TTL = rest of today + 1-hour buffer) ─
        $ttlSeconds = now()->secondsUntilEndOfDay() + 3600;
        Cache::put($dailyKey, $usedToday + 1, $ttlSeconds);

        // ── 5.5. Build User Taste Profile ────────────────────────────────────
        $userProfile = $this->profileService->buildForUser($user);

        // ── 6. Check missing profile for personalized request ────────────────
        if (!empty($intent['is_personalized']) && empty($userProfile['available'])) {
            if (empty($intent['has_explicit_condition'])) {
                $fallbackMsg = $isLoggedIn
                    ? 'Mình chưa có đủ dữ liệu về gu xem của bạn. Bạn hãy xem thêm phim, tìm kiếm, thêm phim vào watchlist/yêu thích hoặc viết đánh giá để RecoDB cá nhân hóa gợi ý chính xác hơn nhé.'
                    : 'Mình chưa có đủ dữ liệu về gu xem của bạn. Bạn hãy đăng nhập và tương tác thêm như xem phim, tìm kiếm, thêm phim vào watchlist/yêu thích hoặc viết đánh giá để RecoDB cá nhân hóa gợi ý chính xác hơn nhé.';

                return response()->json([
                    'message'                   => $fallbackMsg,
                    'source'                    => 'system',
                    'fallback'                  => true,
                    'fallback_reason'           => 'missing_user_profile',
                    'called_gemini'             => false,
                    'intent'                    => $intent['intent'],
                    'intent_confidence'         => $intent['confidence'],
                    'context_items_count'       => 0,
                    'suggested_items_count'     => 0,
                    'has_user_profile'          => false,
                    'user_profile_genres_count' => 0,
                    'is_personalized'           => true,
                    'has_explicit_condition'    => false,
                    'suggested_items'           => [],
                ], 200);
            }

            // Missing profile but HAS explicit condition (e.g. "phim kinh di hop gu")
            $intent['profile_missing_but_conditioned'] = true;

            // Bypass Gemini: generate conditioned fallback directly via PHP
            $dbContext   = $this->contextService->buildContext($message, $intent['intent'], $intent['keywords'], $user, $userProfile, $recentItems, $intent['wants_type'] ?? null);
            $fallbackMsg = $this->contextService->formatConditionedFallbackResponse($dbContext, $message);
            $displayItems = array_slice($dbContext['items'] ?? [], 0, 3);

            return response()->json([
                'message'                         => $fallbackMsg,
                'source'                          => 'fallback_conditioned',
                'fallback'                        => false, // It's technically a local recommendation now
                'fallback_reason'                 => null,
                'called_gemini'                   => false,
                'intent'                          => $intent['intent'],
                'intent_confidence'               => $intent['confidence'],
                'context_items_count'             => $dbContext['raw_count'] ?? 0,
                'suggested_items_count'           => count($displayItems),
                'has_user_profile'                => false,
                'user_profile_genres_count'       => 0,
                'is_personalized'                 => true,
                'has_explicit_condition'          => true,
                'profile_missing_but_conditioned' => true,
                'suggested_items'                 => $displayItems,
            ], 200);
        }

        // ── 7. Call AI service with intent + user context (Phase 3) ──────────
        $response = $this->aiService->ask($message, $intent, $user, $userProfile, $recentItems);

        // ── 8. Merge intent + debug metadata ─────────────────────────────────
        $response['intent']            = $intent['intent'];
        $response['intent_confidence'] = $intent['confidence'];
        $response['is_personalized']   = !empty($intent['is_personalized']);
        $response['has_explicit_condition'] = !empty($intent['has_explicit_condition']);
        $response['profile_missing_but_conditioned'] = !empty($intent['profile_missing_but_conditioned']);
        // context_items_count and suggested_items are already set by aiService

        return response()->json($response, 200);
    }
}
