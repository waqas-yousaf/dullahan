# Dulluhan

Dulluhan is a Laravel package for a session-isolated editorial admin panel, Quill-powered post editing, direct public image uploads, public Blade components, and a headless posts API.

## Requirements

- PHP `^8.2`
- Laravel `10.x`, `11.x`, or `12.x`

## Installation

Install the package with Composer:

```bash
composer require waqas-yousaf/dulluhan
```

Laravel package auto-discovery registers the service provider automatically.

Publish optional overrides:

```bash
php artisan vendor:publish --tag=dulluhan-config
php artisan vendor:publish --tag=dulluhan-views
php artisan vendor:publish --tag=dulluhan-migrations
```

Run the installer:

```bash
php artisan dulluhan:install
```

The default admin panel is available at `/spanel`. The public API is available at `/api/dulluhan/posts` and `/api/dulluhan/posts/{slug}`.

The posts API can be filtered with `?type=news` and `?category=announcements`.

## Configuration

Use environment variables to set the initial admin account:

```dotenv
DULLUHAN_ADMIN_NAME="Dulluhan Admin"
DULLUHAN_ADMIN_EMAIL="admin@example.com"
DULLUHAN_ADMIN_PASSWORD="password"
```

The admin route prefix can be changed with:

```dotenv
DULLUHAN_ROUTE_PREFIX=spanel
```

Enable reCAPTCHA on auth pages with:

```dotenv
DULLUHAN_RECAPTCHA_ENABLED=true
DULLUHAN_RECAPTCHA_VERSION=v2
DULLUHAN_RECAPTCHA_SITE_KEY="your-site-key"
DULLUHAN_RECAPTCHA_SECRET_KEY="your-secret-key"
```

For reCAPTCHA v3, set `DULLUHAN_RECAPTCHA_VERSION=v3` and optionally tune `DULLUHAN_RECAPTCHA_MINIMUM_SCORE`.

Post types are configured in `config/dulluhan.php`:

```php
'post_types' => [
    'post' => 'Post',
    'article' => 'Article',
    'news' => 'News',
    'page' => 'Page',
],
```

## Blade Components

```blade
<x-dulluhan-post-list :posts="$posts" />
<x-dulluhan-post-card :post="$post" />
```

Filter posts directly through component props:

```blade
<x-dulluhan-post-list
    :posts="$posts"
    search="dubai"
    post-type="news"
    category="announcements"
    status="published"
    :limit="6"
/>
```

The `posts` prop may be a collection, paginator, array, or Eloquent query builder. For query builders, the component can paginate the filtered result:

```blade
<x-dulluhan-post-list
    :posts="\YourVendor\Dulluhan\Models\Post::query()->with('categories', 'author')"
    category="announcements"
    :paginate="true"
    :per-page="9"
/>
```
