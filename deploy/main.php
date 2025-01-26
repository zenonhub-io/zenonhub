<?php

declare(strict_types=1);

namespace Deployer;

require_once __DIR__ . '/../vendor/deployer/deployer/recipe/laravel.php';
require __DIR__ . '/../vendor/autoload.php';

\Dotenv\Dotenv::createMutable(__DIR__)->load();

set('repository', fn () => getenv('DEPLOYER_REPO'));
set('remote_user', fn () => getenv('DEPLOYER_USER'));
set('port', fn () => getenv('DEPLOYER_PORT'));

host('live')
    ->set('hostname', fn () => getenv('DEPLOYER_LIVE_HOST'))
    ->set('deploy_path', fn () => getenv('DEPLOYER_LIVE_PATH'))
    ->set('branch', 'main')
    ->setLabels([
        'stage' => 'live',
    ]);

host('develop')
    ->set('hostname', fn () => getenv('DEPLOYER_STAGING_HOST'))
    ->set('deploy_path', fn () => getenv('DEPLOYER_STAGING_PATH'))
    ->set('branch', 'develop')
    ->setLabels([
        'stage' => 'develop',
    ]);
