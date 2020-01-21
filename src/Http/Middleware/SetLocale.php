<?php

namespace Pebble\Routes\Http\Middleware;

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
        $route = $routeLoader->getRoutes(['uri' => $request->path()]);

        if($route) {
            app()->setLocale($route->locale);
        }

        return $next($request);
    }
}
