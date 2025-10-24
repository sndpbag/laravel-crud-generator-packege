<?php

namespace Sndpbag\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use Sndpbag\CrudGenerator\Commands\MakeCrudCommand;
use Sndpbag\CrudGenerator\Commands\DeleteCrudCommand;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/crud-generator.php', 'crud-generator'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../config/crud-generator.php' => config_path('crud-generator.php'),
        ], 'crud-generator-config');

        // Publish stubs
        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs/crud-generator'),
        ], 'crud-generator-stubs');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCrudCommand::class,
                DeleteCrudCommand::class,
            ]);
        }
    }
}