<?php

namespace App\Providers;

use App\Services\Discord\GuzzleWebHook;
use App\Services\Prices;
use App\Services\Zenon;
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


        //
        // Integrations

        $this->app->singleton('coingeko.api', function ($app, $params) {
            return new Prices();
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
