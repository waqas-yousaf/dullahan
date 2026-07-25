# Dulluhan

Dulluhan is a lightweight, high-performance, and feature-rich **Laravel Headless CMS & Editorial Blog Package**. It provides a fully session-isolated administration panel, a state-of-the-art rich text editor, built-in SEO optimizations, dynamic sitemaps, ready-made Blade components, and a secure headless JSON API.

Whether you want to build a headless blog using modern frontend frameworks (like Next.js, Nuxt, or Astro) or render posts server-side using Laravel's native Blade templates, Dulluhan has everything you need to manage, publish, and distribute your content.

## Features

### 🖋️ Quill-Powered Rich Text Editor
- **Modern Editing Interface**: Powered by Quill editor with support for font formats, media embeds, formulas, code blocks, and custom styling.
- **Drag & Drop Uploads**: Seamless image uploads directly into the editor with drag-and-drop support, storing assets in your configured public path.
- **Auto-Save Mechanism**: Asynchronous background drafts autosaving while typing.

### 🔒 Session-Isolated Admin Panel
- **Isolated Authentication**: Dedicated guard and session isolation ensures your editorial admin panel is completely separate from standard host application users.
- **Google reCAPTCHA Integration**: Out-of-the-box bot protection supporting both reCAPTCHA v2 and v3 (with customizable minimum scoring).
- **Profile & Password Management**: Authors can update bios, roles, avatar URLs, social media links, and change their login credentials directly from their dashboard.

### 🌐 Secure Headless JSON API
- **Fast Content Retrieval**: Fast, paginated JSON endpoints for listing and viewing posts.
- **Robust API Security**: Protect endpoints with API key authorization (using request headers or query parameters) and optional domain origin whitelisting.
- **Taxonomy Filtering**: Clean endpoints with support for filtering by post types (`post`, `article`, `news`, `page`) and category slugs.

### 🚀 Advanced SEO & Schema Markup
- **Full SEO Controls**: Comprehensive meta fields including Meta Title, Meta Description, Keywords, Canonical URL, and Robots instructions (`index,follow`, `noindex`, etc.).
- **Open Graph Ready**: Custom Open Graph Title, Description, and Image configurations for perfect social media sharing.
- **Structured Data (JSON-LD)**: Input fields for custom Schema JSON to boost search engine visibility and rich snippet ranking.

### 🗺️ Dynamic XML Sitemap
- **SEO-Optimized Sitemaps**: Automatic XML sitemap generation for indexing search engines, updating in real-time as content changes.
- **SEO Elements**: Includes custom prioritized weightings, update frequencies, custom URL structures, and featured images for media-rich index nodes.

### 📦 Flexible Rendering (Blade Components)
- **Pre-Built Blade Components**: Easily render post lists and cards on your frontend using `<x-dulluhan-post-list>` and `<x-dulluhan-post-card>`.
- **Query & Property Filtering**: Pass Eloquent builders or collections directly and filter by category, post type, search terms, and limit/pagination settings.

### 👥 Authors & Categories Management
- **Multi-Author Support**: Create and manage multiple editorial writers, assign authorship to posts, and display custom public bio boxes.
- **Custom Post Types**: Register custom post types and taxonomies (e.g. posts, articles, documentation) directly in the configuration.

## Requirements

- PHP `^8.3`
- Laravel `11.x` or `12.x`

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

The package comes with a `dulluhan.php` configuration file. If you haven't published it, run `php artisan vendor:publish --tag=dulluhan-config` to copy it to your host application's config directory.

Below is a detailed guide to all available configuration parameters and their respective Environment (`.env`) overrides.

### 1. General Settings
- **`route_prefix`** (Env: `DULLUHAN_ROUTE_PREFIX`): The base URL prefix for accessing the editorial admin panel.
  - *Default*: `'spanel'`
- **`api_prefix`** (Env: `DULLUHAN_API_PREFIX`): The URL prefix for the headless JSON API endpoints.
  - *Default*: `'api/dulluhan'`

### 2. Middleware Control
- **`middleware`**: Customize the middleware stacks applied to routes:
  - **`web`**: Applied to the general package frontend/web endpoints. *Default*: `['web']`.
  - **`api`**: Applied to public headless endpoints. *Default*: `['api', 'dulluhan.api']`.
  - **`admin`**: Applied to the session-isolated admin panel. *Default*: `['dulluhan.admin']`.

### 3. API Security & Access Keys
Protect your public headless endpoints from unauthorized access by requiring API keys and restricting client origins.
- **`api_security.enabled`** (Env: `DULLUHAN_API_SECURITY_ENABLED`): Set to `true` to require credentials.
  - *Default*: `false`
- **`api_security.keys`** (Env: `DULLUHAN_API_KEYS`): A comma-separated list of keys authorized to query the API.
  - *Example*: `DULLUHAN_API_KEYS="secret-key-1,secret-key-2"`
- **`api_security.allowed_domains`** (Env: `DULLUHAN_API_ALLOWED_DOMAINS`): A comma-separated list of domains allowed to request the API.
  - *Example*: `DULLUHAN_API_ALLOWED_DOMAINS="example.com,www.example.com"`
- **`api_security.header`**: The request header key to look for the API key.
  - *Default*: `'X-Dulluhan-Api-Key'`
- **`api_security.query_parameter`**: Fallback query parameter name for the API key.
  - *Default*: `'api_key'`

*Note: The API documentation page detailing request headers and key configuration is accessible inside the admin panel at `/spanel/api-documentation`.*

### 4. Dynamic XML Sitemap
Automatically compile and serve an XML sitemap of all published posts for SEO indexing.
- **`sitemap.enabled`** (Env: `DULLUHAN_SITEMAP_ENABLED`): Toggles sitemap compilation.
  - *Default*: `true`
- **`sitemap.path`** (Env: `DULLUHAN_SITEMAP_PATH`): The sitemap XML route endpoint.
  - *Default*: `'blog-sitemap.xml'`
- **`sitemap.post_url_pattern`** (Env: `DULLUHAN_POST_URL_PATTERN`): The URL structure to construct sitemap links if a post doesn't specify a custom Canonical URL.
  - *Default*: `'/blog/{slug}'`
- **`sitemap.changefreq`** (Env: `DULLUHAN_SITEMAP_CHANGEFREQ`): The `<changefreq>` frequency tag value.
  - *Default*: `'weekly'`
- **`sitemap.priority`** (Env: `DULLUHAN_SITEMAP_PRIORITY`): The `<priority>` index score.
  - *Default*: `'0.7'`
- **`sitemap.include_images`** (Env: `DULLUHAN_SITEMAP_INCLUDE_IMAGES`): Appends post featured images (if present) to the XML sitemap node.
  - *Default*: `true`

### 5. Google reCAPTCHA
Secure the admin login/authentication forms.
- **`recaptcha.enabled`** (Env: `DULLUHAN_RECAPTCHA_ENABLED`): Toggles CAPTCHA validation.
  - *Default*: `false`
- **`recaptcha.version`** (Env: `DULLUHAN_RECAPTCHA_VERSION`): Target reCAPTCHA version, supporting `'v2'` or `'v3'`.
  - *Default*: `'v2'`
- **`recaptcha.site_key`** (Env: `DULLUHAN_RECAPTCHA_SITE_KEY`): Public client key.
- **`recaptcha.secret_key`** (Env: `DULLUHAN_RECAPTCHA_SECRET_KEY`): Secret verification key.
- **`recaptcha.minimum_score`** (Env: `DULLUHAN_RECAPTCHA_MINIMUM_SCORE`): Threshold for spam rejection on v3 actions.
  - *Default*: `0.5`
- **`recaptcha.verify_url`**: The validation endpoint.
  - *Default*: `'https://www.google.com/recaptcha/api/siteverify'`

### 6. Media Uploads
- **`uploads.path`**: Local destination subdirectory inside the host's `public/` directory where editor images are stored.
  - *Default*: `'uploads/dulluhan'` (resolves to `public/uploads/dulluhan`)
- **`uploads.max_kb`**: Maximum allowed image upload size in kilobytes.
  - *Default*: `4096` (4MB)
- **`uploads.mimes`**: Array of allowed image extensions.
  - *Default*: `['jpeg', 'png', 'jpg', 'webp', 'svg']`

### 7. Post Types & Taxonomy
- **`post_types`**: Associative array mapping key identifiers to human-readable names for post organization.
  ```php
  'post_types' => [
      'post' => 'Post',
      'article' => 'Article',
      'news' => 'News',
      'page' => 'Page',
  ]
  ```
- **`default_post_type`**: The fallback post type pre-selected when creating new posts.
  - *Default*: `'post'`

### 8. Auto-Save Intervals
- **`autosave.enabled`**: Automatically saves drafts in the background while typing.
  - *Default*: `true`
- **`autosave.interval_ms`**: Background auto-save interval frequency in milliseconds.
  - *Default*: `30000` (30 seconds)

### 9. Default Administrator Account
Values used during `php artisan dulluhan:install` to initialize the default author.
- **`admin.name`** (Env: `DULLUHAN_ADMIN_NAME`): Admin full name.
  - *Default*: `'Dulluhan Admin'`
- **`admin.email`** (Env: `DULLUHAN_ADMIN_EMAIL`): Admin email username.
  - *Default*: `'admin@example.com'`
- **`admin.password`** (Env: `DULLUHAN_ADMIN_PASSWORD`): Admin initial password.
  - *Default*: random 16-character password generated at install time and printed once.

### 10. Listing Pagination
- **`pagination.posts_per_page`**: Limit of posts fetched per page by the public headless API.
  - *Default*: `12`
- **`pagination.admin_posts_per_page`**: Number of posts shown per page inside the admin panel management grid.
  - *Default*: `15`



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
    :posts="\WaqasYousaf\Dulluhan\Models\Post::query()->with('categories', 'author')"
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
