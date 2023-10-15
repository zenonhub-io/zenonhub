<?php

return [
    'twitter' => [
        'enabled' => env('TWITTER_BOT_ENABLED', false),
        'consumer_key' => env('TWITTER_BOT_CONSUMER_KEY'),
        'consumer_secret' => env('TWITTER_BOT_CONSUMER_SECRET'),
        'access_token' => env('TWITTER_BOT_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_BOT_ACCESS_TOKEN_SECRET'),
    ],
];
