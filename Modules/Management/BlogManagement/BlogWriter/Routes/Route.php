<?php

use Modules\Management\BlogManagement\BlogWriter\Controller\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('blog-writers')->middleware('auth:api')->group(function () {
        Route::get('', [Controller::class,'index'])->middleware('permission:blog-writer-view');
        Route::get('{slug}', [Controller::class,'show'])->middleware('permission:blog-writer-details|blog-writer-edit');
        Route::post('store', [Controller::class,'store'])->middleware('permission:blog-writer-create');
        Route::post('update/{slug}', [Controller::class,'update'])->middleware('permission:blog-writer-edit');
        Route::post('update-status', [Controller::class,'updateStatus'])->middleware('permission:blog-writer-edit');
        Route::post('soft-delete', [Controller::class,'softDelete'])->middleware('permission:blog-writer-delete');
        Route::post('destroy/{slug}', [Controller::class,'destroy'])->middleware('permission:blog-writer-delete');
        Route::post('restore', [Controller::class,'restore'])->middleware('permission:blog-writer-delete');
        Route::post('import', [Controller::class,'import'])->middleware('permission:blog-writer-import');
        Route::post('bulk-action', [Controller::class, 'bulkAction'])->middleware('permission:blog-writer-delete');
    });
});