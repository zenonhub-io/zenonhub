<?php

declare(strict_types=1);

uses()->group('site', 'architecture');

test('debugging functions are not used')
    ->expect(['dd', 'dump', 'print_r', 'ray', 'var_dump'])
    ->not->toBeUsedIn('App');
