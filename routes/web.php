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
Route::get('/auth/google/redirect', [GoogleSignInController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleSignInController::class, 'callback'])->name('google.callback');

/*
|--------------------------------------------------------------------------
| User & RBAC Management
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
    Route::delete('permissions/bulk-delete', [\App\Http\Controllers\PermissionController::class, 'bulkDelete'])->name('permissions.bulk-delete');
});

/*
|--------------------------------------------------------------------------
| Another Route
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
