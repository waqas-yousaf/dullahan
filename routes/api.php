<?php

use Illuminate\Support\Facades\Route;
use WaqasYousaf\Dullahan\Http\Controllers\Api\PostController;

Route::prefix(config('dullahan.api_prefix', 'api/dullahan'))
    ->name('dullahan.api.')
    ->middleware(config('dullahan.middleware.api', ['api']))
    ->group(function (): void {
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');
    });
