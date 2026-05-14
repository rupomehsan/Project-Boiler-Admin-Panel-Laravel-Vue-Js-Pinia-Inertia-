export default interface RouteConfig {
  permission: string[];
  permission_slugs?: {
    view?: string;
    details?: string;
    create?: string;
    edit?: string;
    delete?: string;
    import?: string;
    manage_permission?: string;
  };

  prefix: string;
  module_name: string;
  route_prefix: string;
  store_prefix: string;
  route_path: string;

  api_host: string;
  api_version: string;
  api_end_point: string;

  select_fields: string[];
  sort_by_cols: string[];
  table_header_data: string[];
  table_row_data: string[];
  quick_view_data: string[];

  layout_title: string;

  page_title: string;
  all_page_title: string;
  details_page_title: string;
  create_page_title: string;
  edit_page_title: string;
}
