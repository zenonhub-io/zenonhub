<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

uses()->group('site', 'architecture');

test('models')
    ->expect('App\Models')
    ->classes()
    ->toExtend(Model::class);

test('concerns are traits')
    ->expect('App\Models\Concerns')
    ->toBeTraits();

test('DTOs have the "DTO" suffix')
    ->expect('App\Http\DataTransferObjects')
    ->toHaveSuffix('DTO');

test('tests have the "Test" suffix')
    ->expect('Tests\Feature')
    ->toHaveSuffix('Test');
