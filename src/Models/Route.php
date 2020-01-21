<?php

namespace Pebble\Routes\Models;

use Pebble\Routes\Contracts\Route as RouteContract;
use Illuminate\Database\Eloquent\Model;
use Pebble\Routes\RouteRegistrar;

class Route extends Model implements RouteContract
{
    protected $casts = [
        'defaults' => 'array',
        'middleware' => 'array',
        'verbs' => 'array'
    ];
    protected $fillable = ['action', 'defaults', 'middleware', 'uri', 'verbs', 'locale'];
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