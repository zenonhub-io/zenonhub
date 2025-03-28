<x-app-layout>

    <x-includes.header :title="__('Multichain Bridge')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('Overview') => route('stats.bridge'),
            __('Security') => route('stats.bridge', ['tab' => 'security']),
            __('Actions') => route('stats.bridge', ['tab' => 'actions']),
            __('Orchestrators') => route('stats.bridge', ['tab' => 'orchestrators']),
            __('Affiliates') => route('stats.bridge', ['tab' => 'affiliates'])
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'overview')
        <div class="container-fluid px-3 px-md-6">
            <x-alerts.alert :type="$status->isBridgeOnline() && $status->isOrchestratorsOnline() ? 'success' : 'warning'" class="mb-6 rounded-4 lead">
                @if ($status->isBridgeOnline() && $status->isOrchestratorsOnline())
                    <span class="d-block mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ __('The bridge and orchestrators are online') }}
                    </span>
                    <a href="{{ config('zenon-hub.bridge_affiliate_link') }}" target="_blank" class="btn btn-outline-success w-100">
                        {{ __('Bridge tokens now') }}
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endif

                @if (! $status->isBridgeOnline())
                    <i class="bi bi-exclamation-circle-fill me-2"></i> The bridge is currently halted
                    @if($status->getMomentumsToUnhalt())
                        for another {{ $status->getTimeTouUhalt()->diffForHumans(['parts' => 2], true) }} ({{ number_format($status->getMomentumsToUnhalt()) }} momentums)
                    @endif
                    please wait until it is back online before interacting with it.
                @endif

                @if (! $status->isOrchestratorsOnline())
                    @if($status->bridgeStatusDTO->orchestratorsRequiredOnlinePercentage)
                        <i class="bi bi-exclamation-circle-fill me-2"></i> Only {{ $status->bridgeStatusDTO->orchestratorsOnlinePercentage }}% of orchestrators are online, please wait until there are over {{ $status->bridgeStatusDTO->orchestratorsRequiredOnlinePercentage }}% before interacting with the bridge
                    @else
                        <i class="bi bi-exclamation-circle-fill me-2"></i> We are unable to determine the Orchestrators status, please try later or proceed with caution.
                    @endif
                @endif
            </x-alerts.alert>
            @foreach($status->getTimeChallenges() as $challenge)
                @if ($challenge->ends_in > 0)
                    <x-alerts.alert type="warning" class="mb-6 rounded-4">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> A time challenge is in place for {{ $challenge->contractMethod->contract->name }}.{{ $challenge->contractMethod->name }}. The challenge will expire in {{  number_format($challenge->ends_in) }} momentums, {{ $challenge->ends_at->diffForHumans() }}
                    </x-alerts.alert>
                @endif
            @endforeach
            <div class="row mb-6 gy-6">
                <div class="col-24 col-md-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Bridge')"
                                :info="__('Bridge is currently :status', ['status' => $status->isBridgeOnline() ? 'Online' : 'Offline'])">
                                <x-stats.indicator :type="$status->isBridgeOnline() ? 'success' : 'warning'" />
                                {{ $status->isBridgeOnline() ? __('Online') : __('Offline') }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-md-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Orchestrators')"
                                :info="$status->bridgeStatusDTO->orchestratorsOnlinePercentage .'% Online'">
                                <x-stats.indicator :type="$status->isOrchestratorsOnline() ? 'success' : 'warning'" />
                                {{ $status->isOrchestratorsOnline() ? __('Online') : __('Offline') }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-md-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Latest TX')"
                                :info="__('Time since last bridge interaction')">
                                @if ($status->getLatestTx())
                                    <x-date-time.carbon :date="$status->getLatestTx()->created_at" :human="true" :short="true" />
                                @else
                                    -
                                @endif
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
            <livewire:stats.bridge.inbound-outbound-volume />
        </div>
    @endif

    @if ($tab === 'security')
        <div class="container-fluid px-3 px-md-6">
            @if ($status->getTimeChallenges()->count())
                @foreach($status->getTimeChallenges() as $challenge)
                    @if ($challenge->ends_in > 0)
                        <x-alerts.alert type="warning" class="mb-6 rounded-4">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> A time challenge is in place for {{ $challenge->contractMethod->contract->name }}.{{ $challenge->contractMethod->name }}. The challenge will expire in {{  number_format($challenge->ends_in) }} momentums, {{ $challenge->ends_at->diffForHumans() }}
                        </x-alerts.alert>
                    @endif
                @endforeach
            @else
                <x-alerts.alert type="info" class="mb-6 rounded-4">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ __('No active time challenges') }}
                </x-alerts.alert>
            @endif

            <h4>Admin</h4>
            <div class="list-group mb-6 mt-2">
                <div class="list-group-item">
                    <div class="w-100">
                        <x-address :account="$status->getBridgeAdmin()->account" :named="false" :copyable="true" />
                        <div class="text-muted text-xs">
                            Last Active @if($status->getBridgeAdmin()->account->last_active_at)
                                <x-date-time.carbon :date="$status->getBridgeAdmin()->account->last_active_at" class="d-inline" />
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($status->getBridgeGuardians()->isNotEmpty())
                <h4>Guardians</h4>
                <div class="list-group mb-6 mt-2">
                    @foreach ($status->getBridgeGuardians() as $guardian)
                        <div class="list-group-item">
                            <div class="w-100">
                                <x-address :account="$guardian->account" :named="false" :copyable="true" />
                                <div class="text-muted text-xs">
                                    Last Active @if($guardian->account->last_active_at)
                                        <x-date-time.carbon :date="$guardian->account->last_active_at" class="d-inline" />
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row">
                <div class="col-24 col-sm-12">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Admin Delay')" :stat="number_format($status->getAdminDelay()) .' '. __('Momentums')" info="Number of momentums to delay appointing a new admin, allows for challenge by the guardians" />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-sm-12">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Soft Delay')" :stat="number_format($status->getSoftDelay()) .' '. __('Momentums')" info="Number of momentums to delay other time challenges, allows for challenge by the guardians" />
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>
    @endif

    @if ($tab === 'actions')
        <livewire:stats.bridge.admin-action-list lazy />
    @endif

    @if ($tab === 'orchestrators')

        <div class="container-fluid px-3 px-md-6">
            <div class="row mb-6 gy-6">
                <div class="col-12 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Total')">
                                {{ $status->bridgeStatusDTO->totalOrchestratorsCount }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Online %')">
                                {{ $status->bridgeStatusDTO->orchestratorsOnlinePercentage ?? 0 }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Total Online')">
                                {{ $status->bridgeStatusDTO->totalOrchestratorsOnlineCount }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>

        <livewire:stats.bridge.orchestrator-list lazy />
    @endif

    @if ($tab === 'affiliates')

        <div class="container-fluid px-3 px-md-6">
            <div class="row mb-6 gy-6">
                <div class="col-12 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__(':znn Paid', ['znn' => app('znnToken')->symbol])">
                                <span class="text-primary">
                                    {{ $stats['znn_paid'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__(':qsr Paid', ['qsr' => app('qsrToken')->symbol])">
                                <span class="text-secondary">
                                    {{ $stats['qsr_paid'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Total Affiliates')">
                                {{ $stats['total_affiliates'] }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>

        <livewire:stats.bridge.affiliate-list lazy />
    @endif

</x-app-layout>
