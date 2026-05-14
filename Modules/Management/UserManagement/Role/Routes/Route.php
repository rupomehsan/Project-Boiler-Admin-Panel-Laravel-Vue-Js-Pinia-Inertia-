<?php

use Modules\Management\UserManagement\Role\Controller\Controller;
use Modules\Management\UserManagement\Role\Controller\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('roles')->middleware('auth:api')->group(function () {
        Route::get('', [Controller::class,'index'])->middleware('permission:role-view');
        Route::get('{slug}', [Controller::class,'show'])->middleware('permission:role-details|role-edit');
        Route::post('store', [Controller::class,'store'])->middleware('permission:role-create');
        Route::post('update/{slug}', [Controller::class,'update'])->middleware('permission:role-edit');
        Route::post('soft-delete', [Controller::class,'softDelete'])->middleware('permission:role-delete');
        Route::post('destroy/{slug}', [Controller::class,'destroy'])->middleware('permission:role-delete');
        Route::post('restore', [Controller::class,'restore'])->middleware('permission:role-delete');
        Route::post('import', [Controller::class,'import'])->middleware('permission:role-import');
        Route::post('bulk-action', [Controller::class, 'bulkAction'])->middleware('permission:role-delete');
        Route::post('update-status', [Controller::class,'updateStatus'])->middleware('permission:role-edit');
    });

    // Permission routes
    Route::prefix('permissions')->middleware(['auth:api', 'permission:role-permission-manage'])->group(function () {
        Route::get('', [PermissionController::class, 'getAllPermissions']);
        Route::get('role/{slug}', [PermissionController::class, 'getRolePermissions']);
        Route::post('role/{slug}/assign', [PermissionController::class, 'assignPermissionsToRole']);
    });
});