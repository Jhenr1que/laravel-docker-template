<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WordpressCacheInvalidationTest extends TestCase
{
    public function test_it_rejects_requests_with_invalid_token(): void
    {
        config()->set('wordpress.invalidation.token', 'secret-token');
        config()->set('wordpress.cache.store', 'array');

        $response = $this->postJson(route('wordpress.cache.invalidate'), [
            'slug' => 'meu-post',
        ], [
            'X-Wordpress-Cache-Token' => 'wrong-token',
        ]);

        $response->assertUnauthorized();
    }

    public function test_it_bumps_cache_version_when_post_changes(): void
    {
        config()->set('wordpress.invalidation.token', 'secret-token');
        config()->set('wordpress.cache.store', 'array');
        config()->set('wordpress.cache.version_key', 'wordpress.posts.version');

        Cache::store('array')->forever('wordpress.posts.version', 3);

        $response = $this->postJson(route('wordpress.cache.invalidate'), [
            'post_id' => 15,
            'post_type' => 'post',
            'slug' => 'meu-post',
        ], [
            'X-Wordpress-Cache-Token' => 'secret-token',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'version' => 4,
            ]);

        $this->assertSame(4, Cache::store('array')->get('wordpress.posts.version'));
    }
}