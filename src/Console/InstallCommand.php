<?php

namespace WaqasYousaf\Dulluhan\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use WaqasYousaf\Dulluhan\Models\Author;

class InstallCommand extends Command
{
    protected $signature = 'dulluhan:install {--force : Force migrations in production}';

    protected $description = 'Install Dulluhan migrations, upload storage, and the default admin author.';

    public function handle(): int
    {
        $this->info('Installing Dulluhan...');

        $this->call('migrate', [
            '--path' => __DIR__ . '/../../database/migrations',
            '--realpath' => true,
            '--force' => $this->option('force'),
        ]);

        $relativePath = trim(config('dulluhan.uploads.path', 'uploads/dulluhan'), '/');
        $publicPath = public_path($relativePath);

        File::ensureDirectoryExists($publicPath, 0755, true);
        @chmod($publicPath, 0755);

        $configuredPassword = config('dulluhan.admin.password');
        $password = $configuredPassword ?: Str::password(16);

        $admin = Author::query()->firstOrCreate(
            ['email' => config('dulluhan.admin.email')],
            [
                'name' => config('dulluhan.admin.name'),
                'password' => Hash::make($password),
            ]
        );

        $this->newLine();
        $this->info('Dulluhan is ready.');
        $this->line('Admin URL: ' . url(config('dulluhan.route_prefix', 'spanel')));
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
