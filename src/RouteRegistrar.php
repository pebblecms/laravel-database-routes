<?php

namespace Pebble\Routes;

use DateInterval;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Pebble\Routes\Contracts\Redirection as RedirectionContract;
use Pebble\Routes\Contracts\Route as RouteContract;

class RouteRegistrar
{
    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var \Illuminate\Cache\CacheManager */
    protected $cacheManager;

    /** @var DateInterval|int */
    public static $cacheExpirationTime;

    /** @var string */
    public static $cacheKey;

    /** @var string */
    public static $cacheModelKey;

    /** @var string */
    protected $redirectionClass;

    /** @var \Illuminate\Support\Collection */
    protected $redirections;

    /** @var string */
    protected $routeClass;

    /** @var \Illuminate\Support\Collection */
    protected $routes;

    /**
     * PermissionRegistrar constructor.
     *
     * @param \Illuminate\Cache\CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->setRouteClass(config('pebble-routes.models.route'));
        $this->setRedirectionClass(config('pebble-routes.models.redirection'));
        $this->cacheManager = $cacheManager;
        $this->initializeCache();
    }

    protected function initializeCache()
    {
        self::$cacheExpirationTime = config('pebble-routes.cache.expiration_time', config('pebble-routes.cache_expiration_time'));
        self::$cacheKey = config('pebble-routes.cache.key');
        self::$cacheModelKey = config('pebble-routes.cache.model_key');
        $this->cache = $this->getCacheStoreFromConfig();
    }

    protected function getCacheStoreFromConfig(): Repository
    {
        // the 'default' fallback here is from the pebble-routes.php config file, where 'default' means to use config(cache.default)
        $cacheDriver = config('pebble-routes.cache.store', 'default');
        // when 'default' is specified, no action is required since we already have the default instance
        if ($cacheDriver === 'default') {
            return $this->cacheManager->store();
        }
        // if an undefined cache store is specified, fallback to 'array' which is Laravel's closest equiv to 'none'
        if (!\array_key_exists($cacheDriver, config('cache.stores'))) {
            $cacheDriver = 'array';
        }
        return $this->cacheManager->store($cacheDriver);
    }

    /**
     * @return void
     */
    public function registerRoutes(): bool
    {
        $tableNames = config('pebble-routes.table_names');

        if(Schema::hasTable($tableNames['routes']) && Schema::hasTable($tableNames['redirections'])) {
            $routes = $this->getRoutes();
            $routes->each(function($route) {
                app()->router
                    ->addRoute($route->verbs, $route->uri, $route->action)
                    ->name(optional($route)->name)
                    ->middleware($route->middleware);
            });

            $redirections = $this->getRedirections();
            $redirections->each(function($redirection) {
                app()->router->any('\Illuminate\Routing\RedirectController')
                    ->defaults('destination', $redirection->destination)
                    ->defaults('status', $redirection->status);
            });
            return true;
        }

        return false;
    }

    /**
     * Flush the cache.
     */
    public function forgetCachedRedirections()
    {
        $this->redirections = null;
        return $this->cache->forget(self::$cacheKey);
    }

    /**
     * Flush the cache.
     */
    public function forgetCachedRoutes()
    {
        $this->routes = null;
        return $this->cache->forget(self::$cacheKey);
    }

    /**
     * Get the routes based on the passed params.
     *
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRoutes(array $params = []): Collection
    {
        if ($this->routes === null) {
            $this->routes = $this->cache->remember(self::$cacheKey, self::$cacheExpirationTime, function () {
                return $this->getRouteClass()->get();
            });
        }
        $routes = clone $this->routes;
        foreach ($params as $attr => $value) {
            $routes = $routes->where($attr, $value);
        }
        return $routes;
    }

    public function getRedirections(array $params = []): Collection
    {
        if ($this->redirections === null) {
            $this->redirections = $this->cache->remember(self::$cacheKey, self::$cacheExpirationTime, function () {
                return $this->getRedirectionClass()->get();
            });
        }
        $redirections = clone $this->redirections;
        foreach ($params as $attr => $value) {
            $redirections = $redirections->where($attr, $value);
        }
        return $redirections;
    }

    /**
     * Get an instance of the redirection class.
     *
     * @return \Pebble\Routes\Contracts\Redirection
     */
    public function getRedirectionClass(): RedirectionContract
    {
        return app($this->redirectionClass);
    }

    /**
     * Get an instance of the route class.
     *
     * @return \Pebble\Routes\Contracts\Route
     */
    public function getRouteClass(): RouteContract
    {
        return app($this->routeClass);
    }

    public function setRedirectionClass($redirectionClass)
    {
        $this->redirectionClass = $redirectionClass;
        return $this;
    }

    public function setRouteClass($routeClass)
    {
        $this->routeClass = $routeClass;
        return $this;
    }

    /**
     * Get the instance of the Cache Store.
     *
     * @return \Illuminate\Contracts\Cache\Store
     */
    public function getCacheStore(): Store
    {
        return $this->cache->getStore();
    }
}