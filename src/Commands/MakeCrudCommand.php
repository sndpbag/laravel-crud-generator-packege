<?php

namespace Sndpbag\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Sndpbag\CrudGenerator\Generators\ModelGenerator;
use Sndpbag\CrudGenerator\Generators\MigrationGenerator;
use Sndpbag\CrudGenerator\Generators\ControllerGenerator;
use Sndpbag\CrudGenerator\Generators\RequestGenerator;
use Sndpbag\CrudGenerator\Generators\ViewGenerator;
use Sndpbag\CrudGenerator\Generators\RouteGenerator;
use Sndpbag\CrudGenerator\Generators\TestGenerator;
use Sndpbag\CrudGenerator\Parsers\FieldParser;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {name}
                            {--fields= : Field definitions (name:type:modifier)}
                            {--softdelete : Add soft deletes}
                            {--auth : Add authentication middleware}
                            {--api : Generate API instead of web CRUD}
                            {--belongsTo=* : BelongsTo relationships}
                            {--hasMany=* : HasMany relationships}
                            {--tests : Generate PHPUnit tests}
                            {--pest : Generate Pest tests}';

    protected $description = 'Generate a complete CRUD with all necessary files';

    protected $fieldParser;
    protected $modelName;
    protected $namespace;
    protected $fields = [];
    protected $relationships = [];

    public function handle()
    {
        $this->info('🚀 Starting CRUD Generation...');
        $this->newLine();

        // Parse model name and namespace
        $this->parseModelName();

        // Parse fields
        $this->parseFields();

        // Parse relationships
        $this->parseRelationships();

        // Generate files
        $this->generateFiles();

        $this->newLine();
        $this->info('✨ CRUD generated successfully!');
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

        $this->info("📝 Model: {$this->modelName}");
        if ($this->namespace) {
            $this->info("📁 Namespace: {$this->namespace}");
        }
    }

    protected function parseFields()
    {
        $fieldsOption = $this->option('fields');

        if (!$fieldsOption) {
            $this->error('❌ --fields option is required!');
            $this->line('Example: --fields="name:string,email:string:unique,price:integer:nullable"');
            exit(1);
        }

        $this->fieldParser = new FieldParser($fieldsOption);
        $this->fields = $this->fieldParser->parse();

        $this->info("📋 Fields: " . count($this->fields) . " field(s) detected");
    }

    protected function parseRelationships()
    {
        $belongsTo = $this->option('belongsTo');
        $hasMany = $this->option('hasMany');

        foreach ($belongsTo as $relation) {
            $this->relationships[] = [
                'type' => 'belongsTo',
                'model' => $relation
            ];
        }

        foreach ($hasMany as $relation) {
            $this->relationships[] = [
                'type' => 'hasMany',
                'model' => $relation
            ];
        }

        if (count($this->relationships) > 0) {
            $this->info("🔗 Relationships: " . count($this->relationships) . " relationship(s)");
        }
    }

    protected function generateFiles()
    {
        $options = [
            'modelName' => $this->modelName,
            'namespace' => $this->namespace,
            'fields' => $this->fields,
            'relationships' => $this->relationships,
            'softDelete' => $this->option('softdelete'),
            'auth' => $this->option('auth'),
            'api' => $this->option('api'),
        ];

        // 1. Generate Model
        $this->generateModel($options);

        // 2. Generate Migration
        $this->generateMigration($options);

        // 3. Generate Requests
        $this->generateRequests($options);

        // 4. Generate Controller
        $this->generateController($options);

        // 5. Generate Views (if not API)
        if (!$this->option('api')) {
            $this->generateViews($options);
        }

        // 6. Register Routes
        $this->registerRoutes($options);

        // 7. Generate Tests (if requested)
        if ($this->option('tests') || $this->option('pest')) {
            $this->generateTests($options);
        }
    }

    protected function generateModel($options)
    {
        $generator = new ModelGenerator($options);
        $result = $generator->generate();

        if ($result['success']) {
            $this->info("✔ Model created: {$result['path']}");
        } else {
            $this->error("✘ Model generation failed: {$result['message']}");
        }
    }

    protected function generateMigration($options)
    {
        $generator = new MigrationGenerator($options);
        $result = $generator->generate();

        if ($result['success']) {
            $this->info("✔ Migration created: {$result['path']}");
        } else {
            $this->error("✘ Migration generation failed: {$result['message']}");
        }
    }

    protected function generateRequests($options)
    {
        $generator = new RequestGenerator($options);
        $result = $generator->generate();

        if ($result['success']) {
            $this->info("✔ Store Request created: {$result['storePath']}");
            $this->info("✔ Update Request created: {$result['updatePath']}");
        } else {
            $this->error("✘ Request generation failed: {$result['message']}");
        }
    }

    protected function generateController($options)
    {
        $generator = new ControllerGenerator($options);
        $result = $generator->generate();

        if ($result['success']) {
            $this->info("✔ Controller created: {$result['path']}");
        } else {
            $this->error("✘ Controller generation failed: {$result['message']}");
        }
    }

    protected function generateViews($options)
    {
        $generator = new ViewGenerator($options);
        $result = $generator->generate();

        if ($result['success']) {
            $this->info("✔ Views created: {$result['path']}");
            $this->line("  → index.blade.php");
            $this->line("  → create.blade.php");
            $this->line("  → edit.blade.php");
        } else {
            $this->error("✘ View generation failed: {$result['message']}");
        }
    }

    protected function registerRoutes($options)
    {
        $generator = new RouteGenerator($options);
        $result = $generator->generate();

        if ($result['success']) {
            $this->info("✔ Routes registered in: routes/{$result['file']}.php");
        } else {
            $this->warn("⚠ Route registration: {$result['message']}");
        }
    }

    protected function generateTests($options)
    {
        $testType = $this->option('pest') ? 'pest' : 'phpunit';
        $options['testType'] = $testType;

        $generator = new TestGenerator($options);
        $result = $generator->generate();

        if ($result['success']) {
            $this->info("✔ Test file created: {$result['path']}");
        } else {
            $this->error("✘ Test generation failed: {$result['message']}");
        }
    }

    protected function displaySummary()
    {
        $this->newLine();
        $this->line('┌─────────────────────────────────────────┐');
        $this->line('│         Generation Summary              │');
        $this->line('├─────────────────────────────────────────┤');
        $this->line("│ Model:      {$this->modelName}");
        $this->line("│ Fields:     " . count($this->fields));
        $this->line("│ Relations:  " . count($this->relationships));
        $this->line("│ Soft Delete: " . ($this->option('softdelete') ? 'Yes' : 'No'));
        $this->line("│ Auth:       " . ($this->option('auth') ? 'Yes' : 'No'));
        $this->line("│ API Mode:   " . ($this->option('api') ? 'Yes' : 'No'));
        $this->line('└─────────────────────────────────────────┘');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('  1. Run: php artisan migrate');
        $this->line('  2. Visit your application to test the CRUD');
        if ($this->option('tests') || $this->option('pest')) {
            $this->line('  3. Run tests: php artisan test');
        }
    }
}