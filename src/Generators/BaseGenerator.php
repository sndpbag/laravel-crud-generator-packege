<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseGenerator
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    abstract public function generate(): array;

    // protected function getStub(string $type): string
    // {
    //     // Check if user has published custom stubs
    //     $customStubPath = base_path("stubs/crud-generator/{$type}.stub");
        
    //     if (File::exists($customStubPath)) {
    //         return File::get($customStubPath);
    //     }

    //     // Use default stub
    //     $defaultStubPath = __DIR__ . "/../../stubs/{$type}.stub";
        
    //     if (File::exists($defaultStubPath)) {
    //         return File::get($defaultStubPath);
    //     }

    //     return '';
    // }

 protected function getStub(string $type): string
    {
        // Check if user has published custom stubs
        $customStubPath = base_path("stubs/crud-generator/{$type}.stub");
        
        if (File::exists($customStubPath)) {
            return File::get($customStubPath);
        }

        // Use default stub
        $defaultStubPath = __DIR__ . "/../../stubs/{$type}.stub";
        
        if (File::exists($defaultStubPath)) {
            return File::get($defaultStubPath);
        }

        return '';
    }

    protected function getFullNamespace(string $type): string
    {
        $baseNamespace = config("crud-generator.namespace.{$type}");
        
        if (!empty($this->options['namespace'])) {
            return $baseNamespace . '\\' . $this->options['namespace'];
        }

        return $baseNamespace;
    }

    protected function ensureDirectoryExists(string $path): void
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function getTableName(): string
    {
        return Str::plural(Str::snake($this->options['modelName']));
    }

    protected function getModelVariable(): string
    {
        return Str::camel($this->options['modelName']);
    }

    protected function getModelVariablePlural(): string
    {
        return Str::camel(Str::plural($this->options['modelName']));
    }

    protected function getViewPath(): string
    {
        $namespace = strtolower(str_replace('\\', '/', $this->options['namespace']));
        $viewName = Str::plural(Str::snake($this->options['modelName']));
        
        if ($namespace) {
            return "{$namespace}.{$viewName}";
        }
        
        return $viewName;
    }

    protected function getRoutePath(): string
    {
        $namespace = strtolower(str_replace('\\', '/', $this->options['namespace']));
        $routeName = Str::plural(Str::snake($this->options['modelName']));
        
        if ($namespace) {
            return "{$namespace}/{$routeName}";
        }
        
        return $routeName;
    }
}