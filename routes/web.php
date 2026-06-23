<?php

use App\Http\Controllers\BuildController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BuildController::class, 'index'])->name('build.index');

Route::get('/{name}', [BuildController::class, 'show'])
    ->name('build.show')
    ->where('name', '[a-zA-Z0-9_-]+');
