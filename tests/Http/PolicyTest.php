<?php

declare(strict_types=1);

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;

uses()->group('site', 'routes');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

//it('privacy policy page returns a successful response', function () {
//    $response = $this->get(route('policy'));
//    $response->assertStatus(200);
//});
