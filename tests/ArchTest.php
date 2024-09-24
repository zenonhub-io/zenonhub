<?php

declare(strict_types=1);

uses()->group('site', 'architecture');

arch()->preset()->php();

arch()->preset()->laravel();

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();

//arch('avoid open for extension')
//    ->expect('App')
//    ->classes()
//    ->toBeFinal();

//arch('ensure no extends')
//    ->expect('App')
//    ->classes()
//    ->not->toBeAbstract();

//arch('annotations')
//    ->expect('App')
//    ->toHavePropertiesDocumented()
//    ->toHaveMethodsDocumented();

test('debugging functions are not used')
    ->expect(['dd', 'dump', 'print_r', 'ray', 'var_dump'])
    ->not->toBeUsedIn('App');
