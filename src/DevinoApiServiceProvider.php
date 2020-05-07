<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use NotificationChannels\DevinoApi\DevinoApi;

class DevinoApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(DevinoApi::class, static function ($app) {
            return new DevinoApi();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'config/devino.php' => config_path('devino.php'),
        ]);
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/database/2020_04_28_174807_create_devino_table.php');
    }
}