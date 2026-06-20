<?php

use Modules\Management\BlogManagement\BlogTag\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('blog-tags')->middleware('auth:api')->group(function () {
        Route::get('', [Controller::class,'index'])->middleware('permission:blog-tag-view');
        Route::get('{slug}', [Controller::class,'show'])->middleware('permission:blog-tag-details|blog-tag-edit');
        Route::post('store', [Controller::class,'store'])->middleware('permission:blog-tag-create');
        Route::post('update/{slug}', [Controller::class,'update'])->middleware('permission:blog-tag-edit');
        Route::post('update-status', [Controller::class,'updateStatus'])->middleware('permission:blog-tag-edit');
        Route::post('soft-delete', [Controller::class,'softDelete'])->middleware('permission:blog-tag-delete');
        Route::post('destroy/{slug}', [Controller::class,'destroy'])->middleware('permission:blog-tag-delete');
        Route::post('restore', [Controller::class,'restore'])->middleware('permission:blog-tag-delete');
        Route::post('import', [Controller::class,'import'])->middleware('permission:blog-tag-import');
        Route::post('bulk-action', [Controller::class, 'bulkAction'])->middleware('permission:blog-tag-delete');
    });
});