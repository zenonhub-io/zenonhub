<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

test('debugging functions are not used')
    ->expect(['dd', 'dump', 'print_r', 'ray', 'var_dump'])
    ->not->toBeUsed();

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

test('tests have the "Test" suffix')
    ->expect('Tests\Feature')
    ->toHaveSuffix('Test');
