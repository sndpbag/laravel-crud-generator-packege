<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrationGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $stub = $this->getStub('migration');
        
        $stub = $this->replaceClassName($stub);
        $stub = $this->replaceTableName($stub);
        $stub = $this->replaceColumns($stub);

        $path = $this->getMigrationPath();
        
        File::put($path, $stub);

        return [
            'success' => true,
            'path' => $path
        ];
    }

    protected function replaceClassName($stub): string
    {
        $className = 'Create' . Str::studly($this->getTableName()) . 'Table';
        return str_replace('{{class}}', $className, $stub);
    }

    protected function replaceTableName($stub): string
    {
        return str_replace('{{table}}', $this->getTableName(), $stub);
    }

    protected function replaceColumns($stub): string
    {
        $columns = "            \$table->id();\n";

        foreach ($this->options['fields'] as $field) {
            $columns .= $this->generateColumnDefinition($field);
        }

        // Add foreign keys for belongsTo relationships
        foreach ($this->options['relationships'] as $relation) {
            if ($relation['type'] === 'belongsTo') {
                $foreignKey = Str::snake($relation['model']) . '_id';
                $columns .= "            \$table->foreignId('{$foreignKey}')->constrained()->onDelete('cascade');\n";
            }
        }

        if ($this->options['softDelete']) {
            $columns .= "            \$table->softDeletes();\n";
        }

        $columns .= "            \$table->timestamps();";

        return str_replace('{{columns}}', $columns, $stub);
    }

    protected function generateColumnDefinition(array $field): string
    {
        $name = $field['name'];
        $type = $field['type'];
        $line = "            \$table->";

        // Handle enum type
        if ($type === 'enum' && !empty($field['enum_values'])) {
            $values = "'" . implode("', '", $field['enum_values']) . "'";
            $line .= "enum('{$name}', [{$values}])";
        } else {
            // Handle standard types
            $line .= $this->getMigrationColumnType($type) . "('{$name}')";
        }

        // Add modifiers
        if ($field['nullable']) {
            $line .= "->nullable()";
        }

        if ($field['unique']) {
            $line .= "->unique()";
        }

        if ($field['default'] !== null) {
            $defaultValue = $field['default'];
            
            // Check if default value needs quotes
            if (in_array($type, ['string', 'text', 'enum'])) {
                $line .= "->default('{$defaultValue}')";
            } else {
                $line .= "->default({$defaultValue})";
            }
        }

        $line .= ";\n";

        return $line;
    }

    protected function getMigrationColumnType(string $type): string
    {
        $typeMap = [
            'string' => 'string',
            'text' => 'text',
            'integer' => 'integer',
            'bigInteger' => 'bigInteger',
            'float' => 'float',
            'decimal' => 'decimal',
            'double' => 'double',
            'boolean' => 'boolean',
            'date' => 'date',
            'datetime' => 'dateTime',
            'timestamp' => 'timestamp',
            'time' => 'time',
            'file' => 'string',
            'image' => 'string',
            'email' => 'string',
            'password' => 'string',
            'url' => 'string',
        ];

        return $typeMap[$type] ?? 'string';
    }

    protected function getMigrationPath(): string
    {
        $timestamp = date('Y_m_d_His');
        $tableName = $this->getTableName();
        $filename = "{$timestamp}_create_{$tableName}_table.php";
        
        return database_path("migrations/{$filename}");
    }
}