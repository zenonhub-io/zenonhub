<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-xs">{{ __('Account') }} {{ $account->has_custom_label ? ' | '.$account->custom_label : '' }}</span>
                <div class="d-flex align-items-center mb-1">
                    @if ($account->socialProfile?->avatar)
                        <div class="w-24 w-md-32">
                            <img src="{{ $token->socialProfile?->avatar }}" class="rounded float-start title-avatar me-2" alt="{{ $token->name }} Logo "/>
                        </div>
                    @endif
                    <x-includes.header-title :title="$account->address" />
                </div>
                <div class="d-flex align-items-center gap-3">
                    <x-social-profile.links :social-profile="$account->socialProfile" />
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-neutral btn-xs dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit-account-{{ $account->address }}">
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
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-24 col-md-8">
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
            <div class="col-24 col-md-8">
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
                        <div class="vstack gap-3">
                            <x-stats.list-item :title="__('Plasma')">
                                @if ($account->plasma_level === 'High')
                                    <x-stats.indicator type="success" /> {{ $account->plasma_level }}
                                @elseif ($account->plasma_level === 'Medium')
                                    <x-stats.indicator type="warning" /> {{ $account->plasma_level }}
                                @elseif ($account->plasma_level === 'Low')
                                    <x-stats.indicator type="danger" /> {{ $account->plasma_level }}
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Fused QSR')" :stat="$account->display_fused_qsr" />
                            <x-stats.list-item :title="__('Staked ZNN')" :stat="$account->display_staked_znn" />
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
                            <hr class="d-block d-md-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-3">
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
                            <x-stats.list-item :title="__('Funded by')">
                                <x-address :account="$account->fundingBlock?->account" :always-short="true "/>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Funding Tx')">
                                <x-link :href="route('explorer.transaction.detail', ['hash' => $account->fundingBlock?->hash])">
                                    <x-hash :hash="$account->fundingBlock?->hash" :always-short="true" />
                                </x-link>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Public Key')" :hr="false">
                                <x-hash :hash="$account->decoded_public_key" :always-short="true" />
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
            __('Rewards') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'rewards']),
            __('Delegations') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'delegations']),
            __('Tokens') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'tokens']),
            __('Stakes') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'stakes']),
            __('Plasma') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'plasma']),
            __('Projects') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'projects']),
            __('JSON') => route('explorer.account.detail', ['address' => $account->address, 'tab' => 'json']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'transactions')
        <livewire:explorer.account.transactions-list :accountId="$account->id" />
    @endif

{{--    @if ($tab === 'rewards')--}}
{{--        <livewire:explorer.account.rewards-list :accountId="$account->id" />--}}
{{--    @endif--}}

{{--    @if ($tab === 'delegations')--}}
{{--        <livewire:explorer.account.delegations-list :accountId="$account->id" />--}}
{{--    @endif--}}

{{--    @if ($tab === 'tokens')--}}
{{--        <livewire:explorer.account.tokens-list :accountId="$account->id" />--}}
{{--    @endif--}}

{{--    @if ($tab === 'stakes')--}}
{{--        <livewire:explorer.account.stakes-list :accountId="$account->id" />--}}
{{--    @endif--}}

{{--    @if ($tab === 'plasma')--}}
{{--        <livewire:explorer.account.plasma-list :accountId="$account->id" />--}}
{{--    @endif--}}

{{--    @if ($tab === 'projects')--}}
{{--        <livewire:explorer.account.projects-list :accountId="$account->id" />--}}
{{--    @endif--}}

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$account->raw_json" />
        </div>
    @endif

    <x-modals.modal class="modal-lg" id="edit-account-{{ $account->address }}">
        <livewire:utilities.update-social-profile item-type="address" :item-id="$account->address" :address="$account->address" :title="$account->address" />
    </x-modals.modal>
</x-app-layout>
