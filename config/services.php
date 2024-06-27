<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'zenon' => [
        'node_url' => env('ZNN_NODE_URL', '127.0.0.1:35997'),
        'throw_errors' => env('ZNN_NODE_THROW_ERRORS', true),
    ],

    'discourse' => [
        'key' => env('DISCOURSE_KEY'),
        'host' => env('DISCOURSE_HOST', 'forum.zenon.org'),
    ],

    'discord' => [
        'token' => env('DISCORD_BOT_TOKEN'),
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
    ],

    'twitter' => [
        'consumer_key' => env('TWITTER_BOT_CONSUMER_KEY'),
        'consumer_secret' => env('TWITTER_BOT_CONSUMER_SECRET'),
        'access_token' => env('TWITTER_BOT_ACCESS_TOKEN'),
        'access_secret' => env('TWITTER_BOT_ACCESS_SECRET'),
    ],

    'etherscan' => [
        'api_key' => env('ETHERSCAN_API_KEY'),
    ],

    'bitquery' => [
        'api_key' => env('BITQUERY_API_KEY'),
    ],
];
