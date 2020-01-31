<?php

namespace Pebble\Routes\Middleware;

use Closure;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeLoader = resolve(\Pebble\Routes\RouteRegistrar::class);
        $routes = $routeLoader->getRoutes(['uri' => $request->path()]);

        if($routes) {
            app()->setLocale(optional($routes->first())->locale);
        }

        return $next($request);
    }
}
