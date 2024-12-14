<?php

namespace Pedramkousari\ExpireCommand\Providers;

use Illuminate\Support\ServiceProvider;
use Pedramkousari\ExpireCommand\ExpireCommand;

class ExpireCommandServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            ExpireCommand::class,
        ]);
    }

    public function boot()
    {
        //
    }
}