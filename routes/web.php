<?php

use Illuminate\Support\Facades\Route;
use LarabergCms\LarabergCms\Controllers\LarabergController;
use LarabergCms\LarabergCms\Controllers\LarabergMediaController;

Route::group(
    [
        'prefix' => config('larabergcms.prefix', 'laraberg'),
        'middleware' => config('larabergcms.middleware', ['web']),
        'as' => 'larabergcms.',
    ],
    function () {
        Route::post('media', [LarabergMediaController::class, 'store'])->name('media');

        Route::get('/', [LarabergController::class, 'index'])->name('index');
        Route::get('create', [LarabergController::class, 'create'])->name('create');
        Route::post('/', [LarabergController::class, 'store'])->name('store');
        Route::get('{laraberg}', [LarabergController::class, 'show'])->name('show');
        Route::get('{laraberg}/edit', [LarabergController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '{laraberg}', [LarabergController::class, 'update'])->name('update');
        Route::delete('{laraberg}', [LarabergController::class, 'destroy'])->name('destroy');
    }
);