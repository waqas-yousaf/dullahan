<?php

return [
    'route_prefix' => env('DULLUHAN_ROUTE_PREFIX', 'spanel'),
    'api_prefix' => env('DULLUHAN_API_PREFIX', 'api/dulluhan'),

    'middleware' => [
        'web' => ['web'],
        'api' => ['api'],
        'admin' => ['dulluhan.admin'],
    ],

    'auth' => [
        'guard' => 'dulluhan',
        'provider' => 'dulluhan_authors',
    ],

    'uploads' => [
        'path' => 'uploads/dulluhan',
        'max_kb' => 4096,
        'mimes' => ['jpeg', 'png', 'jpg', 'webp', 'svg'],
    ],

    'admin' => [
        'name' => env('DULLUHAN_ADMIN_NAME', 'Dulluhan Admin'),
        'email' => env('DULLUHAN_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('DULLUHAN_ADMIN_PASSWORD', 'password'),
    ],

    'pagination' => [
        'posts_per_page' => 12,
        'admin_posts_per_page' => 15,
    ],
];
