<?php

return [
    'node_url' => env('PUBLIC_NODE_WSS', 'wss://node.zenonhub.io:35998'),
    'keystore' => env('PLASMA_BOT_KEYSTORE'),
    'passphrase' => env('PLASMA_BOT_PASSPHRASE'),
    'mnemonic' => env('PLASMA_BOT_MNEMONIC'),
    'address' => env('PLASMA_BOT_ADDRESS'),
];
