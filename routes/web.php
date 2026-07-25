<?php

use Illuminate\Support\Facades\Route;
use YourVendor\Dulluhan\Http\Controllers\Admin\AuthController;
use YourVendor\Dulluhan\Http\Controllers\Admin\DashboardController;
use YourVendor\Dulluhan\Http\Controllers\Admin\PostController;
use YourVendor\Dulluhan\Http\Controllers\Admin\UploadController;

Route::prefix(config('dulluhan.route_prefix', 'spanel'))
    ->name('dulluhan.admin.')
    ->middleware(config('dulluhan.middleware.web', ['web']))
    ->group(function (): void {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');

        Route::middleware(config('dulluhan.middleware.admin', ['web', 'dulluhan.admin']))->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::resource('posts', PostController::class)->except(['show']);
            Route::post('uploads/images', UploadController::class)->name('uploads.images');
        });
    });
