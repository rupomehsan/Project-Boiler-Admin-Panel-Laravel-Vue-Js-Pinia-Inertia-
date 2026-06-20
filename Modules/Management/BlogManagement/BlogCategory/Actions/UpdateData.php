<?php

namespace Modules\Management\BlogManagement\BlogCategory\Actions;

class UpdateData
{
    static $model = \Modules\Management\BlogManagement\BlogCategory\Database\Models\Model::class;

    public static function execute($request, $slug)
    {
        try {
            if (!$data = self::$model::query()->where('slug', $slug)->first()) {
                return messageResponse('Data not found...', $data, 404, 'error');
            }
            $requestData = $request->validated();

            // Process file uploads
                            if ($request->hasFile('thumbnail')) {
                    $requestData['thumbnail'] = uploader($request->file('thumbnail'), 'uploads/BlogManagement/BlogCategory');
                }

            // Normalise JSON fields: decode the JSON string from the form
            // so the model cast can re-encode it correctly into the json column.
            
            $data->update($requestData);
            return messageResponse('Item updated successfully', $data, 201);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}