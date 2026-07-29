<?php

namespace WaqasYousaf\Dullahan\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $table = 'dullahan_posts';

    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'post_type',
        'content',
        'status',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'robots',
        'schema_markup',
        'published_at',
        'autosaved_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'autosaved_at' => 'datetime',
            'schema_markup' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            if (! $post->slug) {
                $post->slug = static::uniqueSlug($post->title, $post->exists ? $post->getKey() : null);
            }

            if ($post->status === 'published' && ! $post->published_at) {
                $post->published_at = now();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function seoOptions(): array
    {
        return [
            'meta_title' => $this->meta_title ?: $this->title,
            'meta_description' => $this->meta_description ?: Str::limit(strip_tags($this->content), 150),
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'og_title' => $this->og_title ?: $this->meta_title ?: $this->title,
            'og_description' => $this->og_description ?: $this->meta_description ?: Str::limit(strip_tags($this->content), 150),
            'og_image' => $this->og_image ?: $this->featured_image,
            'robots' => $this->robots ?: 'index,follow',
            'schema_markup' => $this->schema_markup,
        ];
    }

    public function setSchemaMarkupAttribute(mixed $value): void
    {
        if (is_string($value)) {
            $value = trim($value);
            $this->attributes['schema_markup'] = $value === '' ? null : $value;

            return;
        }

        $this->attributes['schema_markup'] = $value ? json_encode($value) : null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['slug'] = $value ? Str::slug($value) : null;
    }

    public function publicUrl(): string
    {
        if ($this->canonical_url) {
            return $this->canonical_url;
        }

        $pattern = config('dullahan.sitemap.post_url_pattern', '/blog/{category}/{slug}');

        return $this->replaceUrlTokens($pattern);
    }

    public function blogViewUrl(): ?string
    {
        $base = trim((string) config('dullahan.blog_view_url', ''));

        if ($base === '') {
            return null;
        }

        if (str_contains($base, '{slug}') || str_contains($base, '{category}')) {
            return $this->replaceUrlTokens($base);
        }

        return rtrim($base, '/') . '/' . $this->categorySlug() . '/' . $this->slug;
    }

    private function replaceUrlTokens(string $pattern): string
    {
        $path = str_replace(
            ['{category}', '{slug}', '{id}', '{type}'],
            [$this->categorySlug(), $this->slug, $this->getKey(), $this->post_type],
            $pattern
        );

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        return url($path);
    }

    private function categorySlug(): string
    {
        return $this->category ? $this->category->slug : 'uncategorized';
    }
    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
