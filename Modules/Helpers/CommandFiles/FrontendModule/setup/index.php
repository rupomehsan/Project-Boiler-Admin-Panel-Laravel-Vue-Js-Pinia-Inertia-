<?php

use Illuminate\Support\Str;

if (!function_exists('SetupIndex')) {
    /**
     * Generate Setup Index Configuration
     *
     * Creates a TypeScript configuration file with module setup.
     * This centralizes all module configuration in one place.
     *
     * @param string $moduleName The module name (can be nested with /)
     * @param array $fields Array of field definitions
     * @return string TypeScript setup configuration
     */
    function SetupIndex($moduleName, $fields)
    {
        // Parse module path
        $formated_module = explode('/', $moduleName);
        $lengthOfFormatedModule = 0;

        if ($formated_module && count($formated_module) > 1) {
            $lengthOfFormatedModule = count($formated_module) - 1;
            $moduleName = end($formated_module);
        }

        // Generate naming conventions
        $prefix = ucfirst($moduleName);
        $moduleName = Str::kebab($moduleName);
        $apiName = Str::plural(Str::kebab($moduleName));
        $store = Str::snake($moduleName);
        $slug = $moduleName; // e.g. "blog", "test"

        // Extract field names, separating FK columns from regular fields
        $form_fields      = [];
        $fk_columns       = [];  // raw DB column names  → select_fields / table_row_data
        $fk_header_labels = [];  // human-readable label → table_header_data
        foreach ($fields as $field) {
            $fieldName = $field[0];
            if (isset($field[1]) && preg_match('/\{.*\}/', $field[1])) {
                $fk_columns[]       = $fieldName;  // "blog_category_id"
                $label              = Str::title(str_replace('_', ' ', preg_replace('/_id$/', '', $fieldName)));
                $fk_header_labels[] = $label;      // "Blog Category"
            } else {
                $form_fields[] = $fieldName;
            }
        }

        // select_fields + table_row_data: raw column names
        // Laravel snake_cases relation keys in JSON, so item['blog_category_id'] IS the relation object
        $all_select  = array_merge($fk_columns, $form_fields);
        // table_header_data: readable labels for FK, raw names for regular fields
        $header_data = array_merge($fk_header_labels, $form_fields);

        // Format field arrays for TypeScript
        $selectFields = implode(",\n            ", array_map(fn($f) => "\"$f\"", $all_select));
        $headerFields = implode(",\n            ", array_map(fn($f) => "\"$f\"", $header_data));
        // table_row_data uses raw column names — Laravel snake_cases relation keys in JSON,
        // so item['blog_category_id'] contains the relation object (overwrites the FK integer).
        // cellValue() already extracts .name from any object.
        $rowFields    = $selectFields;
        $sortByCols   = implode(",\n            ", array_map(fn($f) => "\"$f\"", $form_fields));

        $content = <<<"EOD"
/**
 * {$prefix} Module Setup Configuration
 *
 * This file contains all configuration for the {$prefix} module including:
 * - API endpoints and versioning
 * - Field configurations for tables and forms
 * - Route and permission settings
 * - UI labels and titles
 *
 * Generated automatically - Modifications will be preserved if regenerated
 */

import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const prefix: string = "{$prefix}";

const setup: setup_type = {
    // Module Identity
    prefix,
    module_name: "{$moduleName}",
    store_prefix: "{$store}",
    route_prefix: "{$prefix}",
    route_path: "{$moduleName}",

    // Permission Configuration
    permission: ["admin", "super_admin"],
    permission_slugs: {
        view: "{$slug}-view",
        details: "{$slug}-details",
        create: "{$slug}-create",
        edit: "{$slug}-edit",
        delete: "{$slug}-delete",
        import: "{$slug}-import",
    },

    // API Configuration
    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "{$apiName}",

    // Field Selection for API requests
    select_fields: [
        "id",
        {$selectFields},
        "status",
        "slug",
        "created_at",
        "deleted_at"
    ],

    // Available columns for sorting
    sort_by_cols: [
        "id",
        {$sortByCols},
        "status",
        "created_at",
    ],

    // Table header columns (shown in list view)
    // FK fields use readable labels; regular fields use raw names
    table_header_data: [
        "id",
        {$headerFields},
        "status",
        "created_at",
    ],

    // Table row data fields (rendered in list view)
    // FK fields use camelCase relation key so item[key] returns the eager-loaded object
    // TableBody auto-extracts .name from the object
    table_row_data: [
        "id",
        {$rowFields},
        "status",
        "created_at",
    ],

    // Quick view modal data fields
    quick_view_data: [
        "id",
        {$rowFields},
        "status",
        "created_at",
    ],

    // UI Labels and Titles
    layout_title: prefix + " Management",
    page_title: `\${prefix} Management`,
    all_page_title: "All " + prefix,
    details_page_title: "Details " + prefix,
    create_page_title: "Create " + prefix,
    edit_page_title: "Edit " + prefix,
};

export default setup;

EOD;

        return $content;
    }
}