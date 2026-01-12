<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SEO Settings
    |--------------------------------------------------------------------------
    */

    'site_name' => env('APP_NAME', 'Forum UR'),
    'separator' => ' - ',

    'defaults' => [
        'title' => 'Forum UR - Wadah Diskusi Civitas Akademika Universitas Raharja',
        'description' => 'Forum diskusi resmi Universitas Raharja. Tempat berbagi pengalaman, berdiskusi, dan berkolaborasi bagi mahasiswa aktif dan alumni.',
        'keywords' => 'forum raharja, universitas raharja, mahasiswa raharja, diskusi kampus, alumni raharja, kampus tangerang',
        'author' => 'Universitas Raharja',
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph & Social Media
    |--------------------------------------------------------------------------
    */

    'og' => [
        'type' => 'website',
        'locale' => 'id_ID',
        'image' => '/images/og-default.png',
    ],

    'twitter' => [
        'card' => 'summary_large_image',
        'site' => '@UnivRaharja',
    ],

    /*
    |--------------------------------------------------------------------------
    | Structured Data (JSON-LD)
    |--------------------------------------------------------------------------
    */

    'organization' => [
        'name' => 'Universitas Raharja',
        'url' => 'https://raharja.ac.id',
        'logo' => 'https://raharja.ac.id/logo.png',
        'sameAs' => [
            'https://facebook.com/universitasraharja',
            'https://instagram.com/universitasraharja',
            'https://twitter.com/UnivRaharja',
            'https://youtube.com/universitasraharja',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sitemap Settings
    |--------------------------------------------------------------------------
    */

    'sitemap' => [
        'changefreq' => [
            'home' => 'daily',
            'threads' => 'weekly',
            'spaces' => 'weekly',
            'profiles' => 'monthly',
            'pages' => 'monthly',
        ],
        'priority' => [
            'home' => 1.0,
            'threads' => 0.8,
            'spaces' => 0.7,
            'profiles' => 0.5,
            'pages' => 0.6,
        ],
    ],

];
