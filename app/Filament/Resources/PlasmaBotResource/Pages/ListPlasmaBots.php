<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlasmaBotResource\Pages;

use App\Filament\Resources\PlasmaBotResource;
use Filament\Resources\Pages\ListRecords;

class ListPlasmaBots extends ListRecords
{
    protected static string $resource = PlasmaBotResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
