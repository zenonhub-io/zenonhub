<?php

declare(strict_types=1);

if (! defined('NOM_DECIMALS')) {
    define('NOM_DECIMALS', 100000000);
}

if (! defined('NOM_SECONDS_IN_DAY')) {
    define('NOM_SECONDS_IN_DAY', 24 * 60 * 60);
}

if (! defined('NOM_MOMENTUMS_PER_HOUR')) {
    define('NOM_MOMENTUMS_PER_HOUR', 3600 / 10);
}

if (! defined('NOM_MOMENTUMS_PER_EPOCH')) {
    define('NOM_MOMENTUMS_PER_EPOCH', NOM_MOMENTUMS_PER_HOUR * 24);
}

return [

    // Common
    'decimals' => NOM_DECIMALS,
    'secondsInDay' => NOM_SECONDS_IN_DAY,
    'momentumsPerHour' => NOM_MOMENTUMS_PER_HOUR,
    'momentumsPerEpoch' => NOM_MOMENTUMS_PER_EPOCH,
    'rewardTimeLimit' => 3600,

    // ZTS
    'znn_zts' => env('ZTS_ZNN', 'zts1znnxxxxxxxxxxxxx9z4ulx'),
    'qsr_zts' => env('ZTS_QSR', 'zts1qsrxxxxxxxxxxxxxmrhjll'),

    // Accelerator
    'accelerator' => [
        'projectNameLengthMax' => 30,
        'projectDescriptionLengthMax' => 240,
        'projectZnnMaximumFunds' => (string) (5000 * NOM_DECIMALS),
        'projectQsrMaximumFunds' => (string) (50000 * NOM_DECIMALS),
        'projectCreationAmount' => (string) (1 * NOM_DECIMALS),
        'phaseTimeUnit' => 24 * 60 * 60,
        'acceleratorDuration' => 20 * 12 * 30 * NOM_SECONDS_IN_DAY,
        'voteAcceptanceThreshold' => 33,
        'acceleratorProjectVotingPeriod' => 14 * NOM_SECONDS_IN_DAY,
    ],

    // Pillar
    'pillar' => [
        'znnStakeAmount' => (string) (15000 * NOM_DECIMALS),
        'qsrStakeBaseAmount' => (string) (150000 * NOM_DECIMALS),
        'qsrStakeIncreaseAmount' => (string) (10000 * NOM_DECIMALS),
        'epochLockTime' => 83 * NOM_SECONDS_IN_DAY,
        'epochRevokeTime' => 7 * NOM_SECONDS_IN_DAY,
        'nameLengthMax' => 40,
    ],

    // Sentinel
    'sentinel' => [
        'znnRegisterAmount' => (string) (5000 * NOM_DECIMALS),
        'qsrDepositAmount' => (string) (50000 * NOM_DECIMALS),
        'lockTimeWindow' => 27 * NOM_SECONDS_IN_DAY,
        'revokeTimeWindow' => 3 * NOM_SECONDS_IN_DAY,
    ],

    // Stake
    'stake' => [
        'timeUnitSec' => 30 * NOM_SECONDS_IN_DAY,
        'timeMinSec' => (30 * NOM_SECONDS_IN_DAY) * 1,
        'timeMaxSec' => (30 * NOM_SECONDS_IN_DAY) * 12,
        'minAmount' => (string) (1 * NOM_DECIMALS),
    ],

    // Plasma
    'plasma' => [
        'minAmount' => 10 * NOM_DECIMALS,
        'expiration' => NOM_MOMENTUMS_PER_HOUR * 10,

        'accountBlockBasePlasma' => 21000,
        'ABByteDataPlasma' => 68,
        'costPerFusionUnit' => '100000000',
    ],

    // Token
    'token' => [
        'issueAmount' => (string) (1 * NOM_DECIMALS),
        'nameLengthMax' => 40,
        'symbolLengthMax' => 10,
        'domainLengthMax' => 128,
        'maxSupplyBig' => '57896044618658097711785492504343953926634992332820282019728792003956564819967',
        'maxDecimals' => 128,
    ],

    // Bridge
    'bridge' => [
        'initialBridgeAdmin' => 'z1qr9vtwsfr2n0nsxl2nfh6l5esqjh2wfj85cfq9',
        'maximumFee' => 10000,
        'minUnhaltDurationInMomentums' => 6 * NOM_MOMENTUMS_PER_HOUR,
        'minAdministratorDelay' => 2 * NOM_MOMENTUMS_PER_EPOCH,
        'minSoftDelay' => NOM_MOMENTUMS_PER_EPOCH,
        'minGuardians' => 5,
    ],

    // Rewards
    'rewards' => [
        'tickDurationInEpochs' => 30,

        'networkZnnRewardConfig' => [
            10 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            6 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            5 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            7 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            5 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            4 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            7 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            4 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            3 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            7 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
            3 * NOM_MOMENTUMS_PER_EPOCH / 6 * NOM_DECIMALS,
        ],

        'networkQsrRewardConfig' => [
            20000 * NOM_DECIMALS,
            20000 * NOM_DECIMALS,
            20000 * NOM_DECIMALS,
            20000 * NOM_DECIMALS,
            15000 * NOM_DECIMALS,
            15000 * NOM_DECIMALS,
            15000 * NOM_DECIMALS,
            5000 * NOM_DECIMALS,
        ],

        'delegationZnnRewardPercentage' => 24,
        'momentumProducingZnnRewardPercentage' => 50,
        'sentinelZnnRewardPercentage' => 13,
        'liquidityZnnRewardPercentage' => 13,
        'liquidityZnnTotalPercentages' => 10000,

        'StakingQsrRewardPercentage' => 50,
        'SentinelQsrRewardPercentage' => 25,
        'LiquidityQsrRewardPercentage' => 25,
        'LiquidityQsrTotalPercentages' => 10000,
        'LiquidityStakeWeights' => [
            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
        ],
    ],
];
