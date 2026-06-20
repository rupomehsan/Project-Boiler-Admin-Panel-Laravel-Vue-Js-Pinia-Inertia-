<?php

use Illuminate\Support\Str;

if (!function_exists('UpdateData')) {
    function UpdateData($moduleName, $singleFileFields, $multiFileFields, $hasFileUploads = false, $jsonFields = [])
    {
        $formated_module = explode('/', $moduleName);

        if (count($formated_module) > 1) {
            $moduleName        = implode('/', $formated_module);
            $moduleNameForFile = Str::replace("/", "/", $moduleName);
            $moduleName        = Str::replace("/", "\\", $moduleName);
        } else {
            $moduleName        = Str::replace("/", "\\", $moduleName);
            $moduleNameForFile = Str::replace("/", "/", $moduleName);
        }

        // json fields: the form sends a JSON string (from MultiChipInput/DynamicRepeater).
        // Decode it to a PHP value so the model's 'json'/'array' cast re-encodes it cleanly.
        $jsonNormalizeCode = '';
        foreach ($jsonFields as $field) {
            $jsonNormalizeCode .= "                if (isset(\$requestData['{$field}']) && is_string(\$requestData['{$field}'])) {\n";
            $jsonNormalizeCode .= "                    \$decoded = json_decode(\$requestData['{$field}'], true);\n";
            $jsonNormalizeCode .= "                    if (json_last_error() === JSON_ERROR_NONE) \$requestData['{$field}'] = \$decoded;\n";
            $jsonNormalizeCode .= "                }\n";
        }

        if ($hasFileUploads) {
            $fileUploadCode = '';

            // Single-file fields: image / file
            foreach ($singleFileFields as $field) {
                $fileUploadCode .= "                if (\$request->hasFile('{$field}')) {\n";
                $fileUploadCode .= "                    \$requestData['{$field}'] = uploader(\$request->file('{$field}'), 'uploads/{$moduleNameForFile}');\n";
                $fileUploadCode .= "                }\n";
            }

            // Multi-file fields: images — loop, upload each, store as JSON array
            foreach ($multiFileFields as $field) {
                $fileUploadCode .= "                if (\$request->hasFile('{$field}')) {\n";
                $fileUploadCode .= "                    \$paths = [];\n";
                $fileUploadCode .= "                    foreach (\$request->file('{$field}') as \$file) {\n";
                $fileUploadCode .= "                        \$paths[] = uploader(\$file, 'uploads/{$moduleNameForFile}');\n";
                $fileUploadCode .= "                    }\n";
                $fileUploadCode .= "                    \$requestData['{$field}'] = json_encode(\$paths);\n";
                $fileUploadCode .= "                }\n";
            }

            $content = <<<"EOD"
            <?php

            namespace Modules\\Management\\{$moduleName}\\Actions;

            class UpdateData
            {
                static \$model = \Modules\\Management\\{$moduleName}\\Database\\Models\\Model::class;

                public static function execute(\$request, \$slug)
                {
                    try {
                        if (!\$data = self::\$model::query()->where('slug', \$slug)->first()) {
                            return messageResponse('Data not found...', \$data, 404, 'error');
                        }
                        \$requestData = \$request->validated();

                        // Process file uploads
                        {$fileUploadCode}
                        // Normalise JSON fields: decode the JSON string from the form
                        // so the model cast can re-encode it correctly into the json column.
                        {$jsonNormalizeCode}
                        \$data->update(\$requestData);
                        return messageResponse('Item updated successfully', \$data, 201);
                    } catch (\Exception \$e) {
                        return messageResponse(\$e->getMessage(), [], 500, 'server_error');
                    }
                }
            }
            EOD;
        } else {
            $content = <<<"EOD"
            <?php

            namespace Modules\\Management\\{$moduleName}\\Actions;

            class UpdateData
            {
                static \$model = \Modules\\Management\\{$moduleName}\\Database\\Models\\Model::class;

                public static function execute(\$request, \$slug)
                {
                    try {
                        if (!\$data = self::\$model::query()->where('slug', \$slug)->first()) {
                            return messageResponse('Data not found...', \$data, 404, 'error');
                        }
                        \$requestData = \$request->validated();

                        // Normalise JSON fields: decode the JSON string from the form
                        // so the model cast can re-encode it correctly into the json column.
                        {$jsonNormalizeCode}
                        \$data->update(\$requestData);
                        return messageResponse('Item updated successfully', \$data, 201);
                    } catch (\Exception \$e) {
                        return messageResponse(\$e->getMessage(), [], 500, 'server_error');
                    }
                }
            }
            EOD;
        }

        return $content;
    }
}
