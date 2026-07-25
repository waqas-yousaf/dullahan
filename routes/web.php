<?php

use Illuminate\Support\Facades\Route;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\ApiDocumentationController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\AuthorBoxController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\AuthorController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\AuthController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\AutosaveController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\CategoryController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\DashboardController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\PasswordController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\PostController;
use WaqasYousaf\Dulluhan\Http\Controllers\Admin\UploadController;
use WaqasYousaf\Dulluhan\Http\Controllers\SitemapController;

Route::get(config('dulluhan.sitemap.path', 'dulluhan-sitemap.xml'), SitemapController::class)
    ->name('dulluhan.sitemap');

Route::prefix(config('dulluhan.route_prefix', 'spanel'))
    ->name('dulluhan.admin.')
    ->middleware(config('dulluhan.middleware.web', ['web']))
    ->group(function (): void {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');

        Route::middleware(config('dulluhan.middleware.admin', ['web', 'dulluhan.admin']))->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::put('author-box', [AuthorBoxController::class, 'update'])->name('author-box.update');
            Route::get('password', [PasswordController::class, 'edit'])->name('password.edit');
            Route::put('password', [PasswordController::class, 'update'])->name('password.update');
            Route::post('posts/autosave', AutosaveController::class)->name('posts.autosave');
            Route::post('posts/{post}/autosave', AutosaveController::class)->name('posts.autosave.existing');
            Route::resource('posts', PostController::class)->except(['show']);
            Route::resource('categories', CategoryController::class)->except(['show', 'create']);
            Route::resource('authors', AuthorController::class)->except(['show']);
            Route::post('uploads/images', UploadController::class)->name('uploads.images');
        });
    });
