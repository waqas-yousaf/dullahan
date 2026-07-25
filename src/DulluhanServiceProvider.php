<?php

namespace WaqasYousaf\Dulluhan;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use WaqasYousaf\Dulluhan\Console\InstallCommand;
use WaqasYousaf\Dulluhan\Http\Middleware\DulluhanApiAccess;
use WaqasYousaf\Dulluhan\Http\Middleware\DulluhanAdminAuth;
use WaqasYousaf\Dulluhan\Models\Author;
use WaqasYousaf\Dulluhan\View\Components\PostCard;
use WaqasYousaf\Dulluhan\View\Components\PostList;

class DulluhanServiceProvider extends ServiceProvider
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
        $this->mergeConfigFrom(__DIR__ . '/../config/dulluhan.php', 'dulluhan');
    }

    public function boot(Router $router): void
    {
        $this->injectAuthConfiguration();

        $router->aliasMiddleware('dulluhan.admin', DulluhanAdminAuth::class);
        $router->aliasMiddleware('dulluhan.api', DulluhanApiAccess::class);

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dulluhan');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Blade::component('dulluhan-post-card', PostCard::class);
        Blade::component('dulluhan-post-list', PostList::class);

        $this->publishes([
            __DIR__ . '/../config/dulluhan.php' => config_path('dulluhan.php'),
        ], 'dulluhan-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/dulluhan'),
        ], 'dulluhan-views');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'dulluhan-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
        }
    }

    private function injectAuthConfiguration(): void
    {
        $guard = config('dulluhan.auth.guard', 'dulluhan');
        $provider = config('dulluhan.auth.provider', 'dulluhan_authors');

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
