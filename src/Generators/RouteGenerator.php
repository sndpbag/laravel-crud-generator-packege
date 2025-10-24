<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RouteGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $routeFile = $this->options['api'] ? 'api' : 'web';
        $routePath = base_path("routes/{$routeFile}.php");

        if (!File::exists($routePath)) {
            return [
                'success' => false,
                'message' => "Route file not found: {$routePath}"
            ];
        }

        $content = File::get($routePath);
        $routeLine = $this->generateRouteLine();

        // Check if route already exists
        if (Str::contains($content, $routeLine)) {
            return [
                'success' => false,
                'message' => 'Route already exists',
                'file' => $routeFile
            ];
        }

        // Append route to file
        $content = rtrim($content) . "\n\n" . $routeLine . "\n";
        File::put($routePath, $content);

        return [
            'success' => true,
            'file' => $routeFile
        ];
    }

    protected function generateRouteLine(): string
    {
        $controllerNamespace = $this->getFullNamespace('controller');
        $controllerClass = $this->options['modelName'] . 'Controller';
        $fullControllerPath = "{$controllerNamespace}\\{$controllerClass}";
        $routePath = $this->getRoutePath();

        if ($this->options['api']) {
            $route = "Route::apiResource('{$routePath}', \\{$fullControllerPath}::class);";
        } else {
            $route = "Route::resource('{$routePath}', \\{$fullControllerPath}::class);";
        }

        // Add middleware if auth is enabled
        if ($this->options['auth'] && !$this->options['api']) {
            $route = "Route::middleware(['auth'])->group(function () {\n    {$route}\n});";
        }

        return $route;
    }
}