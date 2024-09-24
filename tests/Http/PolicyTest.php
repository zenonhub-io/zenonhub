<?php

declare(strict_types=1);

use Database\Seeders\NomSeeder;

uses()->group('site', 'routes');

beforeEach(function () {
    $this->seed(NomSeeder::class);
});

it('privacy policy page returns a successful response', function () {
    $response = $this->get(route('policy'));
    $response->assertStatus(200);
});
