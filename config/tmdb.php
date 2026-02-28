<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TMDb API Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình kết nối với The Movie Database (TMDb) API.
    | Đăng ký lấy API key tại: https://www.themoviedb.org/settings/api
    |
    */

    'api_key' => env('TMDB_API_KEY', ''),

    'base_url' => 'https://api.themoviedb.org/3',

    'image_base_url' => 'https://image.tmdb.org/t/p/',

    // Kích thước ảnh phổ biến
    'poster_sizes' => [
        'small' => 'w185',
        'medium' => 'w342',
        'large' => 'w500',
        'original' => 'original',
    ],

    'backdrop_sizes' => [
        'small' => 'w300',
        'medium' => 'w780',
        'large' => 'w1280',
        'original' => 'original',
    ],

    'profile_sizes' => [
        'small' => 'w45',
        'medium' => 'w185',
        'large' => 'h632',
        'original' => 'original',
    ],

    // Ngôn ngữ mặc định cho kết quả API
    'language' => 'vi-VN',

    // Ngôn ngữ fallback nếu không có tiếng Việt
    'fallback_language' => 'en-US',
];
