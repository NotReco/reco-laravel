<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Assistant Settings
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho tính năng AI Assistant của RecoDB.
    |
    */

    'enabled' => filter_var(env('AI_ASSISTANT_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'api_key' => env('GEMINI_API_KEY', ''),

    'model' => env('AI_ASSISTANT_MODEL', 'gemini-2.5-flash'),

    'timeout' => (int) env('AI_ASSISTANT_TIMEOUT', 12),

    'max_output_tokens' => (int) env('AI_ASSISTANT_MAX_OUTPUT_TOKENS', 600),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting / Cooldown
    |--------------------------------------------------------------------------
    | Cooldown (giây) giữa 2 lần gửi liên tiếp của cùng 1 user/IP.
    */

    'cooldown_seconds' => (int) env('AI_ASSISTANT_COOLDOWN_SECONDS', 10),
    'local_cooldown_seconds' => (int) env('AI_ASSISTANT_LOCAL_COOLDOWN_SECONDS', 3),

    /*
    |--------------------------------------------------------------------------
    | Daily Limits
    |--------------------------------------------------------------------------
    | Số câu hỏi tối đa được phép mỗi ngày.
    */

    'daily_limit_guest' => (int) env('AI_ASSISTANT_DAILY_LIMIT_GUEST', 20),

    'daily_limit_user'  => (int) env('AI_ASSISTANT_DAILY_LIMIT_USER', 50),

    /*
    |--------------------------------------------------------------------------
    | Context Limits (Phase 3)
    |--------------------------------------------------------------------------
    | Giới hạn số lượng item và excerpt gửi vào Gemini để tránh token quá lớn.
    */

    'max_context_items'      => (int) env('AI_ASSISTANT_MAX_CONTEXT_ITEMS', 8),
    'max_review_excerpts'    => (int) env('AI_ASSISTANT_MAX_REVIEW_EXCERPTS', 5),
    'review_excerpt_length'  => (int) env('AI_ASSISTANT_REVIEW_EXCERPT_LENGTH', 150),
    'synopsis_length'        => (int) env('AI_ASSISTANT_SYNOPSIS_LENGTH', 180),

    /*
    |--------------------------------------------------------------------------
    | User Taste Profile (Phase 3B)
    |--------------------------------------------------------------------------
    | Giới hạn dữ liệu thu thập để xây dựng User Taste Profile.
    */

    'taste_profile_days'    => (int) env('AI_ASSISTANT_TASTE_PROFILE_DAYS', 60),
    'max_profile_genres'    => (int) env('AI_ASSISTANT_MAX_PROFILE_GENRES', 5),
    'max_profile_titles'    => (int) env('AI_ASSISTANT_MAX_PROFILE_TITLES', 5),
    'max_profile_keywords'  => (int) env('AI_ASSISTANT_MAX_PROFILE_KEYWORDS', 5),
];
