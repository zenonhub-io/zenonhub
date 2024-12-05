<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-xs">{{ __('Pillar') }}</span>
                <div class="d-flex align-items-center mb-1">
                    @if ($pillar->socialProfile?->avatar)
                        <div class="w-24 w-md-32">
                            <img src="{{ $pillar->socialProfile?->avatar }}" class="rounded float-start title-avatar me-2" alt="{{ $pillar->name }} Logo "/>
                        </div>
                    @else
                        <x-svg file="zenon/pillar" class="me-4" style="height: 28px "/>
                    @endif
                    <x-includes.header-title :title="$pillar->name" />
                </div>
                <div class="d-flex align-items-center gap-3">
                    <x-social-profile.links :social-profile="$pillar->socialProfile" />
                </div>
            </div>
            <div class="d-flex align-items-end flex-column">
                <span class="badge badge-md text-bg-{{ $pillar->status_colour }} mb-2">{{ $pillar->status_text }}</span>
                <div class="dropdown">
                    <button class="btn btn-neutral btn-xs dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit-pillar-{{ $pillar->slug }}">
                                <i class="bi bi-pencil-fill me-2"></i> {{ __('Edit') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-share-fill me-2"></i> {{ __('Share') }}
                            </a>
                        </li>
                    </ul>
                </div>

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
                            :stat="$pillar->display_weight . ' ZNN'"
                            :info="__('Total ZNN delegated to the pillar')"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Rewards')"
                            :info="__('Momentum / Delegate rewards %')">
                            {{ $pillar->momentum_rewards }} / {{ $pillar->delegate_rewards }}
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
                            @if (! $pillar->revoked_at)
                                @if ($pillar->is_producing)
                                    <x-stats.indicator type="success" data-bs-toggle="tooltip" data-bs-title="Producing momentums" />
                                @else
                                    <x-stats.indicator type="danger" data-bs-toggle="tooltip" data-bs-title="Not producing momentums" />
                                @endif
                                {{ $pillar->produced_momentums }} / {{ $pillar->expected_momentums }}
                            @else
                                <x-stats.indicator type="danger" data-bs-toggle="tooltip" data-bs-title="Not producing momentums" />
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
                            :title="__('Orchestrator')"
                            :info="__('Indicates if the pillar runs an orchestrator and its status')">
                            @if($pillar->orchestrator)
                                <x-stats.indicator :type="$pillar->orchestrator->is_active ? 'success' : 'danger'" />
                                {{ ($pillar->orchestrator->is_active ? 'Online' : 'Offline') }}
                            @else
                                {{ __('None') }}
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
                            <x-stats.list-item :title="__('Voting')" :info="__('% of Accelerator-Z projects and phases voted on')">
                                @if (! is_null($pillar->az_engagement))
                                    <x-stats.indicator :type="$pillar->az_status_indicator" />
                                    {{ number_format($pillar->az_engagement) }}%
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Registration cost')" :stat="$pillar->display_qsr_burn .' QSR' "/>
                            <x-stats.list-item :title="__('Produced momentums')" :stat="number_format($pillar->momentums()->count())" />
                            <x-stats.list-item :title="__('Total delegators')" :stat="number_format($pillar->activeDelegators()->count())" :hr="false" />
                            <hr class="d-block d-md-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Spawned')">
                                <x-date-time.carbon :date="$pillar->created_at" />
                            </x-stats.list-item>
                            @if ($pillar->revoked_at)
                                <x-stats.list-item :title="__('Revoked')">
                                    <x-date-time.carbon :date="$pillar->revoked_at" />
                                </x-stats.list-item>
                            @else
                                <x-stats.list-item :title="__('Revocable in')" :stat="$pillar->display_revocable_in "/>
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

    @if ($tab === 'updates')
        <livewire:pillars.pillar-updates :pillar-id="$pillar->id" />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$pillar->raw_json" />
        </div>
    @endif

    <x-modals.modal class="modal-lg" id="edit-pillar-{{ $pillar->slug }}">
        <livewire:utilities.update-social-profile item-type="pillar" :item-id="$pillar->slug" :address="$pillar->owner->address" :title="$pillar->name" />
    </x-modals.modal>
</x-app-layout>

