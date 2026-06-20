<?php

namespace Modules\Management\BlogManagement\Blog\Actions;

class StoreData
{
    static $model = \Modules\Management\BlogManagement\Blog\Database\Models\Model::class;

    public static function execute($request)
    {
        try {
            $requestData = $request->validated();

            // Process file uploads
                            if ($request->hasFile('thumbnail_image')) {
                    $requestData['thumbnail_image'] = uploader($request->file('thumbnail_image'), 'uploads/BlogManagement/Blog');
                }
                if ($request->hasFile('gallery')) {
                    $paths = [];
                    foreach ($request->file('gallery') as $file) {
                        $paths[] = uploader($file, 'uploads/BlogManagement/Blog');
                    }
                    $requestData['gallery'] = json_encode($paths);
                }

            // Normalise JSON fields: decode the JSON string from the form
            // so the model cast can re-encode it correctly into the json column.
                            if (isset($requestData['meta_keywords']) && is_string($requestData['meta_keywords'])) {
                    $decoded = json_decode($requestData['meta_keywords'], true);
                    if (json_last_error() === JSON_ERROR_NONE) $requestData['meta_keywords'] = $decoded;
                }

            if ($data = self::$model::query()->create($requestData)) {
                return messageResponse('Item added successfully', $data, 201);
            }
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}