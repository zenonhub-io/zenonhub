<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title and meta tags (SEO)
    |--------------------------------------------------------------------------
    |
    | You may use the SEO facade to set your page's title, description, and keywords.
    | @see https://splade.dev/docs/title-meta
    |
    */

    'defaults' => [
        'title' => env('APP_NAME', 'Zenon Hub'),
        'description' => 'Explore the Zenon Network with Zenon Hub. Track transactions, addresses, and tokens, and uncover the dynamic activities on the cutting-edge Network of Momentum. Discover more now!',
        'keywords' => ['Zenon Network', 'NoM', 'explorer'],
    ],

    'title_separator' => ' | ',
    'title_suffix' => env('APP_NAME', 'Zenon Hub'),

    'auto_canonical_link' => true,

    'open_graph' => [
        'auto_fill' => true,
        'image' => '/img/meta-small.png',
        'site_name' => env('APP_NAME', 'Zenon Hub'),
        'title' => null,
        'type' => 'website', // 'WebPage'
        'url' => null,
    ],

    'twitter' => [
        'auto_fill' => true,
        'card' => 'summary',
        'description' => null,
        'image' => '/img/meta-small.png',
        'site' => '@zenonhub',
        'title' => null,
    ],
];