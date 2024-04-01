<?php

return [
    'node_url' => env('PLASMA_BOT_NODE', 'wss://node.zenonhub.io:35998'),
    'keystore' => env('PLASMA_BOT_KEYSTORE'),
    'passphrase' => env('PLASMA_BOT_PASSPHRASE'),
    'address' => env('PLASMA_BOT_ADDRESS'),
    'access_keys' => explode(',', env('PLASMA_BOT_ACCESS_KEYS', '')),
];
