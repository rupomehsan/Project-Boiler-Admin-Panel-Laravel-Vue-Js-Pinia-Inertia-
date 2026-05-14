<?php

namespace Modules\Management\UserManagement\Role\Actions;

class StoreData
{
    static $model = \Modules\Management\UserManagement\Role\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();
            
            if ($data = self::$model::query()->create($requestData)) {
                // Attach permissions if provided
                if ($request->has('permissions')) {
                    $permissions = json_decode($request->permissions, true);
                    if (is_array($permissions) && count($permissions) > 0) {
                        $data->permissions()->sync($permissions);
                    }
                }
                
                return messageResponse('Item added successfully', $data, 201);
            }
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(),[], 500, 'server_error');
        }
    }
}