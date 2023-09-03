<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable indexer
    |--------------------------------------------------------------------------
    |
    */

    'enable_indexer' => env('EXPLORER_ENABLE_INDEXER', true),

    /*
    |--------------------------------------------------------------------------
    | Fire whale and bridge alerts when indexing
    |--------------------------------------------------------------------------
    |
    */

    'enable_alerts' => env('EXPLORER_ENABLE_ALERTS', false),

    /*
    |--------------------------------------------------------------------------
    | Update account balances when indexing
    |--------------------------------------------------------------------------
    |
    */

    'enable_balances' => env('EXPLORER_ENABLE_BALANCES', true),

    /*
    |--------------------------------------------------------------------------
    | Named accounts, adds labels to accounts in the explorer
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
        'z1qzzavvq2zywv77ts2e9yntc3y24qetjh0x0aj4' => 'Plasma Bot',
        'z1qr9vtwsfr2n0nsxl2nfh6l5esqjh2wfj85cfq9' => 'Bridge Admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Flagged accounts
    |--------------------------------------------------------------------------
    |
    */

    'flagged_accounts' => [
        'z1qrq9g7fauvyts7k97cm6xgj0f872j9h7hl7cwu' => 'Flagged as possible scammer address: <a class="alert-link" target="_blank" href="https://t.me/zenonnetwork/308788">https://t.me/zenonnetwork/308788</a>',
        'z1qzwcjz7qu40d77k0u6hc6lql4rglruvhpvzseh' => 'Flagged as possible scammer address: <a class="alert-link" target="_blank" href="https://t.me/zenonnetwork/308788">https://t.me/zenonnetwork/308788</a>',
        'z1qregw8lte8mud2wpugyaupa7wn5swaqvvxwqr4' => 'Flagged as possible scammer address: <a class="alert-link" target="_blank" href="https://t.me/zenonnetwork/308788">https://t.me/zenonnetwork/308788</a>',
    ],
];
