<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;

class TestGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $testType = $this->options['testType'] ?? 'phpunit';
        
        if ($testType === 'pest') {
            $stub = $this->getStub('test.pest');
        } else {
            $stub = $this->getStub('test.phpunit');
        }
        
        $stub = $this->replaceTestPlaceholders($stub);
        
        $path = $this->getTestPath();
        $this->ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        return [
            'success' => true,
            'path' => $path
        ];
    }

    protected function replaceTestPlaceholders($stub): string
    {
        $modelName = $this->options['modelName'];
        $variable = $this->getModelVariable();
        $routePath = $this->getRoutePath();
        $modelNamespace = $this->getFullNamespace('model');
        
        $replacements = [
            '{{namespace}}' => 'Tests\\Feature',
            '{{class}}' => "{$modelName}Test",
            '{{modelName}}' => $modelName,
            '{{modelNamespace}}' => $modelNamespace,
            '{{modelVariable}}' => $variable,
            '{{routePath}}' => $routePath,
            '{{testData}}' => $this->generateTestData(),
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }

    protected function generateTestData(): string
    {
        $data = [];
        
        foreach ($this->options['fields'] as $field) {
            switch ($field['type']) {
                case 'string':
                case 'email':
                    $data[] = "'{$field['name']}' => 'Test {$field['name']}'";
                    break;
                case 'text':
                    $data[] = "'{$field['name']}' => 'Test {$field['name']} content'";
                    break;
                case 'integer':
                case 'bigInteger':
                    $data[] = "'{$field['name']}' => 123";
                    break;
                case 'float':
                case 'decimal':
                    $data[] = "'{$field['name']}' => 12.34";
                    break;
                case 'boolean':
                    $data[] = "'{$field['name']}' => true";
                    break;
                case 'date':
                    $data[] = "'{$field['name']}' => now()->format('Y-m-d')";
                    break;
                case 'datetime':
                    $data[] = "'{$field['name']}' => now()->format('Y-m-d H:i:s')";
                    break;
                case 'enum':
                    if (!empty($field['enum_values'])) {
                        $data[] = "'{$field['name']}' => '{$field['enum_values'][0]}'";
                    }
                    break;
            }
        }
        
        return implode(",\n            ", $data);
    }

    protected function getTestPath(): string
    {
        $modelName = $this->options['modelName'];
        return base_path("tests/Feature/{$modelName}Test.php");
    }
}