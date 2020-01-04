<?php

namespace Pebble\Routes;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Pebble\Routes\Models\RouteInterface;
use Pebble\Routes\RouteRegistrar;

class RoutesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(RouteRegistrar $routeLoader, Filesystem $filesystem)
    {
        $this->publishes([
            __DIR__.'/../config/pebble-routes.php' => config_path('pebble-routes.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_routes_tables.php.stub' => $this->getMigrationFileName($filesystem),
        ], 'migrations');

        $this->registerModelBindings();

        $routeLoader->registerRoutes();

        $this->app->singleton(RouteRegistrar::class, function ($app) use ($routeLoader) {
            return $routeLoader;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/pebble-routes.php',
            'pebble-routes'
        );
    }

    protected function registerModelBindings()
    {
        $config = $this->app->config['pebble-routes.models'];
        $this->app->bind(RouteInterface::class, $config['route']);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path.'*_create_routes_tables.php');
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_create_routes_tables.php")
            ->first();
    }
}
