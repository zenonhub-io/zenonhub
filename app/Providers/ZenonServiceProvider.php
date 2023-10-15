<?php

namespace App\Providers;

use App\Services\CoinGecko;
use App\Services\Discord\DiscordWebHook;
use App\Services\PlasmaBot;
use App\Services\ZenonSdk;
use App\Services\ZnnCli;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use pnoeric\DiscourseAPI;

class ZenonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        // Zenon

        $this->app->singleton(ZenonSdk::class, function ($app, $params) {
            $node = $params['node'] ?? config('zenon.node_url');

            return (new ZenonSdk($node))->getZenon();
        });

        $this->app->singleton(ZnnCli::class, function ($app, $params) {
            return new ZnnCli($params['node_url'], $params['keystore'], $params['passphrase']);
        });

        $this->app->singleton(PlasmaBot::class, function ($app, $params) {
            return new PlasmaBot();
        });

        //
        // Integrations

        $this->app->singleton(CoinGecko::class, function ($app, $params) {
            return new CoinGecko();
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
    }
}
