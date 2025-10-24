<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RequestGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $storePath = $this->generateStoreRequest();
        $updatePath = $this->generateUpdateRequest();

        return [
            'success' => true,
            'storePath' => $storePath,
            'updatePath' => $updatePath
        ];
    }

    protected function generateStoreRequest(): string
    {
        $stub = $this->getStub('request.store');
        
        $stub = $this->replaceNamespace($stub);
        $stub = $this->replaceClassName($stub, 'Store');
        $stub = $this->replaceRules($stub, 'store');

        $path = $this->getRequestPath('Store');
        $this->ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        return $path;
    }

    protected function generateUpdateRequest(): string
    {
        $stub = $this->getStub('request.update');
        
        $stub = $this->replaceNamespace($stub);
        $stub = $this->replaceClassName($stub, 'Update');
        $stub = $this->replaceRules($stub, 'update');

        $path = $this->getRequestPath('Update');
        $this->ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        return $path;
    }

    protected function replaceNamespace($stub): string
    {
        $namespace = $this->getFullNamespace('request');
        return str_replace('{{namespace}}', $namespace, $stub);
    }

    protected function replaceClassName($stub, $prefix): string
    {
        $className = $prefix . $this->options['modelName'] . 'Request';
        return str_replace('{{class}}', $className, $stub);
    }

    protected function replaceRules($stub, $context): string
    {
        $rules = [];

        foreach ($this->options['fields'] as $field) {
            $fieldRules = $this->generateFieldRules($field, $context);
            $rules[] = "            '{$field['name']}' => '{$fieldRules}',";
        }

        $rulesString = implode("\n", $rules);
        return str_replace('{{rules}}', $rulesString, $stub);
    }

    protected function generateFieldRules(array $field, string $context): string
    {
        $rules = $field['validation'];

        // Handle unique rule for update context
        if ($field['unique'] && $context === 'update') {
            $tableName = $this->getTableName();
            $uniqueIndex = array_search('unique', $rules);
            
            if ($uniqueIndex !== false) {
                // Replace 'unique' with 'unique:table,column,except_id'
                $rules[$uniqueIndex] = "unique:{$tableName},{$field['name']},{\$this->route('id')}";
            }
        } elseif ($field['unique'] && $context === 'store') {
            $tableName = $this->getTableName();
            $uniqueIndex = array_search('unique', $rules);
            
            if ($uniqueIndex !== false) {
                $rules[$uniqueIndex] = "unique:{$tableName},{$field['name']}";
            }
        }

        return implode('|', $rules);
    }

    protected function getRequestPath($prefix): string
    {
        $namespacePath = str_replace('\\', '/', $this->options['namespace']);
        $className = $prefix . $this->options['modelName'] . 'Request';
        
        return app_path("Http/Requests/{$namespacePath}/{$className}.php");
    }
}