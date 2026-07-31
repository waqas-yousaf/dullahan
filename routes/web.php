<?php

use Illuminate\Support\Facades\Route;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\AuthorController;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\AuthController;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\AutosaveController;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\CategoryController;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\DashboardController;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\ProfileController;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\PostController;
use WaqasYousaf\Dullahan\Http\Controllers\Admin\UploadController;
use WaqasYousaf\Dullahan\Http\Controllers\SitemapController;

Route::get(config('dullahan.sitemap.path', 'blog-sitemap.xml'), SitemapController::class)
    ->name('dullahan.sitemap');

Route::prefix(config('dullahan.route_prefix', 'spanel'))
    ->name('dullahan.admin.')
    ->middleware(config('dullahan.middleware.web', ['web']))
    ->group(function (): void {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');

        Route::middleware(config('dullahan.middleware.admin', ['web', 'dullahan.admin']))->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::post('posts/autosave', AutosaveController::class)->name('posts.autosave');
            Route::post('posts/{post}/autosave', AutosaveController::class)->name('posts.autosave.existing');
            Route::get('posts/export', [PostController::class, 'export'])->name('posts.export');
            Route::resource('posts', PostController::class)->except(['show']);
            Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');
            Route::resource('categories', CategoryController::class)->except(['show', 'create']);
            Route::get('authors/export', [AuthorController::class, 'export'])->name('authors.export');
            Route::resource('authors', AuthorController::class)->except(['show']);
            Route::post('uploads/images', UploadController::class)->name('uploads.images');
        });
    });
