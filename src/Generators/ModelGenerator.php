<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $stub = $this->getStub('model');
        
        $stub = $this->replaceNamespace($stub);
        $stub = $this->replaceClassName($stub);
        $stub = $this->replaceFillable($stub);
        $stub = $this->replaceSoftDeletes($stub);
        $stub = $this->replaceRelationships($stub);
        $stub = $this->replaceCasts($stub);

        $path = $this->getModelPath();
        
        $this->ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        return [
            'success' => true,
            'path' => $path
        ];
    }

    protected function replaceNamespace($stub): string
    {
        $namespace = $this->getFullNamespace('model');
        return str_replace('{{namespace}}', $namespace, $stub);
    }

    protected function replaceClassName($stub): string
    {
        return str_replace('{{class}}', $this->options['modelName'], $stub);
    }

    protected function replaceFillable($stub): string
    {
        $fillable = collect($this->options['fields'])
            ->map(fn($field) => "'{$field['name']}'")
            ->implode(",\n        ");

        return str_replace('{{fillable}}', $fillable, $stub);
    }

    protected function replaceSoftDeletes($stub): string
    {
        $useSoftDeletes = $this->options['softDelete'] ?? false;
        
        $useStatement = $useSoftDeletes ? "use Illuminate\Database\Eloquent\SoftDeletes;" : "";
        $trait = $useSoftDeletes ? "use SoftDeletes;" : "";

        $stub = str_replace('{{useSoftDeletes}}', $useStatement, $stub);
        $stub = str_replace('{{softDeleteTrait}}', $trait, $stub);

        return $stub;
    }

    protected function replaceRelationships($stub): string
    {
        $relationships = '';

        foreach ($this->options['relationships'] as $relation) {
            if ($relation['type'] === 'belongsTo') {
                $relationships .= $this->generateBelongsTo($relation['model']);
            } elseif ($relation['type'] === 'hasMany') {
                $relationships .= $this->generateHasMany($relation['model']);
            }
        }

        return str_replace('{{relationships}}', $relationships, $stub);
    }

    protected function generateBelongsTo($model): string
    {
        $method = Str::camel($model);
        return "\n    public function {$method}()\n    {\n        return \$this->belongsTo(\\App\\Models\\{$model}::class);\n    }\n";
    }

    protected function generateHasMany($model): string
    {
        $method = Str::camel(Str::plural($model));
        return "\n    public function {$method}()\n    {\n        return \$this->hasMany(\\App\\Models\\{$model}::class);\n    }\n";
    }

    protected function replaceCasts($stub): string
    {
        $casts = [];

        foreach ($this->options['fields'] as $field) {
            if ($field['type'] === 'boolean') {
                $casts[] = "'{$field['name']}' => 'boolean'";
            } elseif (in_array($field['type'], ['date', 'datetime', 'timestamp'])) {
                $casts[] = "'{$field['name']}' => 'datetime'";
            } elseif (in_array($field['type'], ['integer', 'bigInteger'])) {
                $casts[] = "'{$field['name']}' => 'integer'";
            } elseif (in_array($field['type'], ['float', 'decimal', 'double'])) {
                $casts[] = "'{$field['name']}' => 'decimal:2'";
            }
        }

        $castsString = '';
        if (!empty($casts)) {
            $castsString = "\n    protected \$casts = [\n        " . implode(",\n        ", $casts) . "\n    ];";
        }

        return str_replace('{{casts}}', $castsString, $stub);
    }

    protected function getModelPath(): string
    {
        $namespacePath = str_replace('\\', '/', $this->options['namespace']);
        return app_path("Models/{$namespacePath}/{$this->options['modelName']}.php");
    }

    protected function getStub($type): string
    {
        // Check if user has published custom stubs
        $customStubPath = base_path("stubs/crud-generator/{$type}.stub");
        
        if (File::exists($customStubPath)) {
            return File::get($customStubPath);
        }

        // Use default stub
        return File::get(__DIR__ . "/../../stubs/{$type}.stub");
    }
}