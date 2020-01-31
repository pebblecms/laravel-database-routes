<?php

namespace Pebble\Routes\Contracts;

interface RouteFactory
{
    public static function get($uri, $action);
}
