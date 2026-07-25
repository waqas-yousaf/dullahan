# Dulluhan

Dulluhan is a Laravel package for a session-isolated editorial admin panel, Quill-powered post editing, direct public image uploads, public Blade components, and a headless posts API.

## Requirements

- PHP `^8.2`
- Laravel `10.x`, `11.x`, or `12.x`

## Installation

Install the package with Composer:

```bash
composer require your-vendor/dulluhan
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

## Blade Components

```blade
<x-dulluhan-post-list :posts="$posts" />
<x-dulluhan-post-card :post="$post" />
```
