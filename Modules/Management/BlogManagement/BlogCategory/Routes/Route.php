<?php

use Modules\Management\BlogManagement\BlogCategory\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('blog-categories')->middleware('auth:api')->group(function () {
        Route::get('', [Controller::class,'index'])->middleware('permission:blog-category-view');
        Route::get('{slug}', [Controller::class,'show'])->middleware('permission:blog-category-details|blog-category-edit');
        Route::post('store', [Controller::class,'store'])->middleware('permission:blog-category-create');
        Route::post('update/{slug}', [Controller::class,'update'])->middleware('permission:blog-category-edit');
        Route::post('update-status', [Controller::class,'updateStatus'])->middleware('permission:blog-category-edit');
        Route::post('soft-delete', [Controller::class,'softDelete'])->middleware('permission:blog-category-delete');
        Route::post('destroy/{slug}', [Controller::class,'destroy'])->middleware('permission:blog-category-delete');
        Route::post('restore', [Controller::class,'restore'])->middleware('permission:blog-category-delete');
        Route::post('import', [Controller::class,'import'])->middleware('permission:blog-category-import');
        Route::post('bulk-action', [Controller::class, 'bulkAction'])->middleware('permission:blog-category-delete');
    });
});