<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Notifica o Laravel para invalidar o cache do blog quando um post editorial muda.
 */
add_action('save_post', static function (int $postId, WP_Post $post, bool $update): void {
    if (wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
        return;
    }

    if ($post->post_status !== 'publish') {
        return;
    }

    if (! in_array($post->post_type, ['post'], true)) {
        return;
    }

    $endpoint = defined('LARAVEL_CACHE_INVALIDATION_URL') ? trim((string) LARAVEL_CACHE_INVALIDATION_URL) : '';
    $token = defined('LARAVEL_CACHE_INVALIDATION_TOKEN') ? trim((string) LARAVEL_CACHE_INVALIDATION_TOKEN) : '';

    if ($endpoint === '' || $token === '') {
        return;
    }

    wp_remote_post($endpoint, [
        'timeout' => 3,
        'blocking' => false,
        'headers' => [
            'X-Wordpress-Cache-Token' => $token,
            'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode([
            'post_id' => $postId,
            'post_type' => $post->post_type,
            'slug' => $post->post_name,
            'update' => $update,
        ]),
    ]);
}, 10, 3);
