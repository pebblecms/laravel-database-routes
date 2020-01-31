<?php

namespace Pebble\Routes\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;
use Pebble\Routes\Route;
use Pebble\Routes\RouteRegistrar;

class RouteTest extends TestCase
{
    use WithoutMiddleware;

    public $routeLoader;

    public function setUp(): void
    {
        parent::setUp();

        $this->routeLoader = new RouteRegistrar(new \Illuminate\Cache\CacheManager($this->app));
    }

    public function test_create_route_get()
    {
        $route = Route::get('test', '\Pebble\Routes\Tests\FakeController@test');

        $this->routeLoader->registerRoutes();

        $this->assertEquals('test', $route->uri);

        $response = $this->get('test');
        $response->assertStatus(Response::HTTP_OK);
    }
}