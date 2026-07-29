<?php

return [
    'route_prefix' => env('DULLAHAN_ROUTE_PREFIX', 'spanel'),
    'api_prefix' => env('DULLAHAN_API_PREFIX', 'api/dullahan'),
    'blog_view_url' => env('DULLAHAN_BLOG_VIEW_URL', ''),

    'middleware' => [
        'web' => ['web'],
        'api' => ['api', 'dullahan.api'],
        'admin' => ['dullahan.admin'],
    ],

    'api_security' => [
        'enabled' => env('DULLAHAN_API_SECURITY_ENABLED', false),
        'keys' => array_filter(array_map('trim', explode(',', env('DULLAHAN_API_KEYS', '')))),
        'allowed_domains' => array_filter(array_map('trim', explode(',', env('DULLAHAN_API_ALLOWED_DOMAINS', '')))),
        'header' => 'X-Dullahan-Api-Key',
        'query_parameter' => 'api_key',
    ],

    'api_throttle' => [
        'enabled' => env('DULLAHAN_API_THROTTLE_ENABLED', true),
        'max_attempts' => (int) env('DULLAHAN_API_THROTTLE_MAX_ATTEMPTS', 60),
        'decay_minutes' => (int) env('DULLAHAN_API_THROTTLE_DECAY_MINUTES', 1),
    ],

    'sitemap' => [
        'enabled' => env('DULLAHAN_SITEMAP_ENABLED', true),
        'path' => env('DULLAHAN_SITEMAP_PATH', 'blog-sitemap.xml'),
        'post_url_pattern' => env('DULLAHAN_POST_URL_PATTERN', '/blog/{category}/{slug}'),
        'changefreq' => env('DULLAHAN_SITEMAP_CHANGEFREQ', 'weekly'),
        'priority' => env('DULLAHAN_SITEMAP_PRIORITY', '0.7'),
        'include_images' => env('DULLAHAN_SITEMAP_INCLUDE_IMAGES', true),
    ],

    'auth' => [
        'guard' => 'dullahan',
        'provider' => 'dullahan_authors',
    ],

    'recaptcha' => [
        'enabled' => env('DULLAHAN_RECAPTCHA_ENABLED', false),
        'version' => env('DULLAHAN_RECAPTCHA_VERSION', 'v2'),
        'site_key' => env('DULLAHAN_RECAPTCHA_SITE_KEY'),
        'secret_key' => env('DULLAHAN_RECAPTCHA_SECRET_KEY'),
        'minimum_score' => env('DULLAHAN_RECAPTCHA_MINIMUM_SCORE', 0.5),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    ],

    'uploads' => [
        'path' => 'uploads/dullahan',
        'max_kb' => 4096,
        'mimes' => ['jpeg', 'png', 'jpg', 'webp', 'svg'],
    ],

    'post_types' => [
        'post' => 'Post',
        'article' => 'Article',
        'news' => 'News',
        'page' => 'Page',
    ],

    'default_post_type' => 'post',

    'autosave' => [
        'enabled' => true,
        'interval_ms' => 30000,
    ],

    'admin' => [
        'name' => env('DULLAHAN_ADMIN_NAME', 'Dullahan Admin'),
        'email' => env('DULLAHAN_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('DULLAHAN_ADMIN_PASSWORD'),
    ],

    'pagination' => [
        'posts_per_page' => 12,
        'admin_posts_per_page' => 15,
    ],
];
