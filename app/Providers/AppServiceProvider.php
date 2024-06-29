<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\BitQuery;
use App\Services\BridgeStatus;
use App\Services\Discord\DiscordWebHook;
use App\Services\Seo\MetaTags;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Facades\Actions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BridgeStatus::class, fn ($app, $params) => new BridgeStatus);

        $this->app->singleton(BitQuery::class, fn ($app, $params) => new BitQuery(config('services.bitquery.api_key')));

        $this->app->singleton(DiscordWebHook::class, function ($app, $params) {
            $httpClient = new Client;

            return new DiscordWebHook($httpClient, $params['webhook']);
        });

        $this->app->singleton('discourse.api', function ($app, $params) {
            return new DiscourseAPI(
                config('services.discourse.host'),
                config('services.discourse.key')
            );
        });

        //
        // Helpers

        $this->app->singleton(MetaTags::class, fn () => new MetaTags);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureModels();

        Paginator::useBootstrapFive();
    }

    private function configureActions(): void
    {
        Actions::registerCommands([
            'app/Domains/Common/Actions',
            'app/Domains/Indexer/Actions',
            'app/Domains/Nom/Actions',
        ]);
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict();
    }
}
