/**
 * BlogWriter Module Setup Configuration
 *
 * This file contains all configuration for the BlogWriter module including:
 * - API endpoints and versioning
 * - Field configurations for tables and forms
 * - Route and permission settings
 * - UI labels and titles
 *
 * Generated automatically - Modifications will be preserved if regenerated
 */

import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const prefix: string = "BlogWriter";

const setup: setup_type = {
    // Module Identity
    prefix,
    module_name: "blog-writer",
    store_prefix: "blog-writer",
    route_prefix: "BlogWriter",
    route_path: "blog-writer",

    // Permission Configuration
    permission: ["admin", "super_admin"],
    permission_slugs: {
        view: "blog-writer-view",
        details: "blog-writer-details",
        create: "blog-writer-create",
        edit: "blog-writer-edit",
        delete: "blog-writer-delete",
        import: "blog-writer-import",
    },

    // API Configuration
    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "blog-writers",

    // Field Selection for API requests
    select_fields: [
        "id",
        "name",
            "email",
            "phone",
            "bio",
            "avatar",
            "website",
            "facebook_url",
            "twitter_url",
            "linkedin_url",
            "is_active",
        "status",
        "slug",
        "created_at",
        "deleted_at"
    ],

    // Available columns for sorting
    sort_by_cols: [
        "id",
        "name",
            "email",
            "phone",
            "bio",
            "avatar",
            "website",
            "facebook_url",
            "twitter_url",
            "linkedin_url",
            "is_active",
        "status",
        "created_at",
    ],

    // Table header columns (shown in list view)
    // FK fields use readable labels; regular fields use raw names
    table_header_data: [
        "id",
        "name",
            "email",
            "phone",
            "bio",
            "avatar",
            "website",
            "facebook_url",
            "twitter_url",
            "linkedin_url",
            "is_active",
        "status",
        "created_at",
    ],

    // Table row data fields (rendered in list view)
    // FK fields use camelCase relation key so item[key] returns the eager-loaded object
    // TableBody auto-extracts .name from the object
    table_row_data: [
        "id",
        "name",
            "email",
            "phone",
            "bio",
            "avatar",
            "website",
            "facebook_url",
            "twitter_url",
            "linkedin_url",
            "is_active",
        "status",
        "created_at",
    ],

    // Quick view modal data fields
    quick_view_data: [
        "id",
        "name",
            "email",
            "phone",
            "bio",
            "avatar",
            "website",
            "facebook_url",
            "twitter_url",
            "linkedin_url",
            "is_active",
        "status",
        "created_at",
    ],

    // UI Labels and Titles
    layout_title: prefix + " Management",
    page_title: `${prefix} Management`,
    all_page_title: "All " + prefix,
    details_page_title: "Details " + prefix,
    create_page_title: "Create " + prefix,
    edit_page_title: "Edit " + prefix,
};

export default setup;
