<?php

return [
    'znn_cutoff' => env('WHALE_BOT_ZNN_LIMIT', 1000),
    'qsr_cutoff' => env('WHALE_BOT_qsr_LIMIT', 10000),
    'discord' => [
        'enabled' => env('WHALE_BOT_ENABLE_DISCORD'),
        'bot-token' => env('WHALE_BOT_DISCORD_BOT_TOKEN'),
        'channels' => [
            env('WHALE_BOT_DISCORD_CHANNEL_ID'),
        ],
    ],
    'telegram' => [
        'enabled' => env('WHALE_BOT_ENABLE_TELEGRAM'),
        'bot-token' => env('WHALE_BOT_TELEGRAM_BOT_TOKEN'),
        'channels' => [
            env('WHALE_BOT_DISCORD_CHANNEL_ID'),
        ],
    ],
    'twitter' => [
        'enabled' => env('WHALE_BOT_ENABLE_TWITTER'),
        'api_key' => env('WHALE_BOT_TWITTER_API_KEY'),
        'api_key_secret' => env('WHALE_BOT_TWITTER_API_SECRET'),
        'access_token' => env('WHALE_BOT_TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('WHALE_BOT_TWITTER_ACCESS_TOKEN_SECRET'),
    ],
];
