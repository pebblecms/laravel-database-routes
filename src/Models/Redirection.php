<?php

namespace Pebble\Routes\Models;

use Pebble\Routes\Contracts\Redirection as RedirectionContract;
use Illuminate\Database\Eloquent\Model;
use Pebble\Routes\RouteRegistrar;

class Redirection extends Model implements RedirectionContract
{
    protected $fillable = ['uri', 'destination', 'status'];
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Override default constructor to set table using config file.
     * @param array $attributes [fields]
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('pebble-routes.table_names.redirections'));
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
            resolve(RouteRegistrar::class)->forgetCachedRedirections();
        });

        static::updated(function () {
            resolve(RouteRegistrar::class)->forgetCachedRedirections();
        });

        static::deleted(function () {
            resolve(RouteRegistrar::class)->forgetCachedRedirections();
        });
    }
}