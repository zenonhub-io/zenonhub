<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Chain;
use App\Models\Nom\Token;
use App\Services\CoinGecko;
use App\Services\PlasmaBot;
use App\Services\ZenonCli;
use App\Services\ZenonSdk;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Support\ServiceProvider;

class NoMServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerSdkAndCli();
        $this->registerHelpers();

        $this->app->singleton(PlasmaBot::class, fn ($app, $params) => new PlasmaBot);

        $this->app->singleton(CoinGecko::class, fn ($app, $params) => new CoinGecko);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }

    private function registerSdkAndCli(): void
    {
        $this->app->singleton(Zenon::class, function ($app, $params) {
            $nodeUrl = $params['node'] ?? config('services.zenon.node_url');
            $throwErrors = $params['throwErrors'] ?? config('services.zenon.throw_errors');

            return new Zenon($nodeUrl, $throwErrors);
        });

        $this->app->singleton(ZenonSdk::class, fn ($app, $params) => new ZenonSdk($app->make(Zenon::class)));

        $this->app->singleton(ZenonCli::class, fn ($app, $params) => new ZenonCli(
            $params['node_url'],
            $params['keystore'],
            $params['passphrase']
        ));
    }

    private function registerHelpers(): void
    {
        $this->app->singleton('znnToken', fn ($app, $params) => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value));

        $this->app->singleton('qsrToken', fn ($app, $params) => Token::firstWhere('token_standard', NetworkTokensEnum::QSR->value));

        $this->app->singleton('currentChain', fn ($app, $params) => Chain::getCurrentChainId());
    }
}
