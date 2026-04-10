<?php

namespace App\Models;

use Corcel\Model\Meta\ThumbnailMeta;
use Corcel\Model\Post as CorcelPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class BlogPost extends CorcelPost
{
    protected $connection = 'wordpress';

    protected $postType = 'post';

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || trim($term) === '') {
            return $query;
        }

        return $query->where(function (Builder $subquery) use ($term) {
            $subquery
                ->where('post_title', 'like', "%{$term}%")
                ->orWhere('post_excerpt', 'like', "%{$term}%")
                ->orWhere('post_content', 'like', "%{$term}%");
        });
    }

    public function getCoverImageAttribute(): ?string
    {
        if ($this->thumbnail === null) {
            return $this->image;
        }

        $image = $this->thumbnail->size(ThumbnailMeta::SIZE_LARGE);

        if (is_array($image)) {
            return $image['url'] ?? $this->image;
        }

        return is_string($image) ? $image : $this->image;
    }

    public function getSummaryAttribute(): string
    {
        $content = $this->post_excerpt ?: strip_tags($this->content);

        return Str::words(trim(strip_tags($content)), 26, '...');
    }

    public function getPublishedAtLabelAttribute(): string
    {
        return $this->post_date
            ? $this->post_date->locale(App::currentLocale())->translatedFormat('d M Y')
            : '';
    }
}