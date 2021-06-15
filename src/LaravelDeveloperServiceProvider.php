<?php

namespace Binarcode\LaravelDeveloper;

use Binarcode\LaravelDeveloper\Commands\PruneCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;

class LaravelDeveloperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/developer.php' => config_path('developer.php'),
            ], 'developer-config');

            $migrationFileName = 'create_laravel_developer_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'developer-migrations');
            }

            $this->commands([
                PruneCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/developer.php', 'developer');
        $this->registerMacros();
    }

    protected function registerMacros(): self
    {
        TestResponse::macro('dumpWithoutTrace', function () {
            /**
             * @var TestResponse $this
             */
            $content = $this->getContent();

            /**
             * @var string $content
             */
            $json = json_decode($content);

            if (json_last_error() === JSON_ERROR_NONE) {
                $content = $json;
            }

            /**
             * @var object $content
             */
            unset($content->trace);

            dump($content);

            return $this;
        });

        return $this;
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
