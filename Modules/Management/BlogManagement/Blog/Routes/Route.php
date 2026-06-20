<?php

use Modules\Management\BlogManagement\Blog\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('blogs')->middleware('auth:api')->group(function () {
        Route::get('', [Controller::class,'index'])->middleware('permission:blog-view');
        Route::get('{slug}', [Controller::class,'show'])->middleware('permission:blog-details|blog-edit');
        Route::post('store', [Controller::class,'store'])->middleware('permission:blog-create');
        Route::post('update/{slug}', [Controller::class,'update'])->middleware('permission:blog-edit');
        Route::post('update-status', [Controller::class,'updateStatus'])->middleware('permission:blog-edit');
        Route::post('soft-delete', [Controller::class,'softDelete'])->middleware('permission:blog-delete');
        Route::post('destroy/{slug}', [Controller::class,'destroy'])->middleware('permission:blog-delete');
        Route::post('restore', [Controller::class,'restore'])->middleware('permission:blog-delete');
        Route::post('import', [Controller::class,'import'])->middleware('permission:blog-import');
        Route::post('bulk-action', [Controller::class, 'bulkAction'])->middleware('permission:blog-delete');
    });
});