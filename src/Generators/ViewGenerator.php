<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ViewGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $viewPath = $this->getViewDirectory();
        $this->ensureDirectoryExists($viewPath);

        $this->generateIndexView($viewPath);
        $this->generateCreateView($viewPath);
        $this->generateEditView($viewPath);
        $this->generateShowView($viewPath);

        return [
            'success' => true,
            'path' => $viewPath
        ];
    }

    protected function generateIndexView($viewPath): void
    {
        $template = config('crud-generator.template', 'bootstrap');
        $stub = $this->getStub("views/{$template}/index");
        
        $stub = $this->replaceCommonPlaceholders($stub);
        $stub = $this->replaceTableHeaders($stub);
        $stub = $this->replaceTableColumns($stub);
        $stub = $this->replaceSearchFields($stub);
        
        File::put("{$viewPath}/index.blade.php", $stub);
    }

    protected function generateCreateView($viewPath): void
    {
        $template = config('crud-generator.template', 'bootstrap');
        $stub = $this->getStub("views/{$template}/create");
        
        $stub = $this->replaceCommonPlaceholders($stub);
        $stub = $this->replaceFormFields($stub);
        
        File::put("{$viewPath}/create.blade.php", $stub);
    }

    protected function generateEditView($viewPath): void
    {
        $template = config('crud-generator.template', 'bootstrap');
        $stub = $this->getStub("views/{$template}/edit");
        
        $stub = $this->replaceCommonPlaceholders($stub);
        $stub = $this->replaceFormFields($stub, true);
        
        File::put("{$viewPath}/edit.blade.php", $stub);
    }

    protected function replaceCommonPlaceholders($stub): string
    {
        $modelName = $this->options['modelName'];
        $modelNamePlural = Str::plural($modelName);
        $variable = $this->getModelVariable();
        $variablePlural = $this->getModelVariablePlural();
        $routePath = $this->getRoutePath();
        
        $replacements = [
            '{{modelName}}' => $modelName,
            '{{modelNamePlural}}' => $modelNamePlural,
            '{{modelVariable}}' => $variable,
            '{{modelVariablePlural}}' => $variablePlural,
            '{{routePath}}' => $routePath,
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }

    protected function replaceTableHeaders($stub): string
    {
        $headers = '';
        
        foreach ($this->options['fields'] as $field) {
            if (!in_array($field['type'], ['text', 'file', 'image'])) {
                $label = Str::title(str_replace('_', ' ', $field['name']));
                $headers .= "                <th>\n";
                $headers .= "                    <a href=\"{{ route('{$this->getRoutePath()}.index', ['sort' => '{$field['name']}', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}\">\n";
                $headers .= "                        {$label}\n";
                $headers .= "                        @if(request('sort') === '{$field['name']}')\n";
                $headers .= "                            <i class=\"fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}\"></i>\n";
                $headers .= "                        @endif\n";
                $headers .= "                    </a>\n";
                $headers .= "                </th>\n";
            }
        }
        
        $headers .= "                <th>Actions</th>";
        
        return str_replace('{{tableHeaders}}', $headers, $stub);
    }

    protected function replaceTableColumns($stub): string
    {
        $variable = $this->getModelVariable();
        $columns = '';
        
        foreach ($this->options['fields'] as $field) {
            if (!in_array($field['type'], ['text', 'file', 'image'])) {
                $columns .= "                <td>";
                
                if ($field['type'] === 'boolean') {
                    $columns .= "{{ \${$variable}->{$field['name']} ? 'Yes' : 'No' }}";
                } elseif (in_array($field['type'], ['date', 'datetime', 'timestamp'])) {
                    $columns .= "{{ \${$variable}->{$field['name']}->format('Y-m-d') }}";
                } else {
                    $columns .= "{{ \${$variable}->{$field['name']} }}";
                }
                
                $columns .= "</td>\n";
            }
        }
        
        return str_replace('{{tableColumns}}', $columns, $stub);
    }

    protected function replaceSearchFields($stub): string
    {
        $searchableFields = collect($this->options['fields'])
            ->filter(fn($field) => in_array($field['type'], ['string', 'text', 'email']))
            ->pluck('name')
            ->toArray();
        
        if (empty($searchableFields)) {
            return str_replace('{{searchPlaceholder}}', 'Search...', $stub);
        }
        
        $placeholder = 'Search by ' . implode(', ', array_map(fn($f) => Str::title($f), $searchableFields)) . '...';
        
        return str_replace('{{searchPlaceholder}}', $placeholder, $stub);
    }

    protected function replaceFormFields($stub, $isEdit = false): string
    {
        $variable = $isEdit ? $this->getModelVariable() : null;
        $fields = '';
        
        foreach ($this->options['fields'] as $field) {
            $fields .= $this->generateFormField($field, $variable);
        }
        
        // Add belongsTo relationship fields
        foreach ($this->options['relationships'] as $relation) {
            if ($relation['type'] === 'belongsTo') {
                $fields .= $this->generateRelationshipField($relation, $variable);
            }
        }
        
        return str_replace('{{formFields}}', $fields, $stub);
    }

    protected function generateFormField($field, $variable = null): string
    {
        $template = config('crud-generator.template', 'bootstrap');
        $label = Str::title(str_replace('_', ' ', $field['name']));
        $required = !$field['nullable'] ? 'required' : '';
        $value = $variable ? "{{ old('{$field['name']}', \${$variable}->{$field['name']}) }}" : "{{ old('{$field['name']}') }}";
        
        $html = "        <div class=\"mb-3\">\n";
        $html .= "            <label for=\"{$field['name']}\" class=\"form-label\">{$label}</label>\n";
        
        switch ($field['html_type']) {
            case 'textarea':
                $html .= "            <textarea name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\" rows=\"4\" {$required}>{$value}</textarea>\n";
                break;
                
            case 'select':
                $html .= "            <select name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\" {$required}>\n";
                $html .= "                <option value=\"\">Select {$label}</option>\n";
                foreach ($field['enum_values'] as $enumValue) {
                    $selected = $variable ? "{{ \${$variable}->{$field['name']} === '{$enumValue}' ? 'selected' : '' }}" : "";
                    $html .= "                <option value=\"{$enumValue}\" {$selected}>" . Str::title($enumValue) . "</option>\n";
                }
                $html .= "            </select>\n";
                break;
                
            case 'checkbox':
                $checked = $variable ? "{{ \${$variable}->{$field['name']} ? 'checked' : '' }}" : "";
                $html .= "            <div class=\"form-check\">\n";
                $html .= "                <input type=\"checkbox\" name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-check-input\" value=\"1\" {$checked}>\n";
                $html .= "                <label class=\"form-check-label\" for=\"{$field['name']}\">{$label}</label>\n";
                $html .= "            </div>\n";
                break;
                
            case 'file':
                if ($variable) {
                    $html .= "            @if(\${$variable}->{$field['name']})\n";
                    $html .= "                <div class=\"mb-2\">\n";
                    if ($field['type'] === 'image') {
                        $html .= "                    <img src=\"{{ Storage::url(\${$variable}->{$field['name']}) }}\" alt=\"Current image\" style=\"max-width: 200px;\">\n";
                    } else {
                        $html .= "                    <a href=\"{{ Storage::url(\${$variable}->{$field['name']}) }}\" target=\"_blank\">View current file</a>\n";
                    }
                    $html .= "                </div>\n";
                    $html .= "            @endif\n";
                }
                $accept = $field['type'] === 'image' ? 'accept="image/*"' : '';
                $html .= "            <input type=\"file\" name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\" {$accept}>\n";
                break;
                
            default:
                $html .= "            <input type=\"{$field['html_type']}\" name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"form-control\" value=\"{$value}\" {$required}>\n";
        }
        
        $html .= "            @error('{$field['name']}')\n";
        $html .= "                <div class=\"text-danger mt-1\">{{ \$message }}</div>\n";
        $html .= "            @enderror\n";
        $html .= "        </div>\n\n";
        
        return $html;
    }

    protected function generateRelationshipField($relation, $variable = null): string
    {
        $fieldName = Str::snake($relation['model']) . '_id';
        $label = Str::title(str_replace('_', ' ', $relation['model']));
        $pluralVar = Str::camel(Str::plural($relation['model']));
        
        $html = "        <div class=\"mb-3\">\n";
        $html .= "            <label for=\"{$fieldName}\" class=\"form-label\">{$label}</label>\n";
        $html .= "            <select name=\"{$fieldName}\" id=\"{$fieldName}\" class=\"form-control\" required>\n";
        $html .= "                <option value=\"\">Select {$label}</option>\n";
        $html .= "                @foreach(\${$pluralVar} as \$item)\n";
        
        if ($variable) {
            $html .= "                    <option value=\"{{ \$item->id }}\" {{ \${$variable}->{$fieldName} === \$item->id ? 'selected' : '' }}>{{ \$item->name }}</option>\n";
        } else {
            $html .= "                    <option value=\"{{ \$item->id }}\">{{ \$item->name }}</option>\n";
        }
        
        $html .= "                @endforeach\n";
        $html .= "            </select>\n";
        $html .= "            @error('{$fieldName}')\n";
        $html .= "                <div class=\"text-danger mt-1\">{{ \$message }}</div>\n";
        $html .= "            @enderror\n";
        $html .= "        </div>\n\n";
        
        return $html;
    }

    protected function getViewDirectory(): string
    {
        $namespacePath = strtolower(str_replace('\\', '/', $this->options['namespace']));
        $viewName = Str::plural(Str::snake($this->options['modelName']));
        
        if ($namespacePath) {
            return resource_path("views/{$namespacePath}/{$viewName}");
        }
        
        return resource_path("views/{$viewName}");
    }


    protected function generateShowView($viewPath): void
    {
        $template = config('crud-generator.template', 'bootstrap');
        $stub = $this->getStub("views/{$template}/show");
        
        $stub = $this->replaceCommonPlaceholders($stub);
        $stub = $this->replaceShowFields($stub);
        
        File::put("{$viewPath}/show.blade.php", $stub);
    }

    protected function replaceShowFields($stub): string
    {
        $variable = $this->getModelVariable();
        $fieldsHtml = '';

        // Add regular fields
        foreach ($this->options['fields'] as $field) {
            $label = Str::title(str_replace('_', ' ', $field['name']));
            $fieldName = $field['name'];
            
            $fieldsHtml .= "                    <div class=\"mb-3\">\n";
            $fieldsHtml .= "                        <strong>{$label}:</strong>\n";
            
            if ($field['type'] === 'image') {
                $fieldsHtml .= "                        @if(\${$variable}->{$fieldName})\n";
                $fieldsHtml .= "                            <div class=\"mt-2\"><img src=\"{{ Storage::url(\${$variable}->{$fieldName}) }}\" alt=\"{$label}\" style=\"max-width: 300px;\"></div>\n";
                $fieldsHtml .= "                        @else\n";
                $fieldsHtml .= "                            <p class=\"mb-0\">N/A</p>\n";
                $fieldsHtml .= "                        @endif\n";
            } elseif ($field['type'] === 'file') {
                $fieldsHtml .= "                        @if(\${$variable}->{$fieldName})\n";
                $fieldsHtml .= "                            <a href=\"{{ Storage::url(\${$variable}->{$fieldName}) }}\" target=\"_blank\" class=\"d-block mt-1\">View File</a>\n";
                $fieldsHtml .= "                        @else\n";
                $fieldsHtml .= "                            <p class=\"mb-0\">N/A</p>\n";
                $fieldsHtml .= "                        @endif\n";
            } elseif ($field['type'] === 'boolean') {
                $fieldsHtml .= "                        <p class=\"mb-0\">{{ \${$variable}->{$fieldName} ? 'Yes' : 'No' }}</p>\n";
            } else {
                $fieldsHtml .= "                        <p class=\"mb-0\">{{ \${$variable}->{$fieldName} }}</p>\n";
            }
            
            $fieldsHtml .= "                    </div>\n\n";
        }

        // Add belongsTo relationship fields
        foreach ($this->options['relationships'] as $relation) {
            if ($relation['type'] === 'belongsTo') {
                $relationModel = $relation['model'];
                $relationVar = Str::camel($relationModel);
                $label = Str::title(str_replace('_', ' ', $relationModel));
                
                $fieldsHtml .= "                    <div class=\"mb-3\">\n";
                $fieldsHtml .= "                        <strong>{$label}:</strong>\n";
                // Assuming relationship name field is 'name'. You can make this configurable later.
                $fieldsHtml .= "                        <p class=\"mb-0\">{{ \${$variable}->{$relationVar}->name ?? 'N/A' }}</p>\n";
                $fieldsHtml .= "                    </div>\n\n";
            }
        }
        
        return str_replace('{{showFields}}', $fieldsHtml, $stub);
    }
}