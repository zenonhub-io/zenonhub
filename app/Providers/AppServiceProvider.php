<?php

namespace App\Providers;

use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        $this->app->instance(DatabaseChannel::class, new \App\Channels\DatabaseChannel());
    }
}
