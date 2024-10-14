<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Indexer\InsertAccountBlock;
use App\Actions\Indexer\InsertMomentum;
use App\Services\Indexer;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Support\ServiceProvider;

class IndexerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InsertMomentum::class, fn ($app) => new InsertMomentum);

        $this->app->bind(InsertAccountBlock::class, fn ($app) => new InsertAccountBlock);

        $this->app->singleton(Indexer::class, fn ($app, $params) => new Indexer(
            $app->make(ZenonSdk::class),
            $app->make(InsertMomentum::class),
            $app->make(InsertAccountBlock::class),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
