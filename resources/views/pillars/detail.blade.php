<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <div class="d-flex">
                    @if ($pillar->socialProfile?->avatar)
                        <div class="flex-fill ms-auto d-flex align-items-start me-3">
                            <img src="{{ $pillar->socialProfile?->avatar }}" class="rounded img-fluid" alt="{{ $pillar->name }} Logo" style="min-width: 60px; max-width: 60px;"/>
                        </div>
                    @endif
                    <div class="flex-fill">
                        <div class="d-flex align-items-center">
                            @if (! $pillar->socialProfile?->avatar)
                                <div class="title-icon me-3">
                                    <x-svg file="zenon/pillar" />
                                </div>
                            @endif
                            <h5 class="text-muted">
                                {{ __('Pillar') }}
                                <x-copy :text="route('pillar.detail', ['slug' => $pillar->slug])" class="ms-2" :tooltip="__('Copy URL')" />
                                <span class="pointer ms-2" data-bs-toggle="tooltip" data-bs-title="{{ __('Edit Pillar') }}">
                                    <i class="bi bi-pencil-square"
                                       data-bs-toggle="modal"
                                       data-bs-target="#edit-pillar-{{ $pillar->slug }}"></i>
                                </span>
                            </h5>
                        </div>
                        <x-includes.header-title>
                            <h1 class="ls-tight text-wrap text-break">
                                {{ $pillar->name }}
                            </h1>
                        </x-includes.header-title>
                    </div>
                </div>
                @if ($pillar->socialProfile)
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <x-social-profile.links :social-profile="$pillar->socialProfile" />
                    </div>
                @endif
            </div>
            <div class="d-flex align-items-end flex-column">
                <span class="badge badge-md text-bg-{{ $pillar->status_colour }} mb-2" data-bs-toggle="tooltip" data-bs-title="{{ $pillar->status_tooltip }}">{{ $pillar->status_text }}</span>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Weight')"
                            :stat="__(':weight :znn', ['weight' => $pillar->display_weight, 'znn' => app('znnToken')->symbol])"
                            :info="__('Total :znn delegated to the pillar', ['znn' => app('znnToken')->symbol])"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('APR')"
                            :info="__('Current delegate APR %')">
                            {{ $pillar->delegate_apr }} %
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Momentums')"
                            :info="__('Produced / Expected momentums in the current epoch')">
                            <x-stats.indicator type="{{ $pillar->status_colour }}" data-bs-toggle="tooltip" data-bs-title="{{ $pillar->status_tooltip }}" />
                            @if (! $pillar->revoked_at)
                                {{ $pillar->produced_momentums }} / {{ $pillar->expected_momentums }}
                            @else
                                0 / 0
                            @endif
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Voting')"
                            :info="__('% of Accelerator-Z projects and phases voted on')">
                            @if (! is_null($pillar->az_engagement))
                                <x-stats.indicator :type="$pillar->az_status_indicator" />
                                {{ number_format($pillar->az_engagement) }}%
                            @else
                                -
                            @endif
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
        <x-cards.card class="mb-6">
            <x-cards.body>
                <div class="row">
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Rank')" :stat="'# ' . $pillar->display_rank" />
                            <x-stats.list-item :title="__('Orchestrator')" :info="__('Indicates if the pillar runs an orchestrator and its status')">
                                @if($pillar->orchestrator)
                                    <x-stats.indicator :type="$pillar->orchestrator->is_active ? 'success' : 'danger'" />
                                    {{ ($pillar->orchestrator->is_active ? 'Online' : 'Offline') }}
                                @else
                                    {{ __('None') }}
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Rewards')" stat="{{ $pillar->momentum_rewards }} / {{ $pillar->delegate_rewards }}" :info="__('Momentum / Delegate rewards %')" />
                            <x-stats.list-item :title="__('Registration cost')" :stat="$pillar->display_qsr_burn .' QSR'" />
                            <x-stats.list-item :title="__('Produced momentums')" :stat="number_format($pillar->momentums()->count())" />
                            <x-stats.list-item :title="__('Total delegators')" :stat="number_format($pillar->activeDelegators()->count())" :hr="false" />
                            <hr class="d-block d-md-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Legacy')" stat="{{ $pillar->is_legacy ? __('Yes') : __('No') }}" />
                            <x-stats.list-item :title="__('Created')">
                                @if ($pillar->is_legacy)
                                    {{ __('Genesis') }}
                                @else
                                    <x-date-time.carbon :date="$pillar->created_at" />
                                @endif
                            </x-stats.list-item>
                            @if ($pillar->revoked_at)
                                <x-stats.list-item :title="__('Revoked')">
                                    <x-date-time.carbon :date="$pillar->revoked_at" />
                                </x-stats.list-item>
                            @else
                                <x-stats.list-item :title="__('Revocable in')" :stat="$pillar->display_revocable_in" />
                            @endif
                            <x-stats.list-item :title="__('Owner')">
                                <x-address :account="$pillar->owner" :named="false" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Producer')">
                                <x-address :account="$pillar->producerAccount" :named="false" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Withdraw')" :hr="false">
                                <x-address :account="$pillar->withdrawAccount" :named="false" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                        </div>
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>
    </div>

    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Delegators') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'delegators']),
            __('Votes') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'votes']),
            __('Momentums') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'momentums']),
            __('Updates') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'updates']),
            __('JSON') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'json'])
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'delegators')
        <livewire:pillars.pillar-delegators :pillar-id="$pillar->id" />
    @endif

    @if ($tab === 'votes')
        <livewire:pillars.pillar-votes :pillar-id="$pillar->id" />
    @endif

    @if ($tab === 'momentums')
        <livewire:pillars.pillar-momentums :pillar-id="$pillar->id" />
    @endif

    @if ($tab === 'updates')
        <livewire:pillars.pillar-updates :pillar-id="$pillar->id" />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-cards.card>
                <x-cards.body>
                    <x-code-highlighters.json :code="$pillar->raw_json ?: ['error' => __('Data unavailable, please try again')]" />
                </x-cards.body>
            </x-cards.card>
        </div>
    @endif

    <x-modals.modal class="modal-lg" id="edit-pillar-{{ $pillar->slug }}">
        <livewire:utilities.update-social-profile item-type="pillar" :item-id="$pillar->slug" :address="$pillar->owner->address" :title="$pillar->name" />
    </x-modals.modal>
</x-app-layout>

