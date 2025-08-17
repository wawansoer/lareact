<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User & RBAC Management
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(
    function () {
        Route::resource('users', UserController::class);
        Route::post('users/bulk-delete', [UserController::class, 'bulkDestroy'])
            ->name('users.bulk-delete');

        Route::resource('tenants', TenantController::class);
        Route::post('tenants/bulk-delete', [TenantController::class, 'bulkDestroy'])
            ->name('tenants.bulk-delete');

        Route::resource('roles', RoleController::class);
        Route::post('roles/bulk-delete', [RoleController::class, 'bulkDestroy'])
            ->name('roles.bulk-delete');

        Route::resource('permissions', PermissionController::class);
        Route::post('permissions/bulk-delete', [PermissionController::class, 'bulkDestroy'])
            ->name('permissions.bulk-delete');
    }
);
