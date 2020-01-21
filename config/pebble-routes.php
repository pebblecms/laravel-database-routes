<?php

return [
    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'pebble.routes.cache',
        'model_key' => 'name',
        'store' => 'default',
    ],
    'middlewares' => [
        'set_locale' => \Pebble\Routes\Http\Middleware\SetLocale::class
    ],
    'models' => [
        'redirection' => \Pebble\Routes\Models\Redirection::class,
        'route' => \Pebble\Routes\Models\Route::class
    ],
    'table_names' => [
        'middlewares' => 'middlewares',
        'routes' => 'routes',
        'redirections' => 'redirections'
    ]
];