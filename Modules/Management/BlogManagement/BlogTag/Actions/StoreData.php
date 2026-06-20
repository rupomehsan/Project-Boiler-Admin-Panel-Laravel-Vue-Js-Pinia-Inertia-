<?php

namespace Modules\Management\BlogManagement\BlogTag\Actions;

class StoreData
{
    static $model = \Modules\Management\BlogManagement\BlogTag\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();

            // Normalise JSON fields: decode the JSON string from the form
            // so the model cast can re-encode it correctly into the json column.
            
            if ($data = self::$model::query()->create($requestData)) {
                return messageResponse('Item added successfully', $data, 201);
            }
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}