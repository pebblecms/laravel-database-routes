<?php

namespace Pebble\Routes\Models;

use Pebble\Routes\Contracts\Route as RouteContract;
use Pebble\Routes\RouteRegistrar;
use Illuminate\Database\Eloquent\Model;

class Route extends Model implements RouteContract
{
    protected $casts = [
        'defaults' => 'array',
        'middleware' => 'array',
        'verbs' => 'array'
    ];

    protected $fillable = [
        'action',
        'defaults',
        'domain',
        'middleware',
        'uri',
        'verbs',
        'locale',
        'name'
    ];

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * Override default constructor to set table using config file.
     * @param array $attributes [fields]
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('pebble-routes.table_names.routes'));
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function () {
            resolve(RouteRegistrar::class)->forgetCachedRoutes();
        });

        static::updated(function () {
            resolve(RouteRegistrar::class)->forgetCachedRoutes();
        });

        static::deleted(function () {
            resolve(RouteRegistrar::class)->forgetCachedRoutes();
        });
    }
}