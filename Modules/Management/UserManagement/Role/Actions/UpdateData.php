<?php

namespace Modules\Management\UserManagement\Role\Actions;

class UpdateData
{
    static $model = \Modules\Management\UserManagement\Role\Database\Models\Model::class;

    public static function execute($request,$slug)
    {
        try {
            if (!$data = self::$model::query()->where('slug', $slug)->first()) {
                return messageResponse('Data not found...',$data, 404, 'error');
            }
            $requestData = $request->validated();
            $data->update($requestData);
            
            // Update permissions if provided
            if ($request->has('permissions')) {
                $permissions = json_decode($request->permissions, true);
                if (is_array($permissions)) {
                    $data->permissions()->sync($permissions);
                } else {
                    $data->permissions()->sync([]);
                }
            }
            
            return messageResponse('Item updated successfully',$data, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(),[], 500, 'server_error');
        }
    }
}