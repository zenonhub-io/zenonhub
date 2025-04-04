<?php

declare(strict_types=1);

return [

    'nom_explorer_url' => env('NOM_EXPLORER_URL', 'https://zenonhub.test'),
    'hqz_explorer_url' => env('HQZ_EXPLORER_URL', 'https://zenonhub-hqz.test'),

    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID', 'G-68QQY4TT6Z'),

    'donation_address' => 'z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm',

    'public_node_https' => env('PUBLIC_NODE_HTTPS', 'https://node.zenonhub.io:35997'),
    'public_node_wss' => env('PUBLIC_NODE_WSS', 'wss://node.zenonhub.io:35998'),

    'avatar_url' => 'https://api.dicebear.com/9.x/identicon/svg',
    'bridge_affiliate_link' => 'https://bridge.mainnet.zenon.community/?referral=2f5b37010a3a2224607170251d36010b3179216771262f0f2a04156e2226263a27301a70090f3b21',

    'colours' => [
        'zenon-green' => '#00D557',
        'zenon-blue' => '#0061EB',
        'zenon-pink' => '#F91690',
        'zenon-black' => '#151515',
        'success' => '#00CC88',
        'info' => '#0099FF',
        'warning' => '#FF8C00',
        'danger' => '#FF3366',
        'bg-dark' => '#262626',
    ],

    'socials' => [
        'x' => 'https://x.com/zenonhub',
        'telegram' => 'https://t.me/digitalSloth',
        'github' => 'https://github.com/zenonhub-io',
        'email' => 'digitals1oth@proton.me',
    ],
];
