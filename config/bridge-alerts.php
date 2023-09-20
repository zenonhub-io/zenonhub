<?php

return [
    'watch_addresses' => [
        // Admin
        'z1qr9vtwsfr2n0nsxl2nfh6l5esqjh2wfj85cfq9',
        // Guardians
        'z1qphnq6jfaf82kmpyuuc88983ar66dmh7e59f67',
        'z1qppk2p26xwwzu5w4zyzwknrx28whvjgy9ukc6h',
        'z1qprccs7kjvx9q78m5v5ghwwfvxr6py8rtwcfrd',
        'z1qpxswrfnlll355wrx868xh58j7e2gu2n2u5czv',
        'z1qqcz0rmkz7f5442hjjr0thh2v6txu4875eyrkd',
        'z1qqeyp02thdets4k245fnnjpk764ls65gwsy0cg',
        'z1qr6k9c0z73c2zx22grhcw702slyz0gelt2uwvd',
        'z1qr7urykpjth3w9lcl66atgvu5fc0ywawzha220',
        'z1qrawthjzd95hcz73r3e5wd0xxzjmrt4vfqla0z',
        'z1qrgh8w9q3xj5a2t2atnt3reqhh0akm4qae8ezk',
        'z1qrztagl9rukq3ltdflnvg4zrvpfp84mydfejk9',
        'z1qzjnnpmnqp6uqz2m9uet8l5e42ewwaty2mqcpy',
        'z1qzup2zm6c9g68t085zjn5ycvdnr0u4pt0k4c80',
        'z1qzymmtmfr3gxz3fr80cq94rgaefzkvst4e90lz',
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
