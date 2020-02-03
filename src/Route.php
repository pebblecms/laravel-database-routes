<?php

namespace Pebble\Routes;

use Illuminate\Routing\Router;
use Pebble\Routes\Contracts\Route as RouteContract;
use Pebble\Routes\Contracts\RouteFactory;

class Route implements RouteFactory
{
    public static function any($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => Router::$verbs,
            'action' => $action
        ]);
    }

    public static function delete($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'DELETE' ],
            'action' => $action
        ]);
    }

    public static function get($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }

    public static function match($methods, $uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => array_map('strtoupper', (array) $methods),
            'action' => $action
        ]);
    }

    public static function options($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }

    public static function patch($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'PATCH' ],
            'action' => $action
        ]);
    }

    public static function permanentRedirect($uri, $destination): RouteContract
    {
        return app(config('pebble-routes.models.redirect'))::create([
            'uri' => $uri,
            'destination' => $destination,
            'statis' => 301
        ]);
    }

    public static function post($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'POST' ],
            'action' => $action
        ]);
    }

    public static function put($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'PUT' ],
            'action' => $action
        ]);
    }

    public static function redirect($uri, $action): RouteContract
    {
        // TODO
    }

    public static function view($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }
}