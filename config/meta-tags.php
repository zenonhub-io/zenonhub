<?php

declare(strict_types=1);

return [

    'defaults' => [
        'title' => env('APP_NAME', 'Zenon Hub'),
        'description' => 'Explore the Zenon Network with Zenon Hub. Track transactions, addresses, tokens, and discover all the activity on the cutting-edge Network of Momentum',
        'keywords' => ['Zenon Network', 'NoM', 'explorer', 'ZNN', 'QSR'],
    ],

    'title_separator' => ' | ',
    'title_suffix' => env('APP_NAME', 'Zenon Hub'),

    'auto_canonical_link' => true,

    'open_graph' => [
        'auto_fill' => true,
        'image' => env('APP_URL') . '/img/meta-small.png',
        'site_name' => env('APP_NAME', 'Zenon Hub'),
        'title' => null,
        'type' => 'website', // 'WebPage'
        'url' => null,
    ],

    'twitter' => [
        'auto_fill' => true,
        'card' => 'summary',
        'description' => null,
        'image' => env('APP_URL') . '/img/meta-small.png',
        'site' => '@zenonhub',
        'title' => null,
    ],
];
