<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

test('debugging functions are not used')
    ->expect(['dd', 'dump', 'print_r', 'ray', 'var_dump'])
    ->not->toBeUsedIn('App');

test('models')
    ->expect('App\Models')
    ->classes()
    ->toExtend(Model::class);

test('concerns are traits')
    ->expect('App\Models\Concerns')
    ->toBeTraits();

test('controllers have the "Controller" suffix')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

test('DTOs have the "DTO" suffix')
    ->expect('App\Http\DataTransferObjects')
    ->toHaveSuffix('DTO');

test('tests have the "Test" suffix')
    ->expect('Tests\Feature')
    ->toHaveSuffix('Test');