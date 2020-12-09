<?php

namespace Binarcode\LaravelDeveloper;

use Binarcode\LaravelDeveloper\Commands\LaravelDeveloperCommand;
use Illuminate\Support\ServiceProvider;

class LaravelDeveloperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-developer.php' => config_path('laravel-developer.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/laravel-developer'),
            ], 'views');

            $migrationFileName = 'create_laravel_developer_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }

            $this->commands([
                LaravelDeveloperCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-developer');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-developer.php', 'laravel-developer');
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
