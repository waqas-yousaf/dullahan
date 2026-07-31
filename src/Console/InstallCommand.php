<?php

namespace WaqasYousaf\Dullahan\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use WaqasYousaf\Dullahan\Models\Author;
use WaqasYousaf\Dullahan\Models\Category;

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

        [$name, $email, $password] = $this->promptSuperAdmin();

        $admin = Author::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        if (! $admin->wasRecentlyCreated && $admin->name !== $name) {
            $admin->name = $name;
            $admin->save();
        }

        Category::query()->firstOrCreate(['slug' => 'misc'], ['name' => 'Misc']);

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

    private function promptSuperAdmin(): array
    {
        $name = $this->ask('Super admin full name', config('dullahan.admin.name', 'Dullahan Admin'));

        while (! filled($name)) {
            $this->error('Full name is required.');
            $name = $this->ask('Super admin full name', config('dullahan.admin.name', 'Dullahan Admin'));
        }

        $email = $this->ask('Super admin email', config('dullahan.admin.email', 'admin@example.com'));

        while (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address.');
            $email = $this->ask('Super admin email', config('dullahan.admin.email', 'admin@example.com'));
        }

        $password = $this->secret('Super admin password');
        $confirm = $this->secret('Confirm password');

        while (! filled($password) || $password !== $confirm) {
            if (! filled($password)) {
                $this->error('Password is required.');
            } else {
                $this->error('Passwords do not match.');
            }

            $password = $this->secret('Super admin password');
            $confirm = $this->secret('Confirm password');
        }

        return [$name, $email, $password];
    }
}
