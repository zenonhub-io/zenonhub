<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Domains\Indexer\Providers\IndexerServiceProvider::class,
    App\Domains\Nom\Providers\NoMServiceProvider::class,
];
