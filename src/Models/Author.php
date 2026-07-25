<?php

namespace WaqasYousaf\Dulluhan\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Author extends Authenticatable
{
    use Notifiable;

    protected $table = 'dulluhan_authors';

    protected $fillable = [
        'name',
        'email',
        'password',
        'job_title',
        'bio',
        'avatar',
        'website_url',
        'facebook_url',
        'x_url',
        'linkedin_url',
        'instagram_url',
        'youtube_url',
        'social_links',
        'show_author_box',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'metadata' => 'array',
            'social_links' => 'array',
            'show_author_box' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function authorBox(): ?array
    {
        if (! $this->show_author_box) {
            return null;
        }

        return [
            'name' => $this->name,
            'job_title' => $this->job_title,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'website_url' => $this->website_url,
            'social_links' => $this->publicSocialLinks(),
        ];
    }

    public function publicSocialLinks(): array
    {
        $links = collect([
            ['label' => 'Facebook', 'url' => $this->facebook_url],
            ['label' => 'X', 'url' => $this->x_url],
            ['label' => 'LinkedIn', 'url' => $this->linkedin_url],
            ['label' => 'Instagram', 'url' => $this->instagram_url],
            ['label' => 'YouTube', 'url' => $this->youtube_url],
        ])->filter(fn (array $link) => filled($link['url']));

        return $links
            ->merge($this->social_links ?? [])
            ->values()
            ->all();
    }
}
