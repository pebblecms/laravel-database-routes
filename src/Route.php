<?php

namespace Pebble\Routes;

use Pebble\Routes\Contracts\Route as RouteContract;
use Pebble\Routes\Contracts\RouteFactory;

class Route implements RouteFactory
{
    public static function any($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }

    public static function delete($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
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

    public static function match($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
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
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }

    public static function permanentRedirect($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }

    public static function post($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }

    public static function put($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
    }

    public static function redirect($uri, $action): RouteContract
    {
        return app(config('pebble-routes.models.route'))::create([
            'uri' => $uri,
            'verbs' => [ 'GET', 'HEAD' ],
            'action' => $action
        ]);
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