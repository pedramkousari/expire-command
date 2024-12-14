<?php

namespace Pedramkousari\ExpireCommand\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Pedramkousari\ExpireCommand\ExpireCommand;

class ExpireCommandServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            ExpireCommand::class,
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/expire.php', 'expire'
        );

    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/expire.php' => config_path('expire.php'),
            ]);

            $this->publishes([
                __DIR__.'/../lang' => $this->app->langPath(),
            ]);
        }

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'expire');

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $this->configureSchedule($schedule);
        });
    }

    protected function configureSchedule(Schedule $schedule)
    {

        $schedule
            ->command(config('expire.signature'). ' --check')
            ->cron(config('expire.cron'));
    }
}
