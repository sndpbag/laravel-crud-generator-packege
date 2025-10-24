<?php

namespace Sndpbag\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DeleteCrudCommand extends Command
{
    protected $signature = 'crud:delete {name} {--force : Skip confirmation}';
    protected $description = 'Delete all files generated for a CRUD';

    protected $modelName;
    protected $namespace;
    protected $deletedFiles = [];

    public function handle()
    {
        $this->parseModelName();

        if (!$this->option('force')) {
            if (!$this->confirm("⚠️  Are you sure you want to delete all files for '{$this->modelName}'? This action cannot be undone!")) {
                $this->info('Deletion cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('🗑️  Deleting CRUD files...');
        $this->newLine();

        // Delete Model
        $this->deleteModel();

        // Delete Controller
        $this->deleteController();

        // Delete Requests
        $this->deleteRequests();

        // Delete Views
        $this->deleteViews();

        // Delete Migration (with confirmation)
        $this->deleteMigration();

        // Remove Routes
        $this->removeRoutes();

        // Delete Tests
        $this->deleteTests();

        $this->newLine();
        $this->displaySummary();

        return Command::SUCCESS;
    }

    protected function parseModelName()
    {
        $name = $this->argument('name');

        if (Str::contains($name, '/')) {
            $parts = explode('/', $name);
            $this->modelName = array_pop($parts);
            $this->namespace = implode('\\', $parts);
        } else {
            $this->modelName = $name;
            $this->namespace = '';
        }
    }

    protected function deleteModel()
    {
        $namespacePath = str_replace('\\', '/', $this->namespace);
        $path = app_path("Models/{$namespacePath}/{$this->modelName}.php");

        if (File::exists($path)) {
            File::delete($path);
            $this->deletedFiles[] = $path;
            $this->info("✔ Deleted Model: {$path}");
        } else {
            $this->warn("⚠ Model not found: {$path}");
        }
    }

    protected function deleteController()
    {
        $namespacePath = str_replace('\\', '/', $this->namespace);
        $path = app_path("Http/Controllers/{$namespacePath}/{$this->modelName}Controller.php");

        if (File::exists($path)) {
            File::delete($path);
            $this->deletedFiles[] = $path;
            $this->info("✔ Deleted Controller: {$path}");
        } else {
            $this->warn("⚠ Controller not found: {$path}");
        }
    }

    protected function deleteRequests()
    {
        $namespacePath = str_replace('\\', '/', $this->namespace);
        $basePath = app_path("Http/Requests/{$namespacePath}");

        $storeRequest = "{$basePath}/Store{$this->modelName}Request.php";
        $updateRequest = "{$basePath}/Update{$this->modelName}Request.php";

        if (File::exists($storeRequest)) {
            File::delete($storeRequest);
            $this->deletedFiles[] = $storeRequest;
            $this->info("✔ Deleted Store Request: {$storeRequest}");
        }

        if (File::exists($updateRequest)) {
            File::delete($updateRequest);
            $this->deletedFiles[] = $updateRequest;
            $this->info("✔ Deleted Update Request: {$updateRequest}");
        }
    }

    protected function deleteViews()
    {
        $namespacePath = str_replace('\\', '/', strtolower($this->namespace));
        $viewPath = resource_path("views/{$namespacePath}/" . Str::plural(Str::snake($this->modelName)));

        if (File::exists($viewPath)) {
            File::deleteDirectory($viewPath);
            $this->deletedFiles[] = $viewPath;
            $this->info("✔ Deleted Views directory: {$viewPath}");
        } else {
            $this->warn("⚠ Views directory not found: {$viewPath}");
        }
    }

    protected function deleteMigration()
    {
        $tableName = Str::plural(Str::snake($this->modelName));
        $migrationFiles = File::glob(database_path("migrations/*_create_{$tableName}_table.php"));

        if (count($migrationFiles) > 0) {
            if ($this->option('force') || $this->confirm('Do you want to delete the migration file as well?')) {
                foreach ($migrationFiles as $file) {
                    File::delete($file);
                    $this->deletedFiles[] = $file;
                    $this->info("✔ Deleted Migration: {$file}");
                }
            } else {
                $this->line('Migration file kept.');
            }
        }
    }

    protected function removeRoutes()
    {
        $routeFile = base_path('routes/web.php');
        $tableName = Str::plural(Str::snake($this->modelName));
        $namespacePath = strtolower(str_replace('\\', '/', $this->namespace));
        $routePath = $namespacePath ? "{$namespacePath}/{$tableName}" : $tableName;

        if (File::exists($routeFile)) {
            $content = File::get($routeFile);

            // Remove route line
            $patterns = [
                "/Route::resource\(['\"]" . preg_quote($routePath, '/') . "['\"].*?\);?\n/",
                "/Route::apiResource\(['\"]" . preg_quote($routePath, '/') . "['\"].*?\);?\n/",
            ];

            $updated = false;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $content = preg_replace($pattern, '', $content);
                    $updated = true;
                }
            }

            if ($updated) {
                File::put($routeFile, $content);
                $this->info("✔ Removed routes from: {$routeFile}");
            } else {
                $this->warn("⚠ No matching routes found in: {$routeFile}");
            }
        }
    }

    protected function deleteTests()
    {
        $testPath = base_path("tests/Feature/{$this->modelName}Test.php");

        if (File::exists($testPath)) {
            File::delete($testPath);
            $this->deletedFiles[] = $testPath;
            $this->info("✔ Deleted Test: {$testPath}");
        }
    }

    protected function displaySummary()
    {
        $this->line('┌─────────────────────────────────────────┐');
        $this->line('│         Deletion Summary                │');
        $this->line('├─────────────────────────────────────────┤');
        $this->line("│ Model:        {$this->modelName}");
        $this->line("│ Files Deleted: " . count($this->deletedFiles));
        $this->line('└─────────────────────────────────────────┘');

        if (count($this->deletedFiles) > 0) {
            $this->newLine();
            $this->info('✨ CRUD files deleted successfully!');
        } else {
            $this->newLine();
            $this->warn('⚠️  No files were found to delete.');
        }
    }
}