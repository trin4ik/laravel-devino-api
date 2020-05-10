<?php

namespace Trin4ik\DevinoApi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Trin4ik\DevinoApi\Console\CheckStatus;

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
        $this->app->bind('sms', static function ($app) {
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
            __DIR__.'/../config/devino.php' => config_path('devino.php'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2020_04_28_174807_create_devino_table.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckStatus::class
            ]);
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('devino:check')->everyMinute();
        });
    }
}
