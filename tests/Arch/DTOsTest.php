<?php

declare(strict_types=1);

uses()->group('site', 'architecture');

test('DTOs have the "DTO" suffix')
    ->expect('App\DataTransferObjects')
    ->toHaveSuffix('DTO');
