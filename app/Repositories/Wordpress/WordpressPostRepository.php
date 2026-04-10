<?php

namespace App\Repositories\Wordpress;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WordpressPostRepository
{
    public function hasPublishedPosts(): bool
    {
        return DB::connection('wordpress')
            ->table($this->postsTable('posts'))
            ->where('posts.post_type', $this->postType())
            ->where('posts.post_status', 'publish')
            ->exists();
    }

    public function paginatePublishedPosts(?string $search, int $page, int $perPage): LengthAwarePaginatorContract
    {
        $query = $this->publishedPostsQuery($search);
        $total = (clone $query)->count();

        $items = $query
            ->forPage($page, $perPage)
            ->get();

        return new LengthAwarePaginator(
            items: $this->hydratePosts($items),
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => route('blog.index'),
                'query' => array_filter([
                    'search' => $search,
                ], fn (mixed $value) => $value !== null && $value !== ''),
            ],
        );
    }

    public function featuredPosts(int $limit): Collection
    {
        return $this->hydratePosts(
            $this->publishedPostsQuery()
                ->limit($limit)
                ->get(),
        );
    }

    public function findPublishedPostBySlug(string $slug): ?object
    {
        return $this->hydratePosts(
            $this->publishedPostsQuery()
                ->where('posts.post_name', $slug)
                ->limit(1)
                ->get(),
        )->first();
    }

    public function relatedPosts(string $slug, int $limit): Collection
    {
        $currentPost = $this->findPublishedPostBySlug($slug);

        if ($currentPost === null) {
            return collect();
        }

        return $this->hydratePosts(
            $this->publishedPostsQuery()
                ->where('posts.ID', '!=', $currentPost->id)
                ->limit($limit)
                ->get(),
        );
    }

    public function sitemapPosts(int $limit): Collection
    {
        return $this->hydratePosts(
            $this->publishedPostsQuery()
                ->limit($limit)
                ->get(),
        );
    }

    public function categories(): Collection
    {
        return DB::connection('wordpress')
            ->table('terms as terms')
            ->join('term_taxonomy as taxonomy', 'taxonomy.term_id', '=', 'terms.term_id')
            ->where('taxonomy.taxonomy', $this->taxonomy())
            ->orderBy('terms.name')
            ->get([
                'terms.term_id as id',
                'terms.slug',
                'terms.name',
            ]);
    }

    private function publishedPostsQuery(?string $search = null)
    {
        $query = DB::connection('wordpress')
            ->table($this->postsTable('posts'))
            ->where('posts.post_type', $this->postType())
            ->where('posts.post_status', 'publish')
            ->orderByDesc('posts.post_date')
            ->select([
                'posts.ID',
                'posts.post_name',
                'posts.post_title',
                'posts.post_excerpt',
                'posts.post_content',
                'posts.post_date',
                'posts.post_modified',
            ]);

        if ($search !== null && trim($search) !== '') {
            $query->where(function ($subquery) use ($search) {
                $subquery
                    ->where('posts.post_title', 'like', "%{$search}%")
                    ->orWhere('posts.post_excerpt', 'like', "%{$search}%")
                    ->orWhere('posts.post_content', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function hydratePosts(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        $postIds = $rows->pluck('ID')->map(fn (mixed $id) => (int) $id)->all();
        $thumbnailUrls = $this->thumbnailUrls($postIds);
        $categories = $this->categoriesByPost($postIds);

        return $rows->map(function (object $row) use ($thumbnailUrls, $categories) {
            $content = (string) $row->post_content;
            $excerpt = trim((string) $row->post_excerpt);
            $summarySource = $excerpt !== '' ? $excerpt : strip_tags($content);

            return (object) [
                'id' => (int) $row->ID,
                'slug' => (string) $row->post_name,
                'title' => html_entity_decode((string) $row->post_title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'content' => $content,
                'excerpt' => $excerpt,
                'summary' => Str::words(trim(strip_tags($summarySource)), 26, '...'),
                'published_at' => $row->post_date ? CarbonImmutable::parse($row->post_date) : null,
                'modified_at' => $row->post_modified ? CarbonImmutable::parse($row->post_modified) : null,
                'published_at_label' => $row->post_date
                    ? CarbonImmutable::parse($row->post_date)->locale(app()->getLocale())->translatedFormat('d M Y')
                    : '',
                'thumbnail' => $thumbnailUrls[(int) $row->ID] ?? null,
                'category' => $categories[(int) $row->ID] ?? null,
            ];
        });
    }

    private function thumbnailUrls(array $postIds): array
    {
        $thumbnailIds = DB::connection('wordpress')
            ->table('postmeta')
            ->whereIn('post_id', $postIds)
            ->where('meta_key', '_thumbnail_id')
            ->pluck('meta_value', 'post_id');

        if ($thumbnailIds->isEmpty()) {
            return [];
        }

        $attachments = DB::connection('wordpress')
            ->table('posts')
            ->whereIn('ID', $thumbnailIds->values()->all())
            ->pluck('guid', 'ID');

        return $thumbnailIds
            ->map(fn (mixed $attachmentId) => $attachments[(int) $attachmentId] ?? null)
            ->all();
    }

    private function categoriesByPost(array $postIds): array
    {
        $primaryCategories = DB::connection('wordpress')
            ->table('postmeta')
            ->whereIn('post_id', $postIds)
            ->where('meta_key', '_yoast_wpseo_primary_category')
            ->pluck('meta_value', 'post_id')
            ->map(fn (mixed $termId) => (int) $termId)
            ->all();

        $categories = DB::connection('wordpress')
            ->table('term_relationships as relationships')
            ->join('term_taxonomy as taxonomy', function ($join) {
                $join
                    ->on('taxonomy.term_taxonomy_id', '=', 'relationships.term_taxonomy_id')
                    ->where('taxonomy.taxonomy', '=', $this->taxonomy());
            })
            ->join('terms as terms', 'terms.term_id', '=', 'taxonomy.term_id')
            ->whereIn('relationships.object_id', $postIds)
            ->orderBy('terms.name')
            ->get([
                'relationships.object_id as post_id',
                'terms.term_id',
                'terms.name',
                'terms.slug',
            ])
            ->groupBy('post_id');

        return collect($postIds)
            ->mapWithKeys(function (int $postId) use ($categories, $primaryCategories) {
                $items = collect($categories->get($postId, []));

                if ($items->isEmpty()) {
                    return [$postId => null];
                }

                $primaryTermId = $primaryCategories[$postId] ?? null;
                $selected = $items->firstWhere('term_id', $primaryTermId) ?? $items->first();

                return [$postId => (object) [
                    'id' => (int) $selected->term_id,
                    'name' => (string) $selected->name,
                    'slug' => (string) $selected->slug,
                ]];
            })
            ->all();
    }

    private function postsTable(string $alias): string
    {
        return 'posts as '.$alias;
    }

    private function postType(): string
    {
        return (string) config('wordpress.content.post_type', 'post');
    }

    private function taxonomy(): string
    {
        return (string) config('wordpress.content.taxonomy', 'category');
    }
}