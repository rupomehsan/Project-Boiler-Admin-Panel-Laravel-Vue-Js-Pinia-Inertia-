/**
 * Blog Module Setup Configuration
 *
 * This file contains all configuration for the Blog module including:
 * - API endpoints and versioning
 * - Field configurations for tables and forms
 * - Route and permission settings
 * - UI labels and titles
 *
 * Generated automatically - Modifications will be preserved if regenerated
 */

import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const prefix: string = "Blog";

const setup: setup_type = {
    // Module Identity
    prefix,
    module_name: "blog",
    store_prefix: "blog",
    route_prefix: "Blog",
    route_path: "blog",

    // Permission Configuration
    permission: ["admin", "super_admin"],
    permission_slugs: {
        view: "blog-view",
        details: "blog-details",
        create: "blog-create",
        edit: "blog-edit",
        delete: "blog-delete",
        import: "blog-import",
    },

    // API Configuration
    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "blogs",

    // Field Selection for API requests
    select_fields: [
        "id",
        "blog_category_id",
            "writer_id",
            "title",
            "short_description",
            "content",
            "reading_time",
            "average_rating",
            "publish_date",
            "scheduled_at",
            "thumbnail_image",
            "gallery",
            "blog_type",
            "content_format",
            "external_url",
            "show_on_top",
            "allow_comments",
            "is_featured",
            "is_published",
            "video_link",
            "meta_title",
            "meta_description",
            "meta_keywords",
        "status",
        "slug",
        "created_at",
        "deleted_at"
    ],

    // Available columns for sorting
    sort_by_cols: [
        "id",
        "title",
            "short_description",
            "content",
            "reading_time",
            "average_rating",
            "publish_date",
            "scheduled_at",
            "thumbnail_image",
            "gallery",
            "blog_type",
            "content_format",
            "external_url",
            "show_on_top",
            "allow_comments",
            "is_featured",
            "is_published",
            "video_link",
            "meta_title",
            "meta_description",
            "meta_keywords",
        "status",
        "created_at",
    ],

    // Table header columns (shown in list view)
    // FK fields use readable labels; regular fields use raw names
    table_header_data: [
        "id",
        "Blog Category",
            "Writer",
            "title",
            "short_description",
            "content",
            "reading_time",
            "average_rating",
            "publish_date",
            "scheduled_at",
            "thumbnail_image",
            "gallery",
            "blog_type",
            "content_format",
            "external_url",
            "show_on_top",
            "allow_comments",
            "is_featured",
            "is_published",
            "video_link",
            "meta_title",
            "meta_description",
            "meta_keywords",
        "status",
        "created_at",
    ],

    // Table row data fields (rendered in list view)
    // FK fields use camelCase relation key so item[key] returns the eager-loaded object
    // TableBody auto-extracts .name from the object
    table_row_data: [
        "id",
        "blog_category_id",
            "writer_id",
            "title",
            "short_description",
            "content",
            "reading_time",
            "average_rating",
            "publish_date",
            "scheduled_at",
            "thumbnail_image",
            "gallery",
            "blog_type",
            "content_format",
            "external_url",
            "show_on_top",
            "allow_comments",
            "is_featured",
            "is_published",
            "video_link",
            "meta_title",
            "meta_description",
            "meta_keywords",
        "status",
        "created_at",
    ],

    // Quick view modal data fields
    quick_view_data: [
        "id",
        "blog_category_id",
            "writer_id",
            "title",
            "short_description",
            "content",
            "reading_time",
            "average_rating",
            "publish_date",
            "scheduled_at",
            "thumbnail_image",
            "gallery",
            "blog_type",
            "content_format",
            "external_url",
            "show_on_top",
            "allow_comments",
            "is_featured",
            "is_published",
            "video_link",
            "meta_title",
            "meta_description",
            "meta_keywords",
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
