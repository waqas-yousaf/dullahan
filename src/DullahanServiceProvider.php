<?php

namespace WaqasYousaf\Dullahan;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use WaqasYousaf\Dullahan\Console\InstallCommand;
use WaqasYousaf\Dullahan\Http\Middleware\DullahanApiAccess;
use WaqasYousaf\Dullahan\Http\Middleware\DullahanAdminAuth;
use WaqasYousaf\Dullahan\Models\Author;
use WaqasYousaf\Dullahan\View\Components\PostCard;
use WaqasYousaf\Dullahan\View\Components\PostList;

class DullahanServiceProvider extends ServiceProvider
{
    public static function version(): string
    {
        $composerPath = __DIR__ . '/../composer.json';
        if (file_exists($composerPath)) {
            $composer = json_decode(file_get_contents($composerPath), true);
            return $composer['version'] ?? '0.0.1';
        }
        return '1.0.0';
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dullahan.php', 'dullahan');
    }

    public function boot(Router $router): void
    {
        $this->injectAuthConfiguration();

        $router->aliasMiddleware('dullahan.admin', DullahanAdminAuth::class);
        $router->aliasMiddleware('dullahan.api', DullahanApiAccess::class);

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/dullahan', 'dullahan');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Blade::component('dullahan-post-card', PostCard::class);
        Blade::component('dullahan-post-list', PostList::class);

        $this->publishes([
            __DIR__ . '/../config/dullahan.php' => config_path('dullahan.php'),
        ], 'dullahan-config');

        $this->publishes([
            __DIR__ . '/../resources/views/dullahan' => resource_path('views/vendor/dullahan'),
        ], 'dullahan-views');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'dullahan-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
        }
    }

    private function injectAuthConfiguration(): void
    {
        $guard = config('dullahan.auth.guard', 'dullahan');
        $provider = config('dullahan.auth.provider', 'dullahan_authors');

        config()->set("auth.guards.$guard", array_merge([
            'driver' => 'session',
            'provider' => $provider,
        ], config("auth.guards.$guard", [])));

        config()->set("auth.providers.$provider", array_merge([
            'driver' => 'eloquent',
            'model' => Author::class,
        ], config("auth.providers.$provider", [])));
    }
}
