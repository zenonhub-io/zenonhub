<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Providers;

use App\Domains\Indexer\Actions\InsertAccountBlock;
use App\Domains\Indexer\Actions\InsertMomentum;
use App\Domains\Indexer\Services\Indexer;
use App\Domains\Nom\Services\ZenonSdk;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class IndexerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InsertMomentum::class, fn ($app) => new InsertMomentum);

        $this->app->bind(InsertAccountBlock::class, fn ($app) => new InsertAccountBlock($app->make(ZenonSdk::class)));

        $this->app->singleton(Zenon::class, function ($app, $params) {
            $nodeUrl = $params['node'] ?? config('services.zenon.node_url');
            $throwErrors = $params['throwErrors'] ?? config('services.zenon.throw_errors');

            return new Zenon($nodeUrl, $throwErrors);
        });

        $this->app->singleton(Indexer::class, fn ($app, $params) => new Indexer(
            $app->make(ZenonSdk::class),
            $app->make(InsertMomentum::class),
            $app->make(InsertAccountBlock::class),
        ));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::shouldBeStrict();
    }
}
