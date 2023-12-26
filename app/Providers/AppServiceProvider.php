<?php

namespace App\Providers;

use App\Services\BitQuery;
use App\Services\CoinGecko;
use App\Services\Discord\DiscordWebHook;
use App\Services\Meta;
use App\Services\PlasmaBot;
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

        $this->app->singleton(ZenonCli::class, function ($app, $params) {
            return new ZenonCli($params['node_url'], $params['keystore'], $params['passphrase']);
        });

        $this->app->singleton(PlasmaBot::class, function ($app, $params) {
            return new PlasmaBot();
        });

        //
        // Integrations

        $this->app->singleton(CoinGecko::class, function ($app, $params) {
            return new CoinGecko();
        });

        $this->app->singleton(BitQuery::class, function ($app, $params) {
            return new BitQuery(config('services.bitquery.api_key'));
        });

        $this->app->singleton(DiscordWebHook::class, function ($app, $params) {
            $httpClient = new Client();

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

        $this->app->singleton(Meta::class, function () {
            return new Meta();
        });

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
