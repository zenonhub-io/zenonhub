<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\BridgeStatus;
use App\Services\Discord\DiscordWebHook;
use App\Services\Seo\MetaTags;
use App\Services\TokenPrice;
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
        $this->registerServices();

        //
        // Old

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

    private function registerServices(): void
    {
        $this->app->singleton(BridgeStatus::class, fn ($app, $params) => new BridgeStatus);

        $this->app->singleton(TokenPrice::class, fn ($app, $params) => new TokenPrice);

        $this->app->singleton(MetaTags::class, fn () => new MetaTags);
    }

    private function configureActions(): void
    {
        Actions::registerCommands([
            'app/Actions',
        ]);
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict();
    }
}
