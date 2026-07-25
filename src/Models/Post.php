<?php

namespace WaqasYousaf\Dulluhan\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $table = 'dulluhan_posts';

    protected $fillable = [
        'author_id',
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'dulluhan_category_post', 'post_id', 'category_id')
            ->withTimestamps();
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

        $pattern = config('dulluhan.sitemap.post_url_pattern', '/blog/{slug}');
        return url(str_replace('{slug}', $this->slug, $pattern));
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
