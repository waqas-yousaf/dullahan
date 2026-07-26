<?php

namespace WaqasYousaf\Dullahan\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use WaqasYousaf\Dullahan\Models\Author;

class InstallCommand extends Command
{
    protected $signature = 'dullahan:install {--force : Force migrations in production}';

    protected $description = 'Install Dullahan migrations, upload storage, and the default admin author.';

    public function handle(): int
    {
        $this->info('Installing Dullahan...');

        $this->call('migrate', [
            '--path' => __DIR__ . '/../../database/migrations',
            '--realpath' => true,
            '--force' => $this->option('force'),
        ]);

        $relativePath = trim(config('dullahan.uploads.path', 'uploads/dullahan'), '/');
        $publicPath = public_path($relativePath);

        File::ensureDirectoryExists($publicPath, 0755, true);
        @chmod($publicPath, 0755);

        $configuredPassword = config('dullahan.admin.password');
        $password = $configuredPassword ?: Str::password(16);

        $admin = Author::query()->firstOrCreate(
            ['email' => config('dullahan.admin.email')],
            [
                'name' => config('dullahan.admin.name'),
                'password' => Hash::make($password),
            ]
        );

        $this->newLine();
        $this->info('Dullahan is ready.');
        $this->line('Admin URL: ' . url(config('dullahan.route_prefix', 'spanel')));
        $this->line('Admin email: ' . $admin->email);
        if ($admin->wasRecentlyCreated) {
            $this->line('Admin password: ' . $password);
        } else {
            $this->line('Admin password: unchanged; author already exists.');
        }
        $this->line('Uploads path: public/' . $relativePath);

        return self::SUCCESS;
    }
}
