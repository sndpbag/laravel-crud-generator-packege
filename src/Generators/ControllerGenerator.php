<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ControllerGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $stub = $this->options['api'] 
            ? $this->getStub('controller.api') 
            : $this->getStub('controller.web');
        
        $stub = $this->replaceNamespace($stub);
        $stub = $this->replaceClassName($stub);
        $stub = $this->replaceModelImports($stub);
        $stub = $this->replaceRequestImports($stub);
        $stub = $this->replaceAuthMiddleware($stub);
        $stub = $this->replaceModelVariable($stub);
        $stub = $this->replaceTableName($stub);
        $stub = $this->replaceViewPath($stub);
        $stub = $this->replaceRoutePath($stub);
        $stub = $this->replaceIndexMethod($stub);
        $stub = $this->replaceCreateMethod($stub);
        $stub = $this->replaceStoreMethod($stub);
        $stub = $this->replaceEditMethod($stub);
        $stub = $this->replaceUpdateMethod($stub);
        $stub = $this->replaceDestroyMethod($stub);

        $stub = str_replace('{{modelName}}', $this->options['modelName'], $stub);

        $path = $this->getControllerPath();
        $this->ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        return [
            'success' => true,
            'path' => $path
        ];
    }

    protected function replaceNamespace($stub): string
    {
        $namespace = $this->getFullNamespace('controller');
        return str_replace('{{namespace}}', $namespace, $stub);
    }

    protected function replaceClassName($stub): string
    {
        $className = $this->options['modelName'] . 'Controller';
        return str_replace('{{class}}', $className, $stub);
    }

    protected function replaceModelImports($stub): string
    {
        $modelNamespace = $this->getFullNamespace('model');
        $modelImport = "use {$modelNamespace}\\{$this->options['modelName']};";
        
        return str_replace('{{modelImport}}', $modelImport, $stub);
    }

    protected function replaceRequestImports($stub): string
    {
        $requestNamespace = $this->getFullNamespace('request');
        $modelName = $this->options['modelName'];
        
        $imports = "use {$requestNamespace}\\Store{$modelName}Request;\n";
        $imports .= "use {$requestNamespace}\\Update{$modelName}Request;";
        
        return str_replace('{{requestImports}}', $imports, $stub);
    }

    protected function replaceAuthMiddleware($stub): string
    {
        $middleware = '';
        
        if ($this->options['auth']) {
            $middleware = "\n    public function __construct()\n    {\n        \$this->middleware('auth');\n    }\n";
        }
        
        return str_replace('{{authMiddleware}}', $middleware, $stub);
    }

    protected function replaceModelVariable($stub): string
    {
        $variable = $this->getModelVariable();
        $variablePlural = $this->getModelVariablePlural();
        
        $stub = str_replace('{{modelVariable}}', $variable, $stub);
        $stub = str_replace('{{modelVariablePlural}}', $variablePlural, $stub);
        
        return $stub;
    }

    protected function replaceTableName($stub): string
    {
        return str_replace('{{tableName}}', $this->getTableName(), $stub);
    }

    protected function replaceViewPath($stub): string
    {
        return str_replace('{{viewPath}}', $this->getViewPath(), $stub);
    }

    protected function replaceRoutePath($stub): string
    {
        return str_replace('{{routePath}}', $this->getRoutePath(), $stub);
    }

    protected function replaceIndexMethod($stub): string
    {
        $modelName = $this->options['modelName'];
        $variablePlural = $this->getModelVariablePlural();
        
        $method = "    public function index()\n    {\n";
        
        // Add search functionality
        $method .= "        \$query = {$modelName}::query();\n\n";
        $method .= "        // Search functionality\n";
        $method .= "        if (request('search')) {\n";
        
        // Add searchable fields
        $searchableFields = $this->getSearchableFields();
        if (!empty($searchableFields)) {
            $method .= "            \$query->where(function(\$q) {\n";
            foreach ($searchableFields as $index => $field) {
                $operator = $index === 0 ? 'where' : 'orWhere';
                $method .= "                \$q->{$operator}('{$field}', 'like', '%' . request('search') . '%');\n";
            }
            $method .= "            });\n";
        }
        $method .= "        }\n\n";
        
        // Add sorting functionality
        $method .= "        // Sorting functionality\n";
        $method .= "        \$sortField = request('sort', 'id');\n";
        $method .= "        \$sortDirection = request('direction', 'desc');\n";
        $method .= "        \$query->orderBy(\$sortField, \$sortDirection);\n\n";
        
        // Add pagination
        $method .= "        \${$variablePlural} = \$query->paginate(config('crud-generator.pagination'));\n\n";
        
        if ($this->options['api']) {
            $method .= "        return response()->json(\${$variablePlural});\n";
        } else {
            $method .= "        return view('{$this->getViewPath()}.index', compact('{$variablePlural}'));\n";
        }
        
        $method .= "    }";
        
        return str_replace('{{indexMethod}}', $method, $stub);
    }

    protected function replaceCreateMethod($stub): string
    {
        if ($this->options['api']) {
            return str_replace('{{createMethod}}', '', $stub);
        }
        
        $method = "    public function create()\n    {\n";
        
        // Add relationship data for dropdowns
        foreach ($this->options['relationships'] as $relation) {
            if ($relation['type'] === 'belongsTo') {
                $var = Str::camel(Str::plural($relation['model']));
                $method .= "        \${$var} = \\App\\Models\\{$relation['model']}::all();\n";
            }
        }
        
        if (!empty($this->options['relationships'])) {
            $compactVars = [];
            foreach ($this->options['relationships'] as $relation) {
                if ($relation['type'] === 'belongsTo') {
                    $compactVars[] = "'" . Str::camel(Str::plural($relation['model'])) . "'";
                }
            }
            $compact = implode(', ', $compactVars);
            $method .= "\n        return view('{$this->getViewPath()}.create', compact({$compact}));\n";
        } else {
            $method .= "        return view('{$this->getViewPath()}.create');\n";
        }
        
        $method .= "    }";
        
        return str_replace('{{createMethod}}', $method, $stub);
    }

    protected function replaceStoreMethod($stub): string
    {
        $modelName = $this->options['modelName'];
        $variable = $this->getModelVariable();
        
        $method = "    public function store(Store{$modelName}Request \$request)\n    {\n";
        $method .= "        \$data = \$request->validated();\n\n";
        
        // Handle file uploads
        $fileFields = $this->getFileFields();
        if (!empty($fileFields)) {
            $method .= "        // Handle file uploads\n";
            foreach ($fileFields as $field) {
                $method .= "        if (\$request->hasFile('{$field}')) {\n";
                $method .= "            \$data['{$field}'] = \$request->file('{$field}')->store(config('crud-generator.storage_path'), 'public');\n";
                $method .= "        }\n\n";
            }
        }
        
        $method .= "        \${$variable} = {$modelName}::create(\$data);\n\n";
        
        if ($this->options['api']) {
            $method .= "        return response()->json(\${$variable}, 201);\n";
        } else {
            $method .= "        return redirect()->route('{$this->getRoutePath()}.index')\n";
            $method .= "            ->with('success', config('crud-generator.messages.created'));\n";
        }
        
        $method .= "    }";
        
        return str_replace('{{storeMethod}}', $method, $stub);
    }

    protected function replaceEditMethod($stub): string
    {
        if ($this->options['api']) {
            return str_replace('{{editMethod}}', '', $stub);
        }
        
        $modelName = $this->options['modelName'];
        $variable = $this->getModelVariable();
        
        $method = "    public function edit({$modelName} \${$variable})\n    {\n";
        
        // Add relationship data for dropdowns
        foreach ($this->options['relationships'] as $relation) {
            if ($relation['type'] === 'belongsTo') {
                $var = Str::camel(Str::plural($relation['model']));
                $method .= "        \${$var} = \\App\\Models\\{$relation['model']}::all();\n";
            }
        }
        
        $compactVars = ["'{$variable}'"];
        foreach ($this->options['relationships'] as $relation) {
            if ($relation['type'] === 'belongsTo') {
                $compactVars[] = "'" . Str::camel(Str::plural($relation['model'])) . "'";
            }
        }
        $compact = implode(', ', $compactVars);
        
        $method .= "\n        return view('{$this->getViewPath()}.edit', compact({$compact}));\n";
        $method .= "    }";
        
        return str_replace('{{editMethod}}', $method, $stub);
    }

    protected function replaceUpdateMethod($stub): string
    {
        $modelName = $this->options['modelName'];
        $variable = $this->getModelVariable();
        
        $method = "    public function update(Update{$modelName}Request \$request, {$modelName} \${$variable})\n    {\n";
        $method .= "        \$data = \$request->validated();\n\n";
        
        // Handle file uploads with old file deletion
        $fileFields = $this->getFileFields();
        if (!empty($fileFields)) {
            $method .= "        // Handle file uploads\n";
            foreach ($fileFields as $field) {
                $method .= "        if (\$request->hasFile('{$field}')) {\n";
                $method .= "            // Delete old file\n";
                $method .= "            if (\${$variable}->{$field}) {\n";
                $method .= "                \\Storage::disk('public')->delete(\${$variable}->{$field});\n";
                $method .= "            }\n";
                $method .= "            \$data['{$field}'] = \$request->file('{$field}')->store(config('crud-generator.storage_path'), 'public');\n";
                $method .= "        }\n\n";
            }
        }
        
        $method .= "        \${$variable}->update(\$data);\n\n";
        
        if ($this->options['api']) {
            $method .= "        return response()->json(\${$variable});\n";
        } else {
            $method .= "        return redirect()->route('{$this->getRoutePath()}.index')\n";
            $method .= "            ->with('success', config('crud-generator.messages.updated'));\n";
        }
        
        $method .= "    }";
        
        return str_replace('{{updateMethod}}', $method, $stub);
    }

    protected function replaceDestroyMethod($stub): string
    {
        $modelName = $this->options['modelName'];
        $variable = $this->getModelVariable();
        
        $method = "    public function destroy({$modelName} \${$variable})\n    {\n";
        
        // Handle file deletion
        $fileFields = $this->getFileFields();
        if (!empty($fileFields)) {
            $method .= "        // Delete associated files\n";
            foreach ($fileFields as $field) {
                $method .= "        if (\${$variable}->{$field}) {\n";
                $method .= "            \\Storage::disk('public')->delete(\${$variable}->{$field});\n";
                $method .= "        }\n";
            }
            $method .= "\n";
        }
        
        $method .= "        \${$variable}->delete();\n\n";
        
        if ($this->options['api']) {
            $method .= "        return response()->json(['message' => 'Deleted successfully'], 200);\n";
        } else {
            $method .= "        return redirect()->route('{$this->getRoutePath()}.index')\n";
            $method .= "            ->with('success', config('crud-generator.messages.deleted'));\n";
        }
        
        $method .= "    }";
        
        return str_replace('{{destroyMethod}}', $method, $stub);
    }

    protected function getFileFields(): array
    {
        return collect($this->options['fields'])
            ->filter(fn($field) => in_array($field['type'], ['file', 'image']))
            ->pluck('name')
            ->toArray();
    }

    protected function getSearchableFields(): array
    {
        return collect($this->options['fields'])
            ->filter(fn($field) => in_array($field['type'], ['string', 'text', 'email']))
            ->pluck('name')
            ->toArray();
    }

    protected function getControllerPath(): string
    {
        $namespacePath = str_replace('\\', '/', $this->options['namespace']);
        $className = $this->options['modelName'] . 'Controller';
        
        return app_path("Http/Controllers/{$namespacePath}/{$className}.php");
    }

}
 