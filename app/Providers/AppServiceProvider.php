<?php

namespace App\Providers;

use App\Services\Seo\MetaTags;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register() : void
    {
        //
        // Helpers

        $this->app->singleton(Meta::class, fn () => new MetaTags);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() : void
    {
        //
    }
}
