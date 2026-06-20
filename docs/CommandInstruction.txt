
/*
|==========================================================================
| ARTISAN COMMAND REFERENCE
| make:module  ·  make:table  ·  delete:module  ·  delete:modules  ·  run:modules
|==========================================================================
*/


/*
|--------------------------------------------------------------------------
| 1. make:module
|--------------------------------------------------------------------------
| Generates a complete full-stack CRUD module in one command.
| Creates backend (PHP) + optionally frontend (Vue 3 + Pinia).
|--------------------------------------------------------------------------
*/

SYNTAX:
  php artisan make:module {Group/Module} {[fields]} --vue

  {Group/Module}   — Path under Modules/Management/  (e.g. BlogManagement/Blog)
  {[fields]}       — Comma-separated field definitions (see Field Types below)
  --vue            — Also scaffold Vue pages, store, routes, and sidebar entry


WHAT IT GENERATES:
  Backend (always):
    ✔ Modules/Management/{Group}/{Module}/
        Actions/GetAllData.php          – paginated list with search / filter / sort
        Actions/StoreData.php           – create record (handles file uploads)
        Actions/UpdateData.php          – update record (handles file uploads)
        Actions/GetSingleData.php       – single record by slug
        Actions/UpdateStatus.php        – toggle active / inactive
        Actions/SoftDelete.php          – move to trash
        Actions/DestroyData.php         – permanent delete
        Actions/RestoreData.php         – restore from trash
        Actions/ImportData.php          – CSV import via queue job
        Actions/BulkActions.php         – bulk activate / deactivate / delete / restore
        Controller/Controller.php       – thin, delegates all logic to Actions
        Validations/DataStoreValidation.php
        Validations/BulkActionsValidation.php
        Database/Migrations/create_{table}_table.php
        Database/Models/Model.php
        Database/Seeders/Seeder.php
        Routes/Route.php
        Others/Api.http                 – REST client docs
        Others/ImportJob.php
        Others/COMMAND_GUIDE.md
    ✔ Migration runs automatically (php artisan migrate)
    ✔ Seeder runs automatically (php artisan db:seed)
    ✔ Route include appended to Modules/Routes/Backend/ApiRoutes.php
    ✔ Permissions injected into PermissionSeeder → re-seeded automatically

  Frontend (--vue flag):
    ✔ resources/js/backend/Views/Management/{Group}/{Module}/
        pages/All.vue                   – list table: search, filter, bulk actions, export
        pages/Form.vue                  – create + edit (driven by form_fields.js)
        pages/Details.vue               – record detail view / canvas
        pages/Layout.vue                – module layout wrapper (router-view)
        setup/index.ts                  – module config (single source of truth)
        setup/routes.js                 – Vue Router child routes
        setup/form_fields.js            – field definitions for Form.vue
        store/index.ts                  – 3-line Pinia store (createCrudStore factory)
    ✔ Import + child route appended to resources/js/backend/Views/Routes/routes.js
    ✔ Sidebar menu entry added to Layouts/Partials/Sidebar/Index.vue


EXAMPLES:

  # Minimal — no fields, backend only
  php artisan make:module Contact

  # Backend only with fields
  php artisan make:module Contact "[full_name:string-150,email:string-150,phone:string-20,message:text]"

  # Full-stack (backend + Vue)
  php artisan make:module Contact "[full_name:string-150,email:string-150,phone:string-20,message:text]" --vue

  # Nested group path
  php artisan make:module BlogManagement/Blog "[title:string-150,content:longtext,thumbnail:image-150,is_published:boolean]" --vue

  # All field types in one command
  php artisan make:module BlogManagement/Blog \
    "[blog_category_id:bigint,title:string-150,description:text,content:longtext,
      reading_time:integer,publish_date:datetime,thumbnail_image:image-150,
      images:images,blog_type:enum-news.tutorial.opinion,url:url,
      show_top:boolean-yes.no,contributors:json,is_featured:boolean,is_published:boolean]" \
    --vue


/*
|--------------------------------------------------------------------------
| FIELD TYPE REFERENCE
|--------------------------------------------------------------------------
*/

  Syntax                     DB Column            Generated Input
  ─────────────────────────────────────────────────────────────────────
  name:string-150            varchar(150)         <input type="text">
  desc:text                  text                 <textarea>
  body:longtext              longtext             <textarea>
  qty:integer                int                  <input type="number">
  date:datetime              datetime             <input type="datetime-local">
  img:image-150              varchar(150)         file input — single image
  imgs:images                json                 file input — multiple images
  type:enum-a.b.c            enum('a','b','c')    <select> with options a, b, c
  link:url                   varchar              <input type="url">
  flag:boolean-yes.no        tinyint              <select> yes / no
  flag:boolean               tinyint              <select> Yes / No
  data:json                  json                 <textarea> (JSON)
  ref:bigint                 bigint               <select> (empty, fill via data_list)

  Notes:
  • enum options are dot-separated:   blog_type:enum-news.tutorial.opinion
  • boolean label override:           show_top:boolean-yes.no  →  options: Yes / No
  • FK/relation syntax:               category_id:bigint{CategoryGroup/Category}
                                      see Section 7 (Relational Data) for full details


/*
|--------------------------------------------------------------------------
| 7. Relational Data  (FK / belongsTo fields)
|--------------------------------------------------------------------------
| Use the {Group/Module} brace AFTER the type — not after the field name.
|
| CORRECT:   product_group_id:bigint{ProductManagement/ProductGroup}
| WRONG:     product_group_id{ProductManagement/ProductGroup}:bigint
|--------------------------------------------------------------------------
*/

SYNTAX:
  field_name:bigint{ParentGroup/ParentModule}

  field_name          — FK column in this table (must end with _id by convention)
  bigint              — DB column type (always bigint for FK)
  {ParentGroup/...}   — Path to the related module under Modules/Management/


WHAT THE BRACE HINT GENERATES (all automatic):

  1. DB MIGRATION  →  bigInteger column (nullable)
     $table->bigInteger('product_group_id')->nullable();

  2. ELOQUENT MODEL  →  belongsTo method (camelCase of field name)
     public function productGroupId()
     {
         return $this->belongsTo(
             \Modules\Management\ProductManagement\ProductGroup\Database\Models\Model::class,
             'product_group_id'
         );
     }

  3. EAGER LOADING  →  $with uses the method name (not the column name)
     $with = ['productGroupId'];   // correct — matches the belongsTo method

  4. FORM FIELD  →  auto-generated as an empty select in setup/form_fields.js
     {
         name: "product_group_id",
         label: "Select Product Group",
         type: "select",
         multiple: false,
         data_list: [],            // populate in Form.vue mounted()
         value: "",
         is_visible: true,
         class: "col-md-6",
     },

  5. SETUP/INDEX.TS  →  FK column name auto-added to select_fields (raw DB column, not method)
     select_fields: ["id", "product_group_id", "name", "status", "slug", "created_at", ...]
     // ->with(['productGroupId']) runs separately and adds the related object to the response


ONLY MANUAL STEP — populate data_list in pages/Form.vue:

    async mounted() {
        const res = await axios.get('/api/v1/product-groups?get_all=1&status=active');
        const field = this.form_fields.find(f => f.name === 'product_group_id');
        if (field) field.data_list = res.data.data.map(g => ({ label: g.name, value: g.id }));
    },


MULTIPLE FK FIELDS — all handled automatically:

  php artisan make:module BlogManagement/Blog \
    "[category_id:bigint{BlogManagement/BlogCategory},
      writer_id:bigint{BlogManagement/BlogWriter},
      title:string-150,content:longtext]" --vue

  Generates automatically:
    $with = ['categoryId', 'writerId'];          ← merged, camelCase method names (for Eloquent)
    form_fields.js: two select fields (empty data_list each)
    setup/index.ts: "category_id", "writer_id" in select_fields  ← raw DB column names


NAMING CONVENTION:

  field name         →  relation method     →  use in $with / select_fields
  ─────────────────────────────────────────────────────────────────────────
  product_group_id   →  productGroupId()    →  "productGroupId"
  category_id        →  categoryId()        →  "categoryId"
  writer_id          →  writerId()          →  "writerId"


/*
|--------------------------------------------------------------------------
| 2. make:table
|--------------------------------------------------------------------------
| Lighter command — creates only a migration + model.
| Use for pivot / helper tables inside an existing module group.
| Does NOT create Actions, Controller, Routes, Seeder, or Vue files.
|--------------------------------------------------------------------------
*/

SYNTAX:
  php artisan make:table {Group/Module} {[fields]}

  {Group/Module}   — Must match an existing group path (e.g. BlogManagement/BlogTag)
  {[fields]}       — Same field syntax as make:module


WHAT IT GENERATES:
    ✔ Modules/Management/{Group}/Database/Migrations/create_{table}_table.php
    ✔ Modules/Management/{Group}/Database/Models/{Module}Model.php
    ✔ Migration runs automatically


EXAMPLES:

  # Simple pivot / lookup table
  php artisan make:table BlogManagement/BlogTag "[name:string-150,slug:string-150]"

  # Pivot table for many-to-many
  php artisan make:table BlogManagement/BlogBlogCategory "[blog_id:bigint,blog_category_id:bigint]"

  # Order sub-table inside an existing product module group
  php artisan make:table ProductManagement/ProductOrder \
    "[product_id:bigint,qty:integer,price:integer,status:enum-pending.paid.cancelled]"


/*
|--------------------------------------------------------------------------
| 3. delete:module
|--------------------------------------------------------------------------
| Exact reverse of make:module.
| Asks for confirmation before making any change.
|--------------------------------------------------------------------------
*/

SYNTAX:
  php artisan delete:module {Group/Module} --vue

  {Group/Module}   — Same path used in make:module
  --vue            — Also remove Vue directory, routes import, and sidebar entry


WHAT IT REMOVES:
  Backend (always):
    ✔ Permissions removed from PermissionSeeder.php
    ✔ DB permission rows deleted (slug prefix match)
    ✔ include_once removed from ApiRoutes.php
    ✔ Database table dropped (DROP TABLE IF EXISTS)
    ✔ Migration record removed from migrations table
    ✔ Modules/Management/{Group}/{Module}/ directory deleted
    ✔ Empty parent group directories cleaned up automatically

  Frontend (--vue flag):
    ✔ Import line removed from routes.js
    ✔ Child route entry removed from routes.js
    ✔ Sidebar menu item removed from Sidebar/Index.vue
    ✔ Empty parent <side-bar-drop-down-menus> block removed if no children remain
    ✔ resources/js/backend/Views/Management/{Group}/{Module}/ deleted
    ✔ Empty parent Vue directories cleaned up automatically


EXAMPLES:

  # Delete backend only
  php artisan delete:module Contact

  # Delete backend + Vue
  php artisan delete:module Contact --vue

  # Delete nested module — backend + Vue
  php artisan delete:module BlogManagement/Blog --vue

  # Delete nested module — backend only (keep Vue files)
  php artisan delete:module BlogManagement/BlogTag


/*
|--------------------------------------------------------------------------
| 4. run:modules  (batch execution)
|--------------------------------------------------------------------------
| Reads command.txt from the project root and runs each make:module line.
| Lines starting with //  /*  |  *  */  are treated as comments (skipped).
|--------------------------------------------------------------------------
*/

SYNTAX:
  php artisan run:modules

COMMAND.TXT FORMAT:

  /*
  |--------------------------------------------------------------------------
  | Blog Management
  |--------------------------------------------------------------------------
  */

  php artisan make:module BlogManagement/BlogCategory "[name:string-150,slug:string-150,description:text,thumbnail:image-150,is_active:boolean]" --vue
  php artisan make:module BlogManagement/BlogTag "[name:string-150,slug:string-150]" --vue
  php artisan make:module BlogManagement/Blog "[blog_category_id:bigint,title:string-150,content:longtext,thumbnail:image-150,is_published:boolean]" --vue


/*
|--------------------------------------------------------------------------
| 5. delete:modules  (batch delete)
|--------------------------------------------------------------------------
| Reads docs/DeleteModules.txt and deletes each module automatically.
| Auto-confirms the prompt — no interaction required.
| Lines starting with //  /*  |  *  */  are treated as comments (skipped).
|--------------------------------------------------------------------------
*/

SYNTAX:
  php artisan delete:modules

DELETMODULES.TXT FORMAT:

  /*
  |--------------------------------------------------------------------------
  | Blog Management
  |--------------------------------------------------------------------------
  */

  php artisan delete:module BlogManagement/Blog --vue
  php artisan delete:module BlogManagement/BlogWriter --vue
  php artisan delete:module BlogManagement/BlogTag --vue
  php artisan delete:module BlogManagement/BlogCategory --vue
  php artisan delete:table BlogManagement/BlogBlogTag

  Note: delete:table handles pivot/helper tables created by make:table.
        It drops the DB table and removes the group-level Database/ files,
        then cleans up the empty BlogManagement/ parent directory.

FILE LOCATION:
  docs/DeleteModules.txt  (relative to project root)


/*
|--------------------------------------------------------------------------
| 6. Reactive Field onChange (Form.vue pattern)
|--------------------------------------------------------------------------
| Trigger a custom Vue method when any field value changes.
|--------------------------------------------------------------------------
*/

  In setup/form_fields.js — add onchangeAction to the field:

    {
        name: "category_id",
        label: "Select Category",
        type: "select",
        data_list: [],
        onchangeAction: "loadSubCategories",   // name of the method in Form.vue
    }

  In pages/Form.vue — add the named method:

    methods: {
        changeAction(actionTitle, event, ref) {
            this[actionTitle](actionTitle, event, ref);   // generic dispatcher
        },

        loadSubCategories(actionTitle, event, ref) {
            // fetch sub-categories based on selected category_id
            // e.g. axios.get('/api/v1/subcategories?category_id=' + event.target.value)
        }
    }

  Any field can call any method by name — no per-field hardcoded event handlers needed.


/*
|--------------------------------------------------------------------------
| 6. Quick Recipes
|--------------------------------------------------------------------------
| Full blog management commands: see docs/command.txt
|--------------------------------------------------------------------------
*/

  // ── Blog Management (all field types — run in order) ──────────────────

  # 1. No dependencies
  php artisan make:module BlogManagement/BlogCategory "[name:string-150,description:text,thumbnail:image-150,color:string-50,sort_order:integer,is_active:boolean]" --vue
  php artisan make:module BlogManagement/BlogTag "[name:string-150,color:color,sort_order:integer,is_active:boolean]" --vue
  php artisan make:module BlogManagement/BlogWriter "[name:string-150,email:string-150,phone:string-20,bio:text,avatar:image-150,website:url,facebook_url:url,twitter_url:url,linkedin_url:url,is_active:boolean]" --vue

  # 2. Blog — depends on BlogCategory + BlogWriter (all remaining field types)
  php artisan make:module BlogManagement/Blog "[blog_category_id:bigint{BlogManagement/BlogCategory},writer_id:bigint{BlogManagement/BlogWriter},title:string-200,short_description:text,content:longtext,reading_time:integer,average_rating:decimal,publish_date:date,scheduled_at:datetime,thumbnail_image:image-150,gallery:images,blog_type:enum-news.tutorial.opinion.review.case_study,content_format:enum-article.video.podcast.infographic,external_url:url,show_on_top:boolean-yes.no,allow_comments:boolean-yes.no,is_featured:boolean,is_published:boolean,video_link:string-200,meta_title:string-200,meta_description:text,meta_keywords:json]" --vue

  # 3. Pivot table (Blog ↔ BlogTag many-to-many)
  php artisan make:table BlogManagement/BlogBlogTag "[blog_id:bigint,blog_tag_id:bigint]"

  // ── Delete (reverse order) ─────────────────────────────────────────────
  php artisan delete:module BlogManagement/Blog --vue
  php artisan delete:module BlogManagement/BlogWriter --vue
  php artisan delete:module BlogManagement/BlogTag --vue
  php artisan delete:module BlogManagement/BlogCategory --vue


  // ── Product Management ─────────────────────────────────────────────────
  php artisan make:module ProductManagement/ProductCategory "[name:string-200,description:text,thumbnail:image-150,sort_order:integer,is_active:boolean]" --vue
  php artisan make:module ProductManagement/Product "[category_id:bigint{ProductManagement/ProductCategory},name:string-200,description:text,price:decimal,discount_price:decimal,thumbnail:image-150,images:images,status:enum-active.inactive.out_of_stock]" --vue
  php artisan make:table ProductManagement/ProductOrder "[product_id:bigint,qty:integer,total_price:decimal,payment_status:enum-pending.paid.failed,trx_id:string-150]"
