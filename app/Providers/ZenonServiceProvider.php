<?php

namespace App\Providers;

use App\Services\Discord\GuzzleWebHook;
use App\Services\PlasmaBot;
use App\Services\Prices;
use App\Services\Twitter;
use App\Services\Zenon;
use App\Services\ZnnCli;
use DigitalSloth\ZnnPhp\Zenon as ZenonApi;
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

        $this->app->singleton('zenon.api', function ($app, $params) {
            $node = $params['node'] ?? config('zenon.node_url');

            return new ZenonApi($node, config('zenon.throw_api_errors'));
        });

        $this->app->singleton('zenon.services', function ($app, $params) {
            return new Zenon();
        });

        $this->app->singleton(ZnnCli::class, function ($app, $params) {
            return new ZnnCli($params['node_url'], $params['keystore'], $params['passphrase']);
        });

        $this->app->singleton(PlasmaBot::class, function ($app, $params) {
            return new PlasmaBot();
        });

        //
        // Integrations

        $this->app->singleton('coingeko.api', function ($app, $params) {
            return new Prices();
        });

        $this->app->singleton('twitter.api', function ($app, $params) {
            return new Twitter(
                $params['api_key'],
                $params['api_key_secret'],
                $params['access_token'],
                $params['access_token_secret'],
            );
        });

        $this->app->singleton('discord.api', function ($app, $params) {
            $httpClient = new Client();

            return new GuzzleWebHook($httpClient, $params['webhook']);
        });

        $this->app->singleton('discourse.api', function ($app, $params) {
            return new DiscourseAPI(
                config('services.discourse.host'),
                config('services.discourse.key')
            );
        });
    }
}
