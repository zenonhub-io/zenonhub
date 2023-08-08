<?php

return [
    'znn_cutoff' => env('WHALE_BOT_ZNN_LIMIT', 1000) * 100000000,
    'qsr_cutoff' => env('WHALE_BOT_qsr_LIMIT', 10000) * 100000000,
    'discord' => [
        'enabled' => env('WHALE_BOT_ENABLE_DISCORD'),
        'webhook' => env('WHALE_BOT_DISCORD_WEBHOOK'),
    ],
    'telegram' => [
        'enabled' => env('WHALE_BOT_ENABLE_TELEGRAM'),
        'bot_token' => env('WHALE_BOT_TELEGRAM_BOT_TOKEN'),
        'chat' => env('WHALE_BOT_TELEGRAM_CHAT_ID'),
    ],
    'twitter' => [
        'enabled' => env('WHALE_BOT_ENABLE_TWITTER'),
        'consumer_key' => env('WHALE_BOT_TWITTER_CONSUMER_KEY'),
        'consumer_secret' => env('WHALE_BOT_TWITTER_CONSUMER_SECRET'),
        'access_token' => env('WHALE_BOT_TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('WHALE_BOT_TWITTER_ACCESS_TOKEN_SECRET'),
    ],
];
