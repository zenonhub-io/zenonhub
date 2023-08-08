<?php

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
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
];
