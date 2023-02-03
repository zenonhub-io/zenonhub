<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Version number
    |--------------------------------------------------------------------------
    |
    */

    'app_version' => env('APP_VERSION', '0.1'),

    /*
    |--------------------------------------------------------------------------
    | Google analytics ID
    |--------------------------------------------------------------------------
    |
    */

    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID', 'G-68QQY4TT6Z'),

    /*
    |--------------------------------------------------------------------------
    | Node URL
    |--------------------------------------------------------------------------
    |
    */

    'node_url' => env('ZNN_NODE_URL', '127.0.0.1:35997'),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Public Node URL
    |--------------------------------------------------------------------------
    |
    */

    'public_node_https' => env('PUBLIC_NODE_HTTPS', 'https://node.zenonhub.io:35997'),

    /*
    |--------------------------------------------------------------------------
    | WSS Public Node URL
    |--------------------------------------------------------------------------
    |
    */

    'public_node_wss' => env('PUBLIC_NODE_WSS', 'wss://node.zenonhub.io:35998'),

    /*
    |--------------------------------------------------------------------------
    | Throw API errors
    |--------------------------------------------------------------------------
    |
    */

    'throw_api_errors' => true,

    /*
    |--------------------------------------------------------------------------
    | Donations address
    |--------------------------------------------------------------------------
    |
    */

    'donation_address' => 'z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm',

    /*
    |--------------------------------------------------------------------------
    | Missing pillars
    | Any pillar spawned at genesis and revoked before the sync is run will not
    | get index so add its name and owner address here.
    |--------------------------------------------------------------------------
    |
    */

    'missing_pillars' => [
        'Barney' => 'z1qpdekjkqdlgup2wwacg68pcccaufdm3h86ulzt'
    ],

    /*
    |--------------------------------------------------------------------------
    | QSR increase per pillar
    |--------------------------------------------------------------------------
    |
    */

    'pillar_qsr_burn_increment' => 1000000000000,


    /*
    |--------------------------------------------------------------------------
    | Default date format
    |--------------------------------------------------------------------------
    |
    */

    //'date_format' => 'jS M, Y'
    'date_format' => 'jS M Y h:i:s A',
    'short_date_format' => 'j/m/Y h:i:s A',

    /*
    |--------------------------------------------------------------------------
    | Missing pillars
    | Any pillar spawned at genesis and revoked before the sync is run will not
    | get index so add its name and owner address here.
    |--------------------------------------------------------------------------
    |
    */

    'named_accounts' => [
        'z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm' => 'Zenon Hub',
        'z1qqs774auqksj94mtnes4qwvvzc8x8955en039j' => 'Bridge liquidity',
        'z1qzlytaqdahg5t02nz5096frflfv7dm3y7yxmg7' => 'BSC Bridge',
        'z1qqujg4pagysn42ve90g8zz2kcuxj03ks7druh2' => 'Liquidity Program Treasury',
        'z1qqw8f3qxx9zg92xgckqdpfws3dw07d26afsj74' => 'Liquidity Program Distributor',
        'z1qqgr9m627e9q6fyqvzd464wa4v2g5edhxrvqfl' => 'STEX Exchange',
    ],

    /*
    |--------------------------------------------------------------------------
    | Free download limit
    | The max number of rows for an unverified users export
    |--------------------------------------------------------------------------
    |
    */

    'free_export_limit' => 1000,

    /*
    |--------------------------------------------------------------------------
    | User download limit
    | The max number of rows for a verified users export
    |--------------------------------------------------------------------------
    |
    */

    'user_export_limit' => 50000,

    /*
    |--------------------------------------------------------------------------
    | Pillar missed momentum limit
    |--------------------------------------------------------------------------
    |
    */

    'pillar_missed_momentum_limit' => 15,


    'momentums_per_hour' => 360,
    'momentums_per_day' => 8640,
];
