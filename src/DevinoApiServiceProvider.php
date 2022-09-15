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
	public function register () {
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
	public function boot () {
		//
		$this->publishes([
			__DIR__ . '/../config/devino.php' => config_path('devino.php'),
		]);

		if ($this->app->runningInConsole()) {
			$this->commands([
				CheckStatus::class
			]);
			$this->app->booted(function () {
				$schedule = $this->app->make(Schedule::class);
				$schedule->command('devino:check')->everyMinute();
			});
			if (!class_exists('DeninoNotification')) {
				$this->publishes([
					__DIR__ . '/../database/migrations/create_devino_notifications_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_devino_notifications_table.php'),
					// you can add any number of migrations here
				], 'migrations');
			}
		}
	}
}
