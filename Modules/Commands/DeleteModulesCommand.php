<?php

namespace Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DeleteModulesCommand extends Command
{
    protected $signature = 'delete:modules';
    protected $description = 'Batch delete modules listed in docs/DeleteModules.txt';

    public function handle(): int
    {
        $filePath = base_path('docs/DeleteModules.txt');

        if (!file_exists($filePath)) {
            $this->error('docs/DeleteModules.txt not found.');
            return 1;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (
                $trimmed === '' ||
                str_starts_with($trimmed, '//') ||
                str_starts_with($trimmed, '/*') ||
                str_starts_with($trimmed, '|') ||
                str_starts_with($trimmed, '*') ||
                str_starts_with($trimmed, '*/')
            ) {
                continue;
            }

            // Route to the correct artisan command
            if (preg_match('/^php\s+artisan\s+delete:table\s+/', $trimmed)) {
                // Parse: php artisan delete:table {module}
                $moduleName = trim(preg_replace('/^php\s+artisan\s+delete:table\s+/', '', $trimmed));
                if (!$moduleName) continue;

                $this->info("Deleting table: {$moduleName}");
                Artisan::call('delete:table', ['module_name' => $moduleName, '--force' => true]);
                $this->line(Artisan::output());

            } elseif (preg_match('/^php\s+artisan\s+delete:module\s+/', $trimmed)) {
                // Parse: php artisan delete:module {module} [--vue]
                $rest       = preg_replace('/^php\s+artisan\s+delete:module\s+/', '', $trimmed);
                $hasVue     = str_contains($rest, '--vue');
                $moduleName = trim(str_replace('--vue', '', $rest));
                if (!$moduleName) continue;

                $this->info("Deleting: {$moduleName}" . ($hasVue ? ' --vue' : ''));
                $args = ['module_name' => $moduleName, '--force' => true];
                if ($hasVue) $args['--vue'] = true;

                Artisan::call('delete:module', $args);
                $this->line(Artisan::output());
            }
        }

        $this->info('All modules deleted.');
        return 0;
    }
}
