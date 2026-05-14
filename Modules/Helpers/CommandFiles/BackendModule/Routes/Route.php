<?php

use Illuminate\Support\Str;

if (!function_exists('RouteContent')) {
    function RouteContent(string $moduleName, string $class_name)
    {
        $formated_module = explode('/', $moduleName);

        if (count($formated_module) > 1) {
            $moduleName = implode('/', $formated_module);
            $moduleName = Str::replace("/", "\\", $moduleName);
        } else {
            $moduleName = Str::replace("/", "\\", $moduleName);
        }

        $route_name = Str::plural(Str::kebab($class_name));
        $slug = Str::kebab($class_name); // e.g. "test", "blog"

        $content = <<<"EOD"
            <?php

            use Modules\\Management\\{$moduleName}\\Controller\\Controller;
            use Illuminate\Support\Facades\Route;

            Route::prefix('v1')->group(function () {
                Route::prefix('{$route_name}')->middleware('auth:api')->group(function () {
                    Route::get('', [Controller::class,'index'])->middleware('permission:{$slug}-view');
                    Route::get('{slug}', [Controller::class,'show'])->middleware('permission:{$slug}-details|{$slug}-edit');
                    Route::post('store', [Controller::class,'store'])->middleware('permission:{$slug}-create');
                    Route::post('update/{slug}', [Controller::class,'update'])->middleware('permission:{$slug}-edit');
                    Route::post('update-status', [Controller::class,'updateStatus'])->middleware('permission:{$slug}-edit');
                    Route::post('soft-delete', [Controller::class,'softDelete'])->middleware('permission:{$slug}-delete');
                    Route::post('destroy/{slug}', [Controller::class,'destroy'])->middleware('permission:{$slug}-delete');
                    Route::post('restore', [Controller::class,'restore'])->middleware('permission:{$slug}-delete');
                    Route::post('import', [Controller::class,'import'])->middleware('permission:{$slug}-import');
                    Route::post('bulk-action', [Controller::class, 'bulkAction'])->middleware('permission:{$slug}-delete');
                });
            });
            EOD;

        return $content;
    }
}
