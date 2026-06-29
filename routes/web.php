<?php

use App\Http\Controllers\BuildController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BuildController::class, 'index'])->name('build.index');

Route::get('/build', [BuildController::class, 'show'])->name('build.show');
