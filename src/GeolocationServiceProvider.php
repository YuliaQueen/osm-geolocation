<?php

namespace Qween\Geolocation;

use Illuminate\Support\ServiceProvider;
use Qween\Geolocation\Services\GeolocationService;
use Qween\Geolocation\Services\OpenStreetMapService;

class GeolocationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/geolocation.php', 'geolocation'
        );

        $this->app->singleton(GeolocationService::class, function ($app) {
            return new OpenStreetMapService($app['config']['geolocation']);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/geolocation.php' => config_path('geolocation.php'),
        ], 'geolocation-config');
    }
}