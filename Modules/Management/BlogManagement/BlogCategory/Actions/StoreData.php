<?php

namespace Modules\Management\BlogManagement\BlogCategory\Actions;

class StoreData
{
    static $model = \Modules\Management\BlogManagement\BlogCategory\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();

            // Process file uploads
                            if ($request->hasFile('thumbnail')) {
                    $requestData['thumbnail'] = uploader($request->file('thumbnail'), 'uploads/BlogManagement/BlogCategory');
                }

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