<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-sm">{{ __('Account') }} {{ $account->has_custom_label ? ' | '.$account->custom_label : '' }}</span>
                <div class="d-flex align-items-center mb-1">
                    @if ($account->socialProfile?->avatar)
                        <div class="me-3" style="height: 28px">
                            <img src="{{ $account->socialProfile?->avatar }}" class="rounded title-avatar" alt="{{ $account->address }} Logo"/>
                        </div>
                    @else
                        <div style="height: 28px; width: 28px" class="me-4 rounded overflow-hidden">
                            {!! $account->avatar_svg !!}
                        </div>
                    @endif
                    <x-includes.header-title class="mb-1">
                        <h1 class="ls-tight text-wrap text-break">
                            {{ $account->address }}
                            <span class="text-lg">
                            <x-copy :text="$account->address" class="mx-2 text-lg" />
                            <span class="pointer text-lg ms-2" data-bs-toggle="tooltip"
                                  data-bs-title="{{ __('Edit address') }}">
                                <i class="bi bi-pencil-square"
                                   data-bs-toggle="modal"
                                   data-bs-target="#edit-account-{{ $account->address }}"></i>
                            </span>
                        </span>
                        </h1>
                    </x-includes.header-title>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <x-social-profile.links :social-profile="$account->socialProfile" />
                </div>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-md-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat :title="__('ZNN')">
{{--                            <span class="text-primary" data-bs-toggle="tooltip" data-bs-title="{{ $account->display_znn_balance }}">--}}
{{--                                {{ Number::abbreviate(app('znnToken')->getDisplayAmount($account->znn_balance), 2) }}--}}
{{--                            </span>--}}
                            <span class="text-primary">
                                {{ $account->display_znn_balance }}
                            </span>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-md-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat :title="__('QSR')">
                            <span class="text-secondary">
                                {{ $account->display_qsr_balance }}
                            </span>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-24 col-md-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat :title="__('USD')">
                            {{ $account->display_usd_balance }}
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
                            <x-stats.list-item :title="__('Address')">
                                <x-hash :hash="$account->address" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Plasma')">
                                <span data-bs-toggle="tooltip" data-bs-title="{{ $account->display_plasma_amount }} QSR">
                                    @if ($account->plasma_level === 'High')
                                        <x-stats.indicator type="success" /> {{ $account->plasma_level }}
                                    @elseif ($account->plasma_level === 'Medium')
                                        <x-stats.indicator type="warning" /> {{ $account->plasma_level }}
                                    @elseif ($account->plasma_level === 'Low')
                                        <x-stats.indicator type="danger" /> {{ $account->plasma_level }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Fused QSR')" :stat="$account->display_qsr_fused" />
                            <x-stats.list-item :title="__('Staked ZNN')" :stat="$account->display_znn_staked" />
                            <x-stats.list-item :title="__('ZNN Rewards')" :stat="$account->display_znn_rewards" />
                            <x-stats.list-item :title="__('QSR Rewards')" :stat="$account->display_qsr_rewards" />
                            <x-stats.list-item :title="__('Delegating to')" :hr="false">
                                @if($account->active_delegation)
                                    <x-link :href="route('pillar.detail', ['slug' => $account->active_delegation->slug])">
                                        {{ $account->active_delegation->name }}
                                    </x-link>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <hr class="d-block d-lg-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('First Active')">
                                @if($account->first_active_at)
                                    <x-date-time.carbon :date="$account->first_active_at" />
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Last Active')">
                                @if($account->last_active_at)
                                    <x-date-time.carbon :date="$account->last_active_at" />
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Chain Height')">
                                {{ $account->display_height }}
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Tokens')">
                                {{ $account->tokens_count }}
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Funded by')">
                                @if($account->fundingBlock)
                                    <x-address :account="$account->fundingBlock->account" :always-short="true" :copyable="true" />
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Funding Tx')">
                                @if($account->fundingBlock)
                                    <x-hash :hash="$account->fundingBlock->hash" :always-short="true" :copyable="true" :link="route('explorer.transaction.detail', ['hash' => $account->fundingBlock->hash])" />
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Public Key')" :hr="false">
                                @if($account->public_key)
                                    <x-hash :hash="$account->decoded_public_key" :always-short="true" :copyable="true" />
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                        </div>
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>
    </div>

    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Transactions') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'transactions']),
            __('Tokens') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'tokens']),
            __('Rewards') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'rewards']),
            __('Delegations') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'delegations']),
            __('Stakes') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'stakes']),
            __('Plasma') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'plasma']),
            __('Projects') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'projects']),
            __('JSON') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'json']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'transactions')
        <livewire:explorer.account.transactions-list :accountId="$account->id" lazy />
    @endif

    @if ($tab === 'tokens')
        <livewire:explorer.account.tokens-list :accountId="$account->id" lazy />
    @endif

    @if ($tab === 'rewards')
        <livewire:explorer.account.rewards-list :accountId="$account->id" lazy />
    @endif

    @if ($tab === 'delegations')
        <livewire:explorer.account.delegations-list :accountId="$account->id" lazy />
    @endif

    @if ($tab === 'stakes')
        <livewire:explorer.account.stakes-list :accountId="$account->id" lazy />
    @endif

    @if ($tab === 'plasma')
        <livewire:explorer.account.plasma-list :accountId="$account->id" lazy />
    @endif

    @if ($tab === 'projects')
        <livewire:explorer.account.projects-list :accountId="$account->id" lazy />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$account->raw_json" />
        </div>
    @endif

    <x-modals.modal class="modal-lg" id="edit-account-{{ $account->address }}">
        <livewire:utilities.update-social-profile item-type="address" :item-id="$account->address" :address="$account->address" :title="$account->address" />
    </x-modals.modal>
</x-app-layout>
