<?php

namespace Pebble\Routes;

use Illuminate\Cache\CacheManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Pebble\Routes\Contracts\Redirection as RedirectionContract;
use Pebble\Routes\Contracts\Route as RouteContract;
use Pebble\Routes\RouteRegistrar;

class RoutesServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the package.
     *
     * @var array
     */
    /*protected $listen = [
        \Pebble\Routes\Events\RouteWasCreated::class => [
            \Pebble\Routes\Listeners\Something::class,
        ]
    ];*/

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

        $this->bootMiddlewares();

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

    protected function bootMiddlewares()
    {
        $this->app->make('Illuminate\Contracts\Http\Kernel')->prependMiddleware(config('pebble-routes.middlewares.set_locale'));
    }

    /*
    private function registerListeners()
    {
        foreach($this->listen as $event => $listeners) {
            foreach($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
    //*/

    protected function registerModelBindings()
    {
        $models = $this->app->config['pebble-routes.models'];
        $this->app->bind(RedirectionContract::class, $models['redirection']);
        $this->app->bind(RouteContract::class, $models['route']);
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

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path . '*_create_routes_tables.php');
            })
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_create_routes_tables.php")
            ->first();
    }
}