<?php

// routes/breadcrumbs.php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

Breadcrumbs::for('donate', function (BreadcrumbTrail $trail) {
    $trail->push('Donate', route('donate'));
});

Breadcrumbs::for('privacy', function (BreadcrumbTrail $trail) {
    $trail->push('Privacy', route('privacy'));
});

//
// Auth

//Breadcrumbs::for('auth.login', function (BreadcrumbTrail $trail) {
//    $trail->parent('home');
//    $trail->push('Login', route('login'));
//});

//
// Pillars
Breadcrumbs::for('pillars', function (BreadcrumbTrail $trail) {
    //$trail->parent('home');
    $trail->push('Pillars', route('pillars.overview'));
});

Breadcrumbs::for('pillar', function (BreadcrumbTrail $trail, App\Models\Nom\Pillar $pillar) {
    $trail->parent('pillars');
    $trail->push('Details', route('pillars.detail', ['slug' => $pillar->slug]));
});

//
// Accelerator Z
Breadcrumbs::for('az', function (BreadcrumbTrail $trail) {
    //$trail->parent('home');
    $trail->push('Accelerator-Z', route('az.overview'));
});

Breadcrumbs::for('project', function (BreadcrumbTrail $trail, App\Models\Nom\AcceleratorProject $project) {
    $trail->parent('az');
    $trail->push('Project', route('az.project', ['hash' => $project->hash]));
});

Breadcrumbs::for('phase', function (BreadcrumbTrail $trail, App\Models\Nom\AcceleratorPhase $phase) {
    $trail->parent('project', $phase->project);
    $trail->push('Phase', route('az.phase', ['hash' => $phase->hash]));
});

//
// Explorer
Breadcrumbs::for('explorer', function (BreadcrumbTrail $trail) {
    //$trail->parent('home');
    $trail->push('Explorer', route('explorer.overview'));
});

Breadcrumbs::for('explorer.momentums', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Momentums', route('explorer.momentums'));
});

Breadcrumbs::for('explorer.momentum', function (BreadcrumbTrail $trail, App\Models\Nom\Momentum $momentum) {
    $trail->parent('explorer.momentums');
    $trail->push('Details', route('explorer.momentum', ['hash' => $momentum->hash]));
});

Breadcrumbs::for('explorer.transactions', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Transactions', route('explorer.transactions'));
});

Breadcrumbs::for('explorer.transaction', function (BreadcrumbTrail $trail, App\Models\Nom\AccountBlock $accountBlock) {
    $trail->parent('explorer.transactions');
    $trail->push('Details', route('explorer.transaction', ['hash' => $accountBlock->hash]));
});

Breadcrumbs::for('explorer.accounts', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Accounts', route('explorer.accounts'));
});

Breadcrumbs::for('explorer.account', function (BreadcrumbTrail $trail, App\Models\Nom\Account $account) {
    $trail->parent('explorer.accounts');
    $trail->push('Details', route('explorer.account', ['address' => $account->address]));
});

Breadcrumbs::for('explorer.tokens', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Tokens', route('explorer.tokens'));
});

Breadcrumbs::for('explorer.token', function (BreadcrumbTrail $trail, App\Models\Nom\Token $token) {
    $trail->parent('explorer.tokens');
    $trail->push('Details', route('explorer.token', ['zts' => $token->token_standard]));
});

Breadcrumbs::for('explorer.staking', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Staking', route('explorer.staking'));
});

Breadcrumbs::for('explorer.fusions', function (BreadcrumbTrail $trail) {
    $trail->parent('explorer');
    $trail->push('Fusions', route('explorer.fusions'));
});

//
// Stats
Breadcrumbs::for('stats', function (BreadcrumbTrail $trail) {
    $trail->push('Stats', route('stats.overview'));
});

Breadcrumbs::for('stats.nodes', function (BreadcrumbTrail $trail) {
    $trail->parent('stats');
    $trail->push('Nodes', route('stats.nodes'));
});

Breadcrumbs::for('stats.accelerator', function (BreadcrumbTrail $trail) {
    $trail->parent('stats');
    $trail->push('Accelerator', route('stats.accelerator'));
});

//
// Tools
Breadcrumbs::for('tools', function (BreadcrumbTrail $trail) {
    //$trail->parent('home');
    $trail->push('Tools', route('tools.overview'));
});

Breadcrumbs::for('tools.api-playground', function (BreadcrumbTrail $trail) {
    $trail->parent('tools');
    $trail->push('API', route('tools.api-playground'));
});

Breadcrumbs::for('tools.broadcast-message', function (BreadcrumbTrail $trail) {
    $trail->parent('tools');
    $trail->push('Broadcast message', route('tools.broadcast-message'));
});

Breadcrumbs::for('tools.verify-signature', function (BreadcrumbTrail $trail) {
    $trail->parent('tools');
    $trail->push('Verify signature', route('tools.verify-signature'));
});

//
// Account
Breadcrumbs::for('account', function (BreadcrumbTrail $trail) {
    //$trail->parent('home');
    $trail->push('Account', route('account.overview'));
});

Breadcrumbs::for('account.details', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Details', route('account.details'));
});

Breadcrumbs::for('account.addresses', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Addresses', route('account.addresses'));
});

Breadcrumbs::for('account.security', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Security', route('account.security'));
});

Breadcrumbs::for('account.notifications', function (BreadcrumbTrail $trail) {
    $trail->parent('account');
    $trail->push('Notifications', route('account.notifications'));
});
