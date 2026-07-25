<?php

use Illuminate\Support\Facades\Route;
use WaqasYousaf\Dulluhan\Http\Controllers\Api\PostController;

Route::prefix(config('dulluhan.api_prefix', 'api/dulluhan'))
    ->name('dulluhan.api.')
    ->middleware(config('dulluhan.middleware.api', ['api']))
    ->group(function (): void {
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');
    });
