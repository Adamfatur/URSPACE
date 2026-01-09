<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('GEMINI_API_KEY'),

    'model' => env('GEMINI_MODEL', 'gemini-3-flash-preview'),

    'base_url' => 'https://generativelanguage.googleapis.com/v1beta',

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        'requests_per_minute' => 10,
        'cache_ttl' => 3600, // 1 hour cache for AI responses
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Processing
    |--------------------------------------------------------------------------
    */

    'processing' => [
        'min_content_length' => 50, // Minimum chars to process
        'max_content_length' => 5000, // Maximum chars to send
    ],

    /*
    |--------------------------------------------------------------------------
    | Moderation Settings
    |--------------------------------------------------------------------------
    */

    'moderation' => [
        'auto_flag_threshold' => 7, // Score >= 7 auto-flagged
        'categories' => [
            'spam',
            'hate_speech',
            'harassment',
            'violence',
            'adult_content',
            'misinformation',
        ],
    ],
];
