<?php

return [
    'content' => [
        'post_type' => env('WORDPRESS_BLOG_POST_TYPE', 'blog_post'),
        'taxonomy' => env('WORDPRESS_BLOG_TAXONOMY', 'blog_category'),
    ],
    'cache' => [
        'store' => env('WORDPRESS_CACHE_STORE', env('CACHE_STORE', 'file')),
        'ttl' => (int) env('WORDPRESS_CACHE_TTL', 3600),
        'prefix' => env('WORDPRESS_CACHE_PREFIX', 'wordpress'),
        'version_key' => env('WORDPRESS_CACHE_VERSION_KEY', 'wordpress.posts.version'),
    ],
    'invalidation' => [
        'token' => env('LARAVEL_CACHE_INVALIDATION_TOKEN', ''),
        'url' => env('LARAVEL_CACHE_INVALIDATION_URL', ''),
    ],
];