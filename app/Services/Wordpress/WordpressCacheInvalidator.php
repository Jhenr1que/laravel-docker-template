<?php

namespace App\Services\Wordpress;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WordpressCacheInvalidator
{
    public function invalidatePostCaches(?string $slug = null, ?string $postType = null): int
    {
        $store = Cache::store(config('wordpress.cache.store', config('cache.default')));
        $versionKey = (string) config('wordpress.cache.version_key', 'wordpress.posts.version');
        $currentVersion = (int) $store->get($versionKey, 1);
        $nextVersion = $currentVersion + 1;

        $store->forever($versionKey, $nextVersion);

        Log::info('WordPress cache invalidated.', [
            'slug' => $slug,
            'post_type' => $postType,
            'version' => $nextVersion,
        ]);

        return $nextVersion;
    }
}