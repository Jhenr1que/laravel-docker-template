<?php

namespace App\Services\Wordpress;

use App\Repositories\Wordpress\WordpressPostRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WordpressBlogService
{
    public function __construct(
        private readonly WordpressPostRepository $wordpressPostRepository,
    ) {}

    public function featuredPosts(int $limit = 3): Collection
    {
        return $this->remember(
            key: $this->cacheKey('home', ['limit' => $limit]),
            callback: fn () => $this->wordpressPostRepository->featuredPosts($limit),
        );
    }

    public function hasPublishedPosts(): bool
    {
        return $this->remember(
            key: $this->cacheKey('has_posts'),
            callback: fn () => $this->wordpressPostRepository->hasPublishedPosts(),
        );
    }

    public function paginatePublishedPosts(?string $search, int $page = 1, int $perPage = 6): LengthAwarePaginatorContract
    {
        return $this->remember(
            key: $this->cacheKey('posts', [
                'search' => sha1((string) $search),
                'page' => $page,
                'per_page' => $perPage,
            ]),
            callback: fn () => $this->wordpressPostRepository->paginatePublishedPosts($search, $page, $perPage),
        );
    }

    public function findPublishedPostBySlug(string $slug): ?object
    {
        return $this->remember(
            key: $this->cacheKey('post', ['slug' => $slug]),
            callback: fn () => $this->wordpressPostRepository->findPublishedPostBySlug($slug),
        );
    }

    public function relatedPosts(string $slug, int $limit = 3): Collection
    {
        return $this->remember(
            key: $this->cacheKey('related', ['slug' => $slug, 'limit' => $limit]),
            callback: fn () => $this->wordpressPostRepository->relatedPosts($slug, $limit),
        );
    }

    public function categories(): Collection
    {
        return $this->remember(
            key: $this->cacheKey('categories'),
            callback: fn () => $this->wordpressPostRepository->categories(),
        );
    }

    public function sitemapPosts(int $limit = 50): Collection
    {
        return $this->remember(
            key: $this->cacheKey('sitemap', ['limit' => $limit]),
            callback: fn () => $this->wordpressPostRepository->sitemapPosts($limit),
        );
    }

    private function remember(string $key, callable $callback): mixed
    {
        return Cache::store(config('wordpress.cache.store', config('cache.default')))
            ->remember($key, now()->addSeconds((int) config('wordpress.cache.ttl', 3600)), $callback);
    }

    private function cacheKey(string $segment, array $context = []): string
    {
        $suffix = collect($context)
            ->map(fn (mixed $value, string $key) => $key.'.'.Str::slug((string) $value, '_'))
            ->implode('.');

        return trim(implode('.', array_filter([
            config('wordpress.cache.prefix', 'wordpress'),
            'v'.Cache::store(config('wordpress.cache.store', config('cache.default')))
                ->get(config('wordpress.cache.version_key', 'wordpress.posts.version'), 1),
            $segment,
            $suffix,
        ])), '.');
    }
}