<?php

return [
    'route_prefix' => env('DULLUHAN_ROUTE_PREFIX', 'spanel'),
    'api_prefix' => env('DULLUHAN_API_PREFIX', 'api/dulluhan'),

    'middleware' => [
        'web' => ['web'],
        'api' => ['api', 'dulluhan.api'],
        'admin' => ['dulluhan.admin'],
    ],

    'api_security' => [
        'enabled' => env('DULLUHAN_API_SECURITY_ENABLED', false),
        'keys' => array_filter(array_map('trim', explode(',', env('DULLUHAN_API_KEYS', '')))),
        'allowed_domains' => array_filter(array_map('trim', explode(',', env('DULLUHAN_API_ALLOWED_DOMAINS', '')))),
        'header' => 'X-Dulluhan-Api-Key',
        'query_parameter' => 'api_key',
    ],

    'sitemap' => [
        'enabled' => env('DULLUHAN_SITEMAP_ENABLED', true),
        'path' => env('DULLUHAN_SITEMAP_PATH', 'blog-sitemap.xml'),
        'post_url_pattern' => env('DULLUHAN_POST_URL_PATTERN', '/blog/{category}/{slug}'),
        'changefreq' => env('DULLUHAN_SITEMAP_CHANGEFREQ', 'weekly'),
        'priority' => env('DULLUHAN_SITEMAP_PRIORITY', '0.7'),
        'include_images' => env('DULLUHAN_SITEMAP_INCLUDE_IMAGES', true),
    ],

    'auth' => [
        'guard' => 'dulluhan',
        'provider' => 'dulluhan_authors',
    ],

    'recaptcha' => [
        'enabled' => env('DULLUHAN_RECAPTCHA_ENABLED', false),
        'version' => env('DULLUHAN_RECAPTCHA_VERSION', 'v2'),
        'site_key' => env('DULLUHAN_RECAPTCHA_SITE_KEY'),
        'secret_key' => env('DULLUHAN_RECAPTCHA_SECRET_KEY'),
        'minimum_score' => env('DULLUHAN_RECAPTCHA_MINIMUM_SCORE', 0.5),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    ],

    'uploads' => [
        'path' => 'uploads/dulluhan',
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
        'name' => env('DULLUHAN_ADMIN_NAME', 'Dulluhan Admin'),
        'email' => env('DULLUHAN_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('DULLUHAN_ADMIN_PASSWORD'),
    ],

    'pagination' => [
        'posts_per_page' => 12,
        'admin_posts_per_page' => 15,
    ],
];
