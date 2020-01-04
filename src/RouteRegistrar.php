<?php

namespace Pebble\Routes;

use Pebble\Routes\Models\RouteInterface;

class RouteRegistrar
{
    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var \Illuminate\Cache\CacheManager */
    protected $cacheManager;

    /** @var string */
    protected $routeClass;

    /** @var \Illuminate\Support\Collection */
    protected $routes;

    /** @var DateInterval|int */
    public static $cacheExpirationTime;

    /** @var string */
    public static $cacheKey;

    /** @var string */
    public static $cacheModelKey;

    /**
     * PermissionRegistrar constructor.
     *
     * @param \Illuminate\Cache\CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->routeClass = config('pebble-routes.models.route');
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

    protected function getCacheStoreFromConfig(): \Illuminate\Contracts\Cache\Repository
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
     * // TODO: register redirections, apply middlewares...
     *
     * @return bool
     */
    public function registerRoutes(): bool
    {
        $routes = $this->getRoutes();
        $routes->each(function($route) {
            app()->router->addRoute($route->action, $route->uri, $route->action);
        });

        return true;
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
     * TODO: filter routes using parameters to optimize sql query
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

    /**
     * Get an instance of the permission class.
     *
     * @return \Pebble\Routes\Models\RouteInterface
     */
    public function getRouteClass(): RouteInterface
    {
        return app($this->routeClass);
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
    public function getCacheStore(): \Illuminate\Contracts\Cache\Store
    {
        return $this->cache->getStore();
    }
}