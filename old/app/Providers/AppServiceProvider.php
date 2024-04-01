<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Nom\Services\PlasmaBot;
use App\Services\BitQuery;
use App\Services\BridgeStatus;
use App\Services\CoinGecko;
use App\Services\Discord\DiscordWebHook;
use App\Services\Meta;
use App\Services\ZenonCli;
use App\Services\ZenonSdk;
use GuzzleHttp\Client;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use pnoeric\DiscourseAPI;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        // Zenon

        $this->app->singleton(ZenonSdk::class, function ($app, $params) {
            $node = $params['node'] ?? config('zenon.node_url');

            return (new ZenonSdk($node))->getSdk();
        });

        $this->app->singleton(ZenonCli::class, fn ($app, $params) => new ZenonCli($params['node_url'], $params['keystore'], $params['passphrase']));

        $this->app->singleton(PlasmaBot::class, fn ($app, $params) => new PlasmaBot);

        //
        // Integrations

        $this->app->singleton(BridgeStatus::class, fn ($app, $params) => new BridgeStatus);

        $this->app->singleton(CoinGecko::class, fn ($app, $params) => new CoinGecko);

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

        $this->app->singleton(Meta::class, fn () => new Meta);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
    }
}
