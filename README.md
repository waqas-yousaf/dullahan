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

For local development from `D:\Projects\dulluhan`, register the path repository in the host Laravel app first:

```bash
composer config repositories.dulluhan path ../dulluhan
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

If Artisan does not list `dulluhan:install` after installing the package, rebuild Laravel package discovery:

```bash
composer dump-autoload
php artisan package:discover
php artisan list dulluhan
```

The host Laravel app must be able to write to `bootstrap/cache`, `storage/logs`, the configured database, and `public/uploads/dulluhan`.

The default admin panel is available at `/spanel`. The public API is available at `/api/dulluhan/posts` and `/api/dulluhan/posts/{slug}`.
The dashboard includes author-box profile editing and password changes for the logged-in Dulluhan author.

The posts API can be filtered with `?type=news` and `?category=announcements`.
Each API post includes an `author_box` payload with public author profile fields when enabled from the dashboard.
The admin post editor includes SEO fields for slug, meta title, meta description, keywords, canonical URL, Open Graph values, robots, and schema JSON.
The admin panel shows the host Laravel app name in the sidebar and Dulluhan plus its package version in the footer.

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

Protect the public API with API keys and optional domain restrictions:

```dotenv
DULLUHAN_API_SECURITY_ENABLED=true
DULLUHAN_API_KEYS="first-key,second-key"
DULLUHAN_API_ALLOWED_DOMAINS="example.com,www.example.com"
```

Requests should send the key in the `X-Dulluhan-Api-Key` header. The API documentation page is available inside the admin panel at `/spanel/api-documentation`.

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

## API Routes

Dulluhan exposes headless JSON API endpoints for retrieving posts. The API base prefix can be customized using `DULLUHAN_API_PREFIX` (defaults to `api/dulluhan`).

### 1. List Posts
Retrieve a paginated list of published posts.

- **Endpoint**: `GET /api/dulluhan/posts`
- **Query Parameters**:
  - `type` (string, optional): Filter posts by post type (e.g. `post`, `article`, `news`, `page`).
  - `category` (string, optional): Filter posts by category slug (e.g. `announcements`).
- **Response Format**: Standard Laravel pagination payload where each item contains:
  - `id`: Post unique ID.
  - `title`: Post title.
  - `slug`: Unique post slug.
  - `post_type`: Type of post.
  - `excerpt`: Brief summary of the post.
  - `content`: Quill HTML content.
  - `status`: Post status (`published` / `draft`).
  - `featured_image`: Path to the featured image.
  - `published_at`: Publication timestamp.
  - `created_at` / `updated_at`: Timestamps.
  - `categories`: Array of associated categories.
  - `seo`: SEO metadata object containing:
    - `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `og_title`, `og_description`, `og_image`, `robots`, `schema_json`.
  - `author_box`: Object with author public profile (if enabled):
    - `name`, `job_title`, `bio`, `avatar`, `website_url`, `social_links` (array).

### 2. View Post
Retrieve detailed information for a single published post.

- **Endpoint**: `GET /api/dulluhan/posts/{slug}`
- **Parameters**:
  - `{slug}` (string, required): The unique URL slug of the post.
- **Response Format**: A wrapper object containing `data` with all the post fields described above. Returns `404 Not Found` if the post is a draft or does not exist.

## Credits & Author

Developed by Waqas Yousaf. Follow me on Twitter/X: [@imakewebapps](https://x.com/imakewebapps).
