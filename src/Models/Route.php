<?php

namespace Pebble\Routes\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model implements RouteInterface
{
    protected $casts = [
        'action' => 'array',
        'middleware' => 'array',
    ];
    protected $fillable = ['uri', 'middleware', 'method', 'action'];
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
}
