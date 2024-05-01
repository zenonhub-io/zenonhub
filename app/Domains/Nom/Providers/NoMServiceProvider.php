<?php

declare(strict_types=1);

namespace App\Domains\Nom\Providers;

use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Chain;
use App\Domains\Nom\Models\Token;
use App\Domains\Nom\Services\PlasmaBot;
use App\Domains\Nom\Services\ZenonCli;
use App\Domains\Nom\Services\ZenonSdk;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class NoMServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
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

        $this->app->singleton(PlasmaBot::class, fn ($app, $params) => new PlasmaBot);

        $this->app->singleton('znnToken', fn ($app, $params) => Token::findBy('token_standard', NetworkTokensEnum::ZNN->value));

        $this->app->singleton('qsrToken', fn ($app, $params) => Token::findBy('token_standard', NetworkTokensEnum::QSR->value));

        $this->app->singleton('currentChain', fn ($app, $params) => Chain::getCurrentChainId());
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
