<?php

declare(strict_types=1);

uses()->group('site', 'architecture');

test('controllers have the "Controller" suffix')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');
