<?php

namespace App\Providers;

use App\Events\Accelerator\ProjectCreated;
use App\Events\Accelerator\PhaseAdded;
use App\Events\Accelerator\PhaseUpdated;
use App\Events\Accelerator\Donate;
use App\Events\Accelerator\VoteByName;
use App\Events\Accelerator\VoteByProdAddress;
use App\Events\Common\DepositQsr;
use App\Events\Common\WithdrawQsr;
use App\Events\Common\CollectReward;
use App\Events\Pillars\Register as RegisterPillar;
use App\Events\Pillars\RegisterLegacy as RegisterLegacyPillar;
use App\Events\Pillars\Revoke as RevokePillar;
use App\Events\Pillars\UpdatePillar;
use App\Events\Pillars\Delegate;
use App\Events\Pillars\Undelegate;
use App\Events\Plasma\Fuse;
use App\Events\Plasma\CancelFuse;
use App\Events\Sentinel\Register as RegisterSentinel;
use App\Events\Sentinel\Revoke as RevokeSentinel;
use App\Events\Stake\Stake;
use App\Events\Stake\Cancel as CancelStake;
use App\Events\Token\IssueToken;
use App\Events\Token\Mint;
use App\Events\Token\Burn;
use App\Events\Token\UpdateToken;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        ],

        // Accelerator
        ProjectCreated::class => [],
        PhaseAdded::class => [],
        PhaseUpdated::class => [],
        Donate::class => [],
        VoteByName::class => [],
        VoteByProdAddress::class => [],

        // Common
        DepositQsr::class => [],
        WithdrawQsr::class => [],
        CollectReward::class => [],

        // Pillar
        RegisterPillar::class => [],
        RegisterLegacyPillar::class => [],
        RevokePillar::class => [],
        UpdatePillar::class => [],
        Delegate::class => [],
        Undelegate::class => [],

        // Plasma
        Fuse::class => [],
        CancelFuse::class => [],

        // Sentinels
        RegisterSentinel::class => [],
        RevokeSentinel::class => [],

        // Staking
        Stake::class => [],
        CancelStake::class => [],

        // Tokens
        IssueToken::class => [],
        Mint::class => [],
        Burn::class => [],
        UpdateToken::class => [],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
