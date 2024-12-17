<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

uses()->group('site', 'architecture');

test('models')
    ->expect('App\Models')
    ->classes()
    ->toExtend(Model::class);

test('tests have the "Test" suffix')
    ->expect('Tests\Feature')
    ->toHaveSuffix('Test');
