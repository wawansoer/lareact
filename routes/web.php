<?php

use App\Http\Controllers\Auth\GoogleSignInController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Google Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/auth/google/redirect', [GoogleSignInController::class, 'redirect'])
    ->name('google.redirect');
Route::get('/auth/google/callback', [GoogleSignInController::class, 'callback'])
    ->name('google.callback');

/*
|--------------------------------------------------------------------------
| Another Route
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
require __DIR__.'/rbac.php';
