<?php

return [
    'watch_addresses' => [
        'z1qr9vtwsfr2n0nsxl2nfh6l5esqjh2wfj85cfq9',
    ],
    'watch_methods' => [
        // Bridge
        'SetNetwork',
        'RemoveNetwork',
        'SetTokenPair',
        'SetNetworkMetadata',
        'RemoveTokenPair',
        'Halt',
        'Unhalt',
        'Emergency',
        'ChangeTssECDSAPubKey',
        'ChangeAdministrator',
        'SetAllowKeyGen',
        'SetRedeemDelay',
        'SetBridgeMetadata',
        'NominateGuardians',
        'SetOrchestratorInfo',

        // Liquidity
        'SetTokenTuple',
        'NominateGuardians',
        'ProposeAdministrator',
        'Emergency',
        'SetIsHalted',
        'SetAdditionalReward',
        'ChangeAdministrator',
    ],
    'discord' => [
        'enabled' => env('BRIDGE_BOT_ENABLE_DISCORD', false),
        'webhook' => env('BRIDGE_BOT_DISCORD_WEBHOOK'),
    ],
    'telegram' => [
        'enabled' => env('BRIDGE_BOT_ENABLE_TELEGRAM', false),
        'bot_token' => env('BRIDGE_BOT_TELEGRAM_BOT_TOKEN'),
        'chat' => env('BRIDGE_BOT_TELEGRAM_CHAT_ID'),
    ],
    'twitter' => [
        'enabled' => env('BRIDGE_BOT_ENABLE_TWITTER', false),
        'consumer_key' => env('BRIDGE_BOT_TWITTER_CONSUMER_KEY'),
        'consumer_secret' => env('BRIDGE_BOT_TWITTER_CONSUMER_SECRET'),
        'access_token' => env('BRIDGE_BOT_TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('BRIDGE_BOT_TWITTER_ACCESS_TOKEN_SECRET'),
    ],
];
