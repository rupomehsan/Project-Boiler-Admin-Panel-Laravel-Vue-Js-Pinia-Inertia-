<?php

namespace Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeleteModuleCommand extends Command
{
    protected $signature = 'delete:module {module_name} {[field]?} {--vue}';
    protected $description = 'Reverse of make:module — delete backend/frontend module files and all registrations';

    protected string $moduleName;
    protected string $ViewModuleName;
    protected bool $withVue;
    protected string $baseDirectory;

    public function handle(): int
    {
        $this->initializeProperties();

        $this->warn("About to delete module: {$this->ViewModuleName}");

        if (!$this->confirm('This will permanently delete files and drop the database table. Continue?', true)) {
            $this->info('Aborted.');
            return 0;
        }

        $this->removePermissionsFromSeeder();
        $this->removeRouteFromApiRoutes();
        $this->dropTable();
        $this->deleteBackendDirectory();

        if ($this->withVue) {
            $this->removeFromVueRoutes();
            $this->removeFromSidebar();
            $this->deleteVueDirectory();
        }

        $this->info("Module [{$this->ViewModuleName}] deleted successfully.");
        return 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Init
    // ─────────────────────────────────────────────────────────────────────────

    protected function initializeProperties(): void
    {
        $this->ViewModuleName = $this->argument('module_name');
        $this->withVue        = (bool) $this->option('vue');
        $this->baseDirectory  = base_path('Modules/Management/');

        // $this->moduleName = last segment only (e.g. "Test" from "TestManagement/Test")
        $parts = explode('/', $this->ViewModuleName);
        $this->moduleName = end($parts);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Backend
    // ─────────────────────────────────────────────────────────────────────────

    protected function deleteBackendDirectory(): void
    {
        $path = base_path("Modules/Management/{$this->ViewModuleName}");

        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
            $this->info("Deleted backend directory: {$path}");

            // Delete empty parent directories up to Modules/Management/
            $base   = base_path('Modules/Management');
            $parent = dirname($path);
            while ($parent !== $base && File::isDirectory($parent)) {
                $items = File::files($parent);
                $dirs  = File::directories($parent);
                if (empty($items) && empty($dirs)) {
                    File::deleteDirectory($parent);
                    $this->info("Deleted empty parent directory: {$parent}");
                    $parent = dirname($parent);
                } else {
                    break;
                }
            }
        } else {
            $this->warn("Backend directory not found: {$path}");
        }
    }

    protected function dropTable(): void
    {
        $table = Str::plural(Str::snake($this->moduleName));

        try {
            DB::statement("DROP TABLE IF EXISTS `{$table}`");
            $this->info("Dropped table: {$table}");

            $deleted = DB::table('migrations')
                ->where('migration', 'like', '%create_' . $table . '_table')
                ->delete();

            if ($deleted) {
                $this->info("Removed migration record for [create_{$table}_table].");
            }
        } catch (\Exception $e) {
            $this->warn("Could not drop table [{$table}]: " . $e->getMessage());
        }
    }

    protected function removeRouteFromApiRoutes(): void
    {
        $filePath     = base_path('Modules/Routes/Backend/ApiRoutes.php');
        $routeInclude = "include_once base_path(\"Modules/Management/{$this->ViewModuleName}/Routes/Route.php\");\n";

        if (!File::exists($filePath)) {
            $this->warn('ApiRoutes.php not found.');
            return;
        }

        $content = File::get($filePath);

        if (str_contains($content, $routeInclude)) {
            $content = str_replace($routeInclude, '', $content);
            File::put($filePath, $content);
            $this->info('Removed route include from ApiRoutes.php');
        } else {
            $this->warn('Route include not found in ApiRoutes.php — skipping.');
        }
    }

    protected function removePermissionsFromSeeder(): void
    {
        $seederPath = base_path('Modules/Management/UserManagement/Role/Database/Seeders/PermissionSeeder.php');

        if (!File::exists($seederPath)) {
            $this->warn('PermissionSeeder not found, skipping.');
            return;
        }

        $slug         = Str::kebab($this->moduleName);
        $routeName    = Str::plural(Str::kebab($this->moduleName));
        $displayName  = ucwords(str_replace('-', ' ', $slug));
        $parts        = explode('/', $this->ViewModuleName);
        $categoryName = count($parts) > 1
            ? trim(preg_replace('/([A-Z])/', ' $1', $parts[0]))
            : $displayName . ' Management';

        $content = File::get($seederPath);

        if (!str_contains($content, "'{$slug}-view'")) {
            $this->warn("Permissions for [{$categoryName}] not found in PermissionSeeder — skipping.");
            return;
        }

        $newContent = $content;

        // Strategy 1: marker-based removal (blocks injected by new make:module with [MODULE:X:START/END] markers)
        $startMarker = "// [MODULE:{$categoryName}:START]";
        if (str_contains($content, $startMarker)) {
            $escapedCategory = preg_quote($categoryName, '/');
            $pattern = '/\n[ \t]*\/\/ \[MODULE:' . $escapedCategory . ':START\].*?\/\/ \[MODULE:' . $escapedCategory . ':END\]\n?/s';
            $newContent = preg_replace($pattern, '', $content);
        } else {
            // Strategy 2: exact string reconstruction for blocks injected by old make:module (no markers)
            $block = <<<PHP

            // {$categoryName}
            '{$categoryName}' => [
                ['name' => 'View {$displayName}', 'slug' => '{$slug}-view', 'route' => '/admin#/{$slug}/all'],
                ['name' => 'View {$displayName} Details', 'slug' => '{$slug}-details', 'route' => '/admin#/{$slug}/details'],
                ['name' => 'Create {$displayName}', 'slug' => '{$slug}-create', 'route' => '/admin#/{$slug}/create'],
                ['name' => 'Edit {$displayName}', 'slug' => '{$slug}-edit', 'route' => '/admin#/{$slug}/edit'],
                ['name' => 'Delete {$displayName}', 'slug' => '{$slug}-delete', 'route' => '/api/v1/{$routeName}/destroy'],
                ['name' => 'Import {$displayName}', 'slug' => '{$slug}-import', 'route' => '/api/v1/{$routeName}/import'],
            ],
PHP;
            $newContent = str_replace($block, '', $content);
        }

        if ($newContent === $content) {
            $this->warn("Could not remove [{$categoryName}] from PermissionSeeder — remove manually.");
            return;
        }

        File::put($seederPath, $newContent);
        $this->info("Removed [{$categoryName}] permissions from PermissionSeeder.");

        // Delete permissions from DB that match these slugs
        try {
            $permModel = \Modules\Management\UserManagement\Role\Database\Models\Permission::class;
            $permModel::where('slug', 'like', $slug . '-%')->delete();
            $this->info("Deleted DB permissions with slug prefix [{$slug}-].");
        } catch (\Exception $e) {
            $this->warn('Could not delete DB permissions: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Vue / Frontend
    // ─────────────────────────────────────────────────────────────────────────

    protected function deleteVueDirectory(): void
    {
        $path = resource_path("js/backend/Views/Management/{$this->ViewModuleName}");

        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
            $this->info("Deleted Vue directory: {$path}");

            // Delete empty parent directories up to Views/Management/
            $base   = resource_path('js/backend/Views/Management');
            $parent = dirname($path);
            while ($parent !== $base && File::isDirectory($parent)) {
                $items = File::files($parent);
                $dirs  = File::directories($parent);
                if (empty($items) && empty($dirs)) {
                    File::deleteDirectory($parent);
                    $this->info("Deleted empty Vue parent directory: {$parent}");
                    $parent = dirname($parent);
                } else {
                    break;
                }
            }
        } else {
            $this->warn("Vue directory not found: {$path}");
        }
    }

    protected function removeFromVueRoutes(): void
    {
        $filePath = base_path('resources/js/backend/Views/Routes/routes.js');

        if (!File::exists($filePath)) {
            $this->warn('Vue routes.js not found.');
            return;
        }

        $parts          = explode('/', $this->ViewModuleName);
        $ViewModuleName = end($parts);                              // "Test"
        $vue_path       = $this->ViewModuleName;                   // "TestManagement/Test"

        $routeImport  = "import {$ViewModuleName}Routes from '../Management/{$vue_path}/setup/routes.js';\n";
        $routeChild   = "        {$ViewModuleName}Routes,\n";

        $content = File::get($filePath);
        $content = str_replace($routeImport, '', $content);
        $content = str_replace($routeChild, '', $content);

        File::put($filePath, $content);
        $this->info('Removed Vue route import and child from routes.js');
    }

    protected function removeFromSidebar(): void
    {
        $filePath = base_path('resources/js/backend/Views/Layouts/Partials/Sidebar/Index.vue');

        if (!File::exists($filePath)) {
            $this->warn('Sidebar Index.vue not found.');
            return;
        }

        $parts   = explode('/', $this->ViewModuleName);
        $content = File::get($filePath);

        if (count($parts) > 1) {
            $parent     = ucwords($parts[0]);                          // "TestManagement"
            $child      = ucwords(end($parts));                        // "Test"
            $menuRoute  = "All{$child}";

            // Remove the menu item object from the parent dropdown's :menus array
            // Matches: optional comma before/after + the { route_name: `AllTest`, ... } block
            $itemPattern = '/,?\s*\{\s*\n\s*route_name:\s*`' . preg_quote($menuRoute, '/') . '`[^}]*\},?/s';
            $newContent  = preg_replace($itemPattern, '', $content);

            if ($newContent !== $content) {
                // If the parent <side-bar-drop-down-menus> :menus="[]" is now empty, remove whole block
                $escapedParent = preg_quote($parent, '/');
                $emptyMenuPattern = '/<side-bar-drop-down-menus[^>]*:menu_title="`' . $escapedParent . '`"[^>]*:menus="\[\s*\]"[^\/]*\/>\n?/s';
                $newContent = preg_replace($emptyMenuPattern, '', $newContent);

                File::put($filePath, $newContent);
                $this->info("Removed [{$child}] from sidebar under [{$parent}].");
            } else {
                $this->warn("Could not find sidebar menu item [{$menuRoute}] — remove manually.");
            }
        } else {
            // Standalone <side-bar-single-menu> entry
            $menuRoute  = "All{$this->ViewModuleName}";
            $linePattern = '/<side-bar-single-menu[^\/]*:route_name="`' . preg_quote($menuRoute, '/') . '`"[^\/]*\/>\n?/';
            $newContent  = preg_replace($linePattern, '', $content);

            if ($newContent !== $content) {
                File::put($filePath, $newContent);
                $this->info("Removed standalone sidebar menu [{$menuRoute}].");
            } else {
                $this->warn("Could not find standalone sidebar entry [{$menuRoute}] — remove manually.");
            }
        }
    }
}
