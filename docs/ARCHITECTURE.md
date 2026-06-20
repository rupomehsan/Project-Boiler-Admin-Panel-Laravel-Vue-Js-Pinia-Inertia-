# Project Architecture

> **Stack**: Laravel · Vue 3 · TypeScript · Pinia · Inertia.js · Vite

---

## Table of Contents

1. [Overview](#overview)
2. [Directory Structure](#directory-structure)
3. [Backend Architecture](#backend-architecture)
4. [Artisan Code Generator](#artisan-code-generator)
5. [Frontend Architecture](#frontend-architecture)
6. [Shared Pinia Store Factory](#shared-pinia-store-factory)
7. [Module Setup Config](#module-setup-config)
8. [Reactive Form Fields](#reactive-form-fields)
9. [Global Infrastructure](#global-infrastructure)
10. [Existing Modules](#existing-modules)

---

## Overview

This is a **code-generator-driven modular admin panel**. Every feature is a self-contained **Management Module** that is fully scaffolded by a single Artisan command — backend (Laravel) and frontend (Vue 3 + Pinia) together.

```
php artisan make:module BlogManagement/Blog [title:string-150,content:longtext] --vue
```

One command → full-stack CRUD module, fully wired.

---

## Directory Structure

```
project-root/
├── Modules/                          # All backend logic
│   ├── Commands/                     # Artisan generators
│   │   ├── ModelingDirectory.php     # make:module command
│   │   ├── TableModelingCommand.php  # make:table command
│   │   ├── RunModuleCommands.php     # run:modules command
│   │   └── DeleteModuleCommand.php
│   ├── Controllers/
│   │   ├── Backend/BackendController.php   # Inertia SPA shell
│   │   └── Frontend/Auth/AuthController.php
│   ├── Helpers/
│   │   ├── CommandFiles/             # Stub templates for code generator
│   │   │   ├── BackendModule/        # PHP stubs
│   │   │   └── FrontendModule/       # Vue stubs
│   │   └── HelperFiles/              # FileUploader, HelperFunctions, ResponseMessage
│   ├── Mail/OTPSendMail.php
│   ├── Management/                   # All feature modules live here
│   │   ├── Auth/
│   │   ├── Dashboard/
│   │   ├── UserManagement/
│   │   ├── BlogManagement/
│   │   ├── Contact/
│   │   └── ...
│   └── Routes/
│       ├── Backend/
│       │   ├── ApiRoutes.php         # Aggregates all module routes
│       │   └── WebRoutes.php         # Single Inertia entry: GET /admin
│       └── Frontend/
│
├── resources/js/
│   ├── backend/                      # Admin panel SPA
│   │   ├── Config/app_config.ts      # api_host, api_version globals
│   │   ├── GlobalComponents/         # Reusable form & UI components
│   │   ├── GlobalStore/              # auth_store, global_store, site_settings_store
│   │   ├── Views/
│   │   │   ├── Layouts/              # App shell, sidebar, partials
│   │   │   ├── Management/           # All Vue module views live here
│   │   │   └── Routes/routes.js      # Central Vue Router config
│   │   ├── shared/
│   │   │   ├── components/           # all_data_page, canvas, dropdown, meta_component
│   │   │   ├── helpers/              # debounce, export csv, filify, table_active_row
│   │   │   ├── setup/                # setup_type.ts interface
│   │   │   └── store/                # createCrudStore factory + all shared actions
│   │   ├── plugins/                  # axios_setup, sweet_alert, moment, enToBn
│   │   └── utils/                    # kebabize, themeManager
│   └── frontend/                     # Public-facing SPA
│
├── docs/
├── .deploy_tools/                    # VPS deploy scripts
├── vite.config.js
└── package.json
```

---

## Backend Architecture
## Backend Architecture
## Backend Architecture

### Module Folder Shape

Every module under `Modules/Management/{Group}/{Module}/` follows the same structure:

```
{Module}/
├── Actions/
│   ├── GetAllData.php        ← paginated list with search / filter / sort
│   ├── StoreData.php         ← create record (+ file upload handling)
│   ├── UpdateData.php        ← update record (+ file upload handling)
│   ├── GetSingleData.php     ← single record by slug
│   ├── UpdateStatus.php      ← toggle active / inactive
│   ├── SoftDelete.php        ← move to trash
│   ├── DestroyData.php       ← permanent delete
│   ├── RestoreData.php       ← restore from trash
│   ├── ImportData.php        ← CSV import via queue job
│   └── BulkActions.php       ← bulk activate / deactivate / delete / restore
│
├── Controller/
│   └── Controller.php        ← thin controller, delegates to Actions
│
├── Validations/
│   ├── DataStoreValidation.php
│   └── BulkActionsValidation.php
│
├── Database/
│   ├── Migrations/
│   ├── Models/Model.php
│   └── Seeders/Seeder.php
│
├── Routes/Route.php          ← module API routes (auto-included on generation)
│
└── Others/
    ├── Api.http               ← REST client documentation
    ├── ImportJob.php
    └── COMMAND_GUIDE.md
```

### Controller Pattern

Controllers are maximally thin — all logic lives in single-responsibility **Action classes** with a static `execute()` method:

```php
class Controller extends ControllersController
{
    public function index()        { return GetAllData::execute(); }
    public function store($req)    { return StoreData::execute($req); }
    public function show($slug)    { return GetSingleData::execute($slug); }
    public function update($req, $slug) { return UpdateData::execute($req, $slug); }
    public function updateStatus() { return UpdateStatus::execute(); }
    public function softDelete()   { return SoftDelete::execute(); }
    public function destroy($slug) { return DestroyData::execute($slug); }
    public function restore()      { return RestoreData::execute(); }
    public function import()       { return ImportData::execute(); }
    public function bulkAction($req) { return BulkActions::execute($req); }
}
```

### Route Registration

Each module's `Routes/Route.php` is **automatically appended** as an `include_once` line into `Modules/Routes/Backend/ApiRoutes.php` when the module is generated. The single Inertia entry point is:

```php
// Modules/Routes/Backend/WebRoutes.php
Route::get('/admin', [BackendController::class, 'AdminPanel'])->name('admin.dashboard');
```

---

## Artisan Code Generator

### Commands

| Command | Description |
|---|---|
| `php artisan make:module {Group/Module} {[fields]} --vue` | Generate full-stack CRUD module |
| `php artisan make:table {Module} {[fields]}` | Generate only DB migration + model |
| `php artisan run:modules` | Batch-execute commands from `command.txt` |

### `make:module` — What It Generates

```
Step 1  Create PHP directory structure
Step 2  Generate all Action classes
Step 3  Generate Controller, Validations, Model, Migration, Seeder, Routes, Others
Step 4  Run php artisan migrate
Step 5  Run module Seeder
Step 6  Append include_once to Modules/Routes/Backend/ApiRoutes.php
Step 7  Inject permissions into PermissionSeeder → re-seed
──── if --vue flag ────
Step 8  Copy Vue page templates (All, Form, Details, Layout)
Step 9  Generate setup/index.ts, setup/routes.js, setup/form_fields.js
Step 10 Generate store/index.ts (3-line factory call)
Step 11 Append import + child route to resources/js/backend/Views/Routes/routes.js
Step 12 Append sidebar menu entry to Layouts/Partials/Sidebar/Index.vue
```

### Field Type Syntax

```
php artisan make:module Group/Module [field:type-options,...] --vue
```

| Field Syntax | DB Column | Generated Input |
|---|---|---|
| `name:string-150` | `varchar(150)` | `<input type="text">` |
| `desc:text` | `text` | `<textarea>` |
| `body:longtext` | `longtext` | `<textarea>` |
| `qty:integer` | `int` | `<input type="number">` |
| `date:datetime` | `datetime` | `<input type="datetime-local">` |
| `img:image-150` | `varchar(150)` | file input — single image |
| `imgs:images` | `json` | file input — multiple images |
| `type:enum-a.b.c` | `enum('a','b','c')` | `<select>` with 3 options |
| `link:url` | `varchar` | `<input type="url">` |
| `flag:boolean-yes.no` | `tinyint` | `<select>` yes / no |
| `data:json` | `json` | `<textarea>` (JSON) |
| `ref:bigint` | `bigint` FK | `<select>` (empty, fill via `data_list`) |

### Full Example

```bash
php artisan make:module BlogManagement/Blog \
  [blog_category_id:bigint,title:string-150,description:text,content:longtext,
   reading_time:integer,publish_date:datetime,thumbnail_image:image-150,
   images:images,blog_type:enum-news.tutorial.opinion,url:url,
   show_top:boolean-yes.no,contributors:json,is_featured:boolean,is_published:boolean] \
  --vue
```

### Batch Execution

Write multiple `make:module` commands in `command.txt` at the project root, then:

```bash
php artisan run:modules
```

Lines starting with `//`, `/*`, `|`, `*`, `*/` are treated as comments and skipped.

---

## Frontend Architecture
## Frontend Architecture
## Frontend Architecture

### Vue Module Folder Shape

```
resources/js/backend/Views/Management/{Group}/{Module}/
├── pages/
│   ├── All.vue         ← list table — search, filter, sort, export, bulk actions
│   ├── Form.vue        ← create + edit (same component, driven by form_fields.js)
│   ├── Details.vue     ← record detail view / canvas
│   └── Layout.vue      ← module layout wrapper (router-view)
│
├── setup/
│   ├── index.ts        ← module config — single source of truth
│   ├── routes.js       ← Vue Router child routes
│   └── form_fields.js  ← field definitions for Form.vue
│
└── store/
    └── index.ts        ← 3 lines: createCrudStore(setup)
```

### Vue Router Registration

Each module's `setup/routes.js` defines a nested route object:

```js
const routes = {
    path: "contact",        // /admin#/contact
    component: Layout,
    children: [
        { path: "all",          name: "AllContact",     component: All },
        { path: "create",       name: "CreateContact",  component: Form },
        { path: "details/:id",  name: "DetailsContact", component: Details },
        { path: "edit/:id",     name: "EditContact",    component: Form },
    ],
};
```

This is auto-imported and registered in `resources/js/backend/Views/Routes/routes.js`.

---

## Shared Pinia Store Factory

**File**: [resources/js/backend/shared/store/createStore.ts](../resources/js/backend/shared/store/createStore.ts)

### Usage — Every Module Store Is 3 Lines

```ts
import { createCrudStore } from "@/shared/store/createStore";
import setup from "../setup";

export const contact_store = createCrudStore(setup);
```

### What The Factory Provides

**Async Actions** (HTTP calls via Axios):

| Action | Description |
|---|---|
| `get_all()` | Fetch paginated list with current filters |
| `create(event)` | POST new record |
| `update(event)` | PUT/PATCH existing record |
| `details(id)` | GET single record |
| `update_status()` | PATCH status toggle |
| `soft_delete()` | DELETE → trash |
| `restore()` | POST restore from trash |
| `destroy()` | DELETE permanent |
| `bulk_action(action, ids)` | POST bulk operation |
| `import_data(event)` | POST CSV file |

**Sync Actions**:

| Action | Description |
|---|---|
| `set_page(n)` | Change current page |
| `set_paginate(n)` | Change per-page limit |
| `set_status(s)` | Switch active / inactive / trashed |
| `set_filter_criteria(obj)` | Apply filter params |
| `reset_filter_criteria()` | Clear all filters |
| `set_item(obj)` | Set selected item |
| `set_show_details_canvas(bool)` | Open/close details panel |
| `set_show_filter_canvas(bool)` | Open/close filter panel |
| `set_import_csv_modal(bool)` | Open/close import modal |
| `set_only_latest_data(bool)` | Toggle latest-only mode |
| `clear_selected()` | Clear checkbox selections |

**State**:

```ts
{
    // Data
    all: {},               // paginated list response
    item: {},              // single selected record
    is_loading: false,
    url: '',

    // API config (from setup)
    api_host, api_version, api_end_point,

    // Filters
    page: 1,
    paginate: 10,
    search_key: '',
    sort_by_col: 'id',
    sort_type: 'DESC',
    start_date: '',
    end_date: '',
    status: 'active',      // 'active' | 'inactive' | 'trashed'
    filter_criteria: {},
    select_fields: [],
    sort_by_cols: [],

    // Counts
    all_data_count: 0,
    active_data_count: 0,
    inactive_data_count: 0,
    trased_data_count: 0,

    // Selection
    selected: [],

    // UI toggles
    show_filter_canvas: false,
    show_create_canvas: false,
    show_edit_canvas: false,
    show_details_canvas: false,
    show_quick_view_canvas: false,
    import_csv_modal_show: false,

    // Export
    is_exporting: false,
    export_progress: 0,

    // Cache
    cached: 0,
    only_latest_data: false,
}
```

> **Code savings**: Traditional approach ~500+ lines per module. Factory approach: 3 lines. Every module is 100% consistent.

---

## Module Setup Config

**File**: `resources/js/backend/Views/Management/{Group}/{Module}/setup/index.ts`

This is the **single source of truth** for a module. All pages, the store, and the router derive their config from it.

```ts
import app_config from "@config/app_config";
import setup_type from "@/shared/setup/setup_type";

const setup: setup_type = {
    // Identity
    prefix: "Contact",
    module_name: "contact",
    store_prefix: "contact",          // Pinia store ID (must be unique)
    route_prefix: "Contact",          // Route name prefix → AllContact, CreateContact...
    route_path: "contact",            // URL segment  → /admin#/contact/all

    // Permissions
    permission: ["admin", "super_admin"],

    // API
    api_host: app_config.api_host,
    api_version: app_config.api_version,
    api_end_point: "contacts",        // → /api/v1/contacts

    // Table / list config
    select_fields:     ["id", "full_name", "email", "phone", "status", "slug", "created_at"],
    sort_by_cols:      ["id", "full_name", "email", "status", "created_at"],
    table_header_data: ["id", "full_name", "email", "phone", "status", "created_at"],
    table_row_data:    ["id", "full_name", "email", "phone", "status", "created_at"],
    quick_view_data:   ["id", "full_name", "email", "phone", "status", "created_at"],

    // UI labels
    layout_title:        "Contact Management",
    page_title:          "Contact Management",
    all_page_title:      "All Contact",
    create_page_title:   "Create Contact",
    edit_page_title:     "Edit Contact",
    details_page_title:  "Details Contact",
};

export default setup;
```

---

## Reactive Form Fields

### `setup/form_fields.js`

Each field object drives `Form.vue` rendering. To trigger a Vue method when a field changes, set `onchangeAction`:

```js
// setup/form_fields.js
export default [
    {
        name: "category_id",
        label: "Select Category",
        type: "select",
        data_list: [],
        onchangeAction: "loadSubCategories",   // method name in Form.vue
    },
    // ...
]
```

### `Form.vue`

```js
methods: {
    // Generic dispatcher — calls this[actionTitle](...)
    changeAction(actionTitle, event, ref) {
        this[actionTitle](actionTitle, event, ref);
    },

    // Named handler called by the dispatcher
    loadSubCategories(actionTitle, event, ref) {
        // custom logic — e.g. fetch sub-categories based on selected category
    }
}
```

This pattern lets any field trigger any method by name without per-field hardcoded event handlers.

---

## Global Infrastructure

### Backend Helpers

| File | Purpose |
|---|---|
| `Modules/Helpers/HelperFiles/FileUploader.php` | Centralized file/image upload handler |
| `Modules/Helpers/HelperFiles/HelperFunctions.php` | Shared utility functions |
| `Modules/Helpers/HelperFiles/ResponseMessage.php` | Standardized API response format |
| `Modules/Mail/OTPSendMail.php` | OTP email mailable |

### Frontend Global Stores

| Store | Purpose |
|---|---|
| `GlobalStore/auth_store.js` | Authenticated user state |
| `GlobalStore/global_store.js` | App-wide reactive state |
| `GlobalStore/site_settings_store.js` | Website settings from API |

### Frontend Global Components

| Component | Purpose |
|---|---|
| `GlobalComponents/FormComponents/CommonInput.vue` | Universal input field wrapper |
| `GlobalComponents/FormComponents/ImageComponent.vue` | Image upload + preview |
| `GlobalComponents/FormComponents/MultiChipInput.vue` | Tag / chip multi-input |
| `GlobalComponents/FormComponents/SelectInput.vue` | Searchable select dropdown |
| `GlobalComponents/FormComponents/TextEditor.vue` | Rich text editor |
| `GlobalComponents/Pagination.vue` | Paginator component |

### Frontend Shared Utilities

| Path | Purpose |
|---|---|
| `shared/helpers/debounce.js` | Debounce for search inputs |
| `shared/helpers/export_all_csv.js` | Export all records to CSV |
| `shared/helpers/export_selected_csv.js` | Export selected rows to CSV |
| `shared/helpers/export_demo_csv.js` | Export CSV import template |
| `shared/helpers/filify/` | File handling utilities |
| `shared/helpers/table_active_row.ts` | Track active row in table |

### Frontend Plugins

| Plugin | Purpose |
|---|---|
| `plugins/axios_setup.js` | Axios instance + interceptors + CSRF |
| `plugins/sweet_alert.js` | SweetAlert2 global setup |
| `plugins/moment_setup.js` | Moment.js global setup |
| `plugins/enToBn.js` | English to Bangla numeral converter |
| `plugins/number_to_text.js` | Number to words |

### Config

```ts
// resources/js/backend/Config/app_config.ts
export default {
    api_host: "http://127.0.0.1:8000",
    api_version: "api/v1",
}
```

Every module's `setup/index.ts` reads from this — change it once to point to production.

---

## Existing Modules

| Group | Module | API Endpoint |
|---|---|---|
| — | Auth | `/api/v1/auth/*` |
| — | Dashboard | `/api/v1/dashboard` |
| UserManagement | User | `/api/v1/users` |
| UserManagement | Role + Permission | `/api/v1/roles`, `/api/v1/permissions` |
| BlogManagement | Blog | `/api/v1/blogs` |
| BlogManagement | BlogCategory | `/api/v1/blog-categories` |
| BlogManagement | BlogTag | `/api/v1/blog-tags` |
| BlogManagement | BlogWriter | `/api/v1/blog-writers` |
| — | Contact | `/api/v1/contacts` |
| CredentialManagement | Credential | `/api/v1/credentials` |
| PersonalNoteManagement | PersonalNote | `/api/v1/personal-notes` |
| ProductManagement | DigitalProduct | `/api/v1/digital-products` |
| ProjectManagement | Project | `/api/v1/projects` |
| SettingManagement | WebsiteSettings | `/api/v1/website-settings` |
| TodoListManagement | TodoList | `/api/v1/todo-lists` |

---

## Adding a New Module (Quickstart)

```bash
# 1. Generate full-stack module
php artisan make:module ProductManagement/ProductCategory \
  [name:string-150,description:text,thumbnail:image-150,is_active:boolean] \
  --vue

# 2. Done. The following are auto-handled:
#    ✔ Migration created + run
#    ✔ Model, Actions, Controller, Validations created
#    ✔ API route appended to ApiRoutes.php
#    ✔ Permissions seeded
#    ✔ Vue pages created (All, Form, Details, Layout)
#    ✔ Pinia store wired (3 lines, createCrudStore)
#    ✔ Vue Router route registered
#    ✔ Sidebar menu entry added
```

The new module is immediately available at `/admin#/product-category/all`.
