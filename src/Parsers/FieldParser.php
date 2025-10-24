<?php

namespace Sndpbag\CrudGenerator\Parsers;

use Illuminate\Support\Str;

class FieldParser
{
    protected $fieldsString;
    protected $fields = [];

    public function __construct(string $fieldsString)
    {
        $this->fieldsString = $fieldsString;
    }

    public function parse(): array
    {
        $fieldDefinitions = explode(',', $this->fieldsString);

        foreach ($fieldDefinitions as $definition) {
            $field = $this->parseFieldDefinition(trim($definition));
            if ($field) {
                $this->fields[] = $field;
            }
        }

        return $this->fields;
    }

    protected function parseFieldDefinition(string $definition): ?array
    {
        if (empty($definition)) {
            return null;
        }

        $parts = explode(':', $definition);
        
        if (count($parts) < 2) {
            return null;
        }

        $field = [
            'name' => $parts[0],
            'type' => $parts[1],
            'modifiers' => [],
            'nullable' => false,
            'unique' => false,
            'default' => null,
            'validation' => [],
            'html_type' => $this->getHtmlType($parts[1]),
            'enum_values' => [],
        ];

        // Parse modifiers and enum values
        for ($i = 2; $i < count($parts); $i++) {
            $modifier = $parts[$i];
            
            // Check for enum with values
            if (preg_match('/^enum\((.*?)\)$/', $parts[1], $matches)) {
                $field['type'] = 'enum';
                $field['enum_values'] = array_map('trim', explode(',', $matches[1]));
                $field['html_type'] = 'select';
                continue;
            }

            // Check for default value
            if (preg_match('/^default\((.*?)\)$/', $modifier, $matches)) {
                $field['default'] = $matches[1];
                $field['modifiers'][] = 'default';
                continue;
            }

            // Check for other modifiers
            if ($modifier === 'nullable') {
                $field['nullable'] = true;
                $field['modifiers'][] = 'nullable';
            } elseif ($modifier === 'unique') {
                $field['unique'] = true;
                $field['modifiers'][] = 'unique';
            } else {
                $field['modifiers'][] = $modifier;
            }
        }

        // Generate validation rules
        $field['validation'] = $this->generateValidationRules($field);

        return $field;
    }

    protected function getHtmlType(string $type): string
    {
        // Handle enum
        if (Str::startsWith($type, 'enum')) {
            return 'select';
        }

        $typeMap = [
            'string' => 'text',
            'text' => 'textarea',
            'integer' => 'number',
            'bigInteger' => 'number',
            'float' => 'number',
            'decimal' => 'number',
            'double' => 'number',
            'boolean' => 'checkbox',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'time' => 'time',
            'timestamp' => 'datetime-local',
            'file' => 'file',
            'image' => 'file',
            'email' => 'email',
            'password' => 'password',
            'url' => 'url',
            'tel' => 'tel',
            'color' => 'color',
        ];

        return $typeMap[$type] ?? 'text';
    }

    protected function generateValidationRules(array $field): array
    {
        $rules = [];

        // Required or nullable
        if (!$field['nullable']) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Type-specific rules
        switch ($field['type']) {
            case 'string':
                $rules[] = 'string';
                $rules[] = 'max:255';
                break;

            case 'text':
                $rules[] = 'string';
                break;

            case 'integer':
            case 'bigInteger':
                $rules[] = 'integer';
                break;

            case 'float':
            case 'decimal':
            case 'double':
                $rules[] = 'numeric';
                break;

            case 'boolean':
                $rules[] = 'boolean';
                break;

            case 'date':
            case 'datetime':
            case 'timestamp':
                $rules[] = 'date';
                break;

            case 'email':
                $rules[] = 'email';
                $rules[] = 'max:255';
                break;

            case 'url':
                $rules[] = 'url';
                break;

            case 'file':
                $rules[] = 'file';
                $rules[] = 'max:2048';
                break;

            case 'image':
                $rules[] = 'image';
                $rules[] = 'mimes:jpg,jpeg,png,gif';
                $rules[] = 'max:2048';
                break;

            case 'enum':
                if (!empty($field['enum_values'])) {
                    $rules[] = 'in:' . implode(',', $field['enum_values']);
                }
                break;
        }

        // Unique rule
        if ($field['unique']) {
            $rules[] = 'unique';
        }

        return $rules;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}