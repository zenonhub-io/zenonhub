<?php

namespace App\Providers;

use App\Events\Nom\Accelerator\AddPhase;
use App\Events\Nom\Accelerator\CreateProject;
use App\Events\Nom\Accelerator\Donate;
use App\Events\Nom\Accelerator\UpdatePhase;
use App\Events\Nom\Accelerator\VoteByName;
use App\Events\Nom\Accelerator\VoteByProdAddress;
use App\Events\Nom\Common\CollectReward;
use App\Events\Nom\Common\DepositQsr;
use App\Events\Nom\Common\WithdrawQsr;
use App\Events\Nom\Pillars\Delegate;
use App\Events\Nom\Pillars\Register as RegisterPillar;
use App\Events\Nom\Pillars\RegisterLegacy as RegisterLegacyPillar;
use App\Events\Nom\Pillars\Revoke as RevokePillar;
use App\Events\Nom\Pillars\Undelegate;
use App\Events\Nom\Pillars\UpdatePillar;
use App\Events\Nom\Plasma\CancelFuse;
use App\Events\Nom\Plasma\Fuse;
use App\Events\Nom\Sentinel\Register as RegisterSentinel;
use App\Events\Nom\Sentinel\Revoke as RevokeSentinel;
use App\Events\Nom\Stake\Cancel as CancelStake;
use App\Events\Nom\Stake\Stake;
use App\Events\Nom\Token\Burn;
use App\Events\Nom\Token\IssueToken;
use App\Events\Nom\Token\Mint;
use App\Events\Nom\Token\UpdateToken;
use App\Listeners\PlasmaBot\ConfirmNewFuse;
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
        CreateProject::class => [],
        AddPhase::class => [],
        UpdatePhase::class => [],
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
        Fuse::class => [
            ConfirmNewFuse::class,
        ],
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
