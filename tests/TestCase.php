<?php

namespace Pebble\Routes\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Pebble\Routes\RouteRegistrar;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        config(['pebble-routes.cache.expiration_time' => -1]);

        $this->setUpDatabase($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Pebble\Routes\RoutesServiceProvider::class,
        ];
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\DatabaseStore ||
            $app[RouteRegistrar::class]->getCacheStore() instanceof \Illuminate\Cache\DatabaseStore) {
            $this->createCacheTable();
        }

        include_once __DIR__.'/../database/migrations/create_routes_tables.php.stub';

        (new \CreateRoutesTables())->up();
    }

    /**
     * Reload the routes.
     */
    protected function reloadRoutes()
    {
        app(RouteRegistrar::class)->forgetCachedPermissions();
    }

    public function createCacheTable()
    {
        Schema::create('cache', function ($table) {
            $table->string('key')->unique();
            $table->text('value');
            $table->integer('expiration');
        });
    }
}