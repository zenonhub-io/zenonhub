<?php

declare(strict_types=1);

use App\Livewire\Utilities\CurrentHeight;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Livewire\Livewire;

uses()->group('livewire', 'utilities', 'current-height');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

test('it shows the current height', function () {
    Livewire::test(CurrentHeight::class)
        ->assertSee('7');
});
