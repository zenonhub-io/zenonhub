<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

Breadcrumbs::for('info', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Info', route('info'));
});

Breadcrumbs::for('donate', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Donate', route('donate'));
});

Breadcrumbs::for('policy', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Privacy Policy', route('policy'));
});

//
// Account
Breadcrumbs::for('account', function (BreadcrumbTrail $trail) {
    // $trail->parent('home');
    $trail->push('Account', route('account.overview'));
});

Breadcrumbs::for('account.details', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Details', route('profile', ['tab' => 'details']));
});

Breadcrumbs::for('account.security', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Security', route('profile', ['tab' => 'security']));
});

Breadcrumbs::for('account.notifications', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Notifications', route('profile', ['tab' => 'notifications']));
});

Breadcrumbs::for('account.favorites', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Favorites', route('profile', ['tab' => 'favorites']));
});

Breadcrumbs::for('account.addresses', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Addresses', route('profile', ['tab' => 'addresses']));
});

Breadcrumbs::for('account.api-keys', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('API Keys', route('profile', ['tab' => 'api-keys']));
});

//
// Pillars
Breadcrumbs::for('pillar.list', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Pillars', route('pillar.list'));
});

Breadcrumbs::for('pillar.detail', function (BreadcrumbTrail $trail, App\Models\Nom\Pillar $pillar) {
    $trail->parent('pillar.list');
    $trail->push('Detail', route('pillar.detail', ['slug' => $pillar->slug]));
});

//
// Accelerator Z
Breadcrumbs::for('accelerator-z.list', function (BreadcrumbTrail $trail) {
    // $trail->parent('home');
    $trail->push('Accelerator-Z', route('accelerator-z.list'));
});

Breadcrumbs::for('accelerator-z.project.detail', function (BreadcrumbTrail $trail, App\Models\Nom\AcceleratorProject $project) {
    $trail->parent('accelerator-z.list');
    $trail->push('Project', route('accelerator-z.project.detail', ['hash' => $project->hash]));
});

Breadcrumbs::for('accelerator-z.phase.detail', function (BreadcrumbTrail $trail, App\Models\Nom\AcceleratorPhase $phase) {
    $trail->parent('accelerator-z.project.detail', $phase->project);
    $trail->push('Phase', route('accelerator-z.phase.detail', ['hash' => $phase->hash]));
});

//
// Explorer
Breadcrumbs::for('explorer', function (BreadcrumbTrail $trail) {
    // $trail->parent('home');
    $trail->push('Explorer', route('explorer.overview'));
});

Breadcrumbs::for('explorer.momentums', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Momentums', route('explorer.momentum.list'));
});

Breadcrumbs::for('explorer.momentum', function (BreadcrumbTrail $trail, App\Models\Nom\Momentum $momentum) {
    $trail->parent('explorer.momentums');
    $trail->push('Details', route('explorer.momentum.detail', ['hash' => $momentum->hash]));
});

Breadcrumbs::for('explorer.blocks', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Blocks', route('explorer.block.list'));
});

Breadcrumbs::for('explorer.block', function (BreadcrumbTrail $trail, App\Models\Nom\AccountBlock $accountBlock) {
    $trail->parent('explorer.blocks');
    $trail->push('Details', route('explorer.block.detail', ['hash' => $accountBlock->hash]));
});

Breadcrumbs::for('explorer.accounts', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Accounts', route('explorer.account.list'));
});

Breadcrumbs::for('explorer.account', function (BreadcrumbTrail $trail, App\Models\Nom\Account $account) {
    $trail->parent('explorer.accounts');
    $trail->push('Details', route('explorer.account.detail', ['address' => $account->address]));
});

Breadcrumbs::for('explorer.tokens', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Tokens', route('explorer.token.list'));
});

Breadcrumbs::for('explorer.token', function (BreadcrumbTrail $trail, App\Models\Nom\Token $token) {
    $trail->parent('explorer.tokens');
    $trail->push('Details', route('explorer.token.detail', ['zts' => $token->token_standard]));
});

Breadcrumbs::for('explorer.bridge', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Bridge', route('explorer.bridge.list'));
});

Breadcrumbs::for('explorer.staking', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Staking', route('explorer.stake.list'));
});

Breadcrumbs::for('explorer.fusions', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Fusions', route('explorer.plasma.list'));
});

//
// Stats
Breadcrumbs::for('stats.nodes', function (BreadcrumbTrail $trail) {
    $trail->parent('stats');
    $trail->push('Nodes', route('stats.public-nodes'));
});

Breadcrumbs::for('stats.accelerator', function (BreadcrumbTrail $trail) {
    $trail->parent('stats');
    $trail->push('Accelerator', route('stats.accelerator-z'));
});

Breadcrumbs::for('stats.bridge', function (BreadcrumbTrail $trail) {
    $trail->parent('stats');
    $trail->push('Bridge', route('stats.bridge'));
});

//
// Tools
Breadcrumbs::for('tools.plasma-bot', function (BreadcrumbTrail $trail) {
    $trail->parent('tools');
    // $trail->parent('home');
    $trail->push('Plasma Bot', route('tools.plasma-bot'));
});

Breadcrumbs::for('tools.api-playground', function (BreadcrumbTrail $trail) {
    $trail->parent('tools');
    // $trail->parent('home');
    $trail->push('API Playground', route('tools.api-playground'));
});

Breadcrumbs::for('tools.verify-signature', function (BreadcrumbTrail $trail) {
    $trail->parent('tools');
    // $trail->parent('home');
    $trail->push('Verify signature', route('tools.verify-signature'));
});

//
// Services
Breadcrumbs::for('services.public-nodes', function (BreadcrumbTrail $trail) {
    $trail->parent('services');
    // $trail->parent('home');
    $trail->push('Public Nodes', route('services.public-nodes'));
});
