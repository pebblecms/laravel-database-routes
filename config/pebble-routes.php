<?php

return [
    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'pebble.routes.cache',
        'model_key' => 'name',
        'store' => 'default',
    ],
    'models' => [
        'route' => \Pebble\Routes\Models\Route::class
    ],
    'table_names' => [
        'middlewares' => 'middlewares',
        'routes' => 'routes',
        'redirections' => 'redirections'
    ]
];