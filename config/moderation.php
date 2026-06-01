<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Moderation Settings
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho tính năng kiểm duyệt nội dung bằng AI.
    |
    */

    'ai_enabled' => env('AI_MODERATION_ENABLED', false),
    
    'ai_provider' => env('AI_MODERATION_PROVIDER', 'gemini'),
    
    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    
    'gemini_model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    
    'ai_timeout' => env('AI_MODERATION_TIMEOUT', 8),
];
