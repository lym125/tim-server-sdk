<?php

namespace Lym125\Tim;

use Illuminate\Support\ServiceProvider;

class TimServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/tim.php' => config_path('tim.php')
        ], 'config');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tim.php',
            'tim'
        );

        $this->app->singleton('lym125.tim', function ($app) {
            return new TimManager($app);
        });
    }
}
