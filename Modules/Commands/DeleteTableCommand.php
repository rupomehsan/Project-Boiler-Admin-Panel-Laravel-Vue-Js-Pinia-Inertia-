<?php

namespace Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeleteTableCommand extends Command
{
    protected $signature = 'delete:table {module_name} {--force}';
    protected $description = 'Reverse of make:table — drop the table and remove group-level Database files';

    public function handle(): int
    {
        $viewModuleName = $this->argument('module_name'); // e.g. BlogManagement/BlogBlogTag

        if (!$this->option('force') && !$this->confirm("Drop table for [{$viewModuleName}] and delete its files?", true)) {
            $this->info('Aborted.');
            return 0;
        }

        $parts  = explode('/', $viewModuleName);
        $module = end($parts);           // BlogBlogTag
        $group  = $parts[0];             // BlogManagement (always the first segment)
        $table  = Str::plural(Str::snake($module)); // blog_blog_tags

        // 1. Drop table
        try {
            DB::statement("DROP TABLE IF EXISTS `{$table}`");
            $this->info("Dropped table: {$table}");

            DB::table('migrations')
                ->where('migration', 'like', '%create_' . $table . '_table')
                ->delete();
        } catch (\Exception $e) {
            $this->warn("Could not drop table [{$table}]: " . $e->getMessage());
        }

        // 2. Delete migration file from group-level Database/Migrations/
        $groupBase  = base_path("Modules/Management/{$group}");
        $migFile    = "{$groupBase}/Database/Migrations/create_{$table}_table.php";
        $modelFile  = "{$groupBase}/Database/Models/{$module}Model.php";

        foreach ([$migFile, $modelFile] as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->info("Deleted: {$file}");
            }
        }

        // 3. Clean up empty Database sub-directories and the group directory itself
        $this->cleanEmptyDirectory("{$groupBase}/Database/Migrations");
        $this->cleanEmptyDirectory("{$groupBase}/Database/Models");
        $this->cleanEmptyDirectory("{$groupBase}/Database");
        $this->cleanEmptyDirectory($groupBase);

        $this->info("Table [{$viewModuleName}] deleted successfully.");
        return 0;
    }

    protected function cleanEmptyDirectory(string $dir): void
    {
        if (!File::isDirectory($dir)) return;

        $contents = array_diff(scandir($dir), ['.', '..']);
        if (empty($contents)) {
            File::deleteDirectory($dir);
            $this->info("Deleted empty directory: {$dir}");
        }
    }
}
