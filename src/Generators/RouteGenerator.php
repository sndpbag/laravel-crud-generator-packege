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

    // protected function generateRouteLine(): string
    // {
    //     $controllerNamespace = $this->getFullNamespace('controller');
    //     $controllerClass = $this->options['modelName'] . 'Controller';
    //     $fullControllerPath = "{$controllerNamespace}\\{$controllerClass}";
    //     $routePath = $this->getRoutePath();

    //     if ($this->options['api']) {
    //         $route = "Route::apiResource('{$routePath}', \\{$fullControllerPath}::class);";
    //     } else {
    //         $route = "Route::resource('{$routePath}', \\{$fullControllerPath}::class);";
    //     }

    //     // Add middleware if auth is enabled
    //     if ($this->options['auth'] && !$this->options['api']) {
    //         $route = "Route::middleware(['auth'])->group(function () {\n    {$route}\n});";
    //     }

    //     return $route;
    // }


    protected function generateRouteLine(): string
    {
        $controllerNamespace = $this->getFullNamespace('controller');
        $controllerClass = $this->options['modelName'] . 'Controller';
        $fullControllerPath = "{$controllerNamespace}\\{$controllerClass}";
        $routePath = $this->getRoutePath();
        $controller = "\\{$fullControllerPath}::class";

        if ($this->options['api']) {
            $route = "Route::apiResource('{$routePath}', {$controller});";
            // (আপনি চাইলে API-এর জন্যও সফট ডিলিট রুট যোগ করতে পারেন)

        } else {
            // Web routes
            $route = "Route::resource('{$routePath}', {$controller});";

            if ($this->options['softDelete']) {
                $route .= "\n    Route::get('{$routePath}/trashed', [{$controller}, 'trashed'])->name('{$routePath}.trashed');";
                // Restore-এর জন্য POST ব্যবহার করা ভালো
                $route .= "\n    Route::post('{$routePath}/{id}/restore', [{$controller}, 'restore'])->name('{$routePath}.restore');"; 
                $route .= "\n    Route::delete('{$routePath}/{id}/force', [{$controller}, 'forceDelete'])->name('{$routePath}.forceDelete');";
            }
        }

        // Add middleware if auth is enabled
        if ($this->options['auth'] && !$this->options['api']) {
            // গ্রুপটিকে আলাদা লাইনে রাখুন যাতে সফট ডিলিট রুটগুলোও এর ভেতরে থাকে
            $route = "Route::middleware(['auth'])->group(function () {\n    {$route}\n});";
        }

        return $route;
    }
}