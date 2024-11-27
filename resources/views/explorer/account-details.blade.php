<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-xs">{{ __('Account') }}</span>
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
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        {{--                        <x-stats.mini-stat--}}
                        {{--                            :title="__('ZNN')"--}}
                        {{--                            :info="__('The current amount of tokens that exist')">--}}
                        {{--                                <span data-bs-toggle="tooltip" data-bs-title="{{ $token->getFormattedAmount($token->total_supply) }}">--}}
                        {{--                                    {{ Number::abbreviate($token->getDisplayAmount($token->total_supply), 2) }}--}}
                        {{--                                </span>--}}
                        {{--                        </x-stats.mini-stat>--}}
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        {{--                        <x-stats.mini-stat--}}
                        {{--                            :title="__('QSR')"--}}
                        {{--                            :info="__('Maximum amount of tokens that can exist')">--}}
                        {{--                                <span data-bs-toggle="tooltip" data-bs-title="{{ $token->getFormattedAmount($token->max_supply) }}">--}}
                        {{--                                    {{ Number::abbreviate($token->getDisplayAmount($token->max_supply), 2) }}--}}
                        {{--                                </span>--}}

                        {{--                        </x-stats.mini-stat>--}}
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        {{--                        <x-stats.mini-stat--}}
                        {{--                            :title="__('USD')"--}}
                        {{--                            :stat="number_format($token->holders_count)"--}}
                        {{--                        />--}}
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        {{--                        <x-stats.mini-stat--}}
                        {{--                            :title="__('Plasma')"--}}
                        {{--                            :stat="number_format($token->holders_count)"--}}
                        {{--                        />--}}
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
        {{--        <x-cards.card class="mb-6">--}}
        {{--            <x-cards.body>--}}
        {{--                <div class="row">--}}
        {{--                    <div class="col-24 col-lg-12">--}}
        {{--                        <div class="vstack gap-3">--}}
        {{--                            <x-stats.list-item :title="__('Token Standard')" :stat="$token->token_standard" />--}}
        {{--                            <x-stats.list-item :title="__('Decimals')" :stat="$token->decimals "/>--}}
        {{--                            <x-stats.list-item :title="__('Domain')">--}}
        {{--                                <x-link :href="$token->domain" :new-tab="true" :navigate="false">{{ $token->domain }}</x-link>--}}
        {{--                            </x-stats.list-item>--}}
        {{--                            <x-stats.list-item :title="__('Owner')">--}}
        {{--                                <x-address :account="$token->owner" :always-short="true "/>--}}
        {{--                            </x-stats.list-item>--}}
        {{--                            <x-stats.list-item :title="__('Created')" :hr="false">--}}
        {{--                                <x-date-time.carbon :date="$token->created_at" />--}}
        {{--                            </x-stats.list-item>--}}
        {{--                            <hr class="d-block d-md-none my-0 mb-3">--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <div class="col-24 col-lg-12">--}}
        {{--                        <div class="vstack gap-3">--}}
        {{--                            <x-stats.list-item :title="__('Total Minted')" :stat="$token->getFormattedAmount($token->total_minted)" />--}}
        {{--                            <x-stats.list-item :title="__('Total Burned')" :stat="$token->getFormattedAmount($token->total_burned)" />--}}
        {{--                            <x-stats.list-item :title="__('Mintable')">--}}
        {{--                                @if($token->is_mintable)--}}
        {{--                                    <x-stats.indicator type="success" /> {{ __('Yes') }}--}}
        {{--                                @else--}}
        {{--                                    <x-stats.indicator type="danger" /> {{ __('No') }}--}}
        {{--                                @endif--}}
        {{--                            </x-stats.list-item>--}}
        {{--                            <x-stats.list-item :title="__('Burnable')">--}}
        {{--                                @if($token->is_burnable)--}}
        {{--                                    <x-stats.indicator type="success" /> {{ __('Yes') }}--}}
        {{--                                @else--}}
        {{--                                    <x-stats.indicator type="danger" /> {{ __('No') }}--}}
        {{--                                @endif--}}
        {{--                            </x-stats.list-item>--}}
        {{--                            <x-stats.list-item :title="__('Utility')" :hr="false">--}}
        {{--                                @if($token->is_utility)--}}
        {{--                                    <x-stats.indicator type="success" /> {{ __('Yes') }}--}}
        {{--                                @else--}}
        {{--                                    <x-stats.indicator type="danger" /> {{ __('No') }}--}}
        {{--                                @endif--}}
        {{--                            </x-stats.list-item>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--            </x-cards.body>--}}
        {{--        </x-cards.card>--}}
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

    @if ($tab === 'rewards')
        <livewire:explorer.account.rewards-list :accountId="$account->id" />
    @endif

    @if ($tab === 'delegations')
        <livewire:explorer.account.delegations-list :accountId="$account->id" />
    @endif

    @if ($tab === 'tokens')
        <livewire:explorer.account.tokens-list :accountId="$account->id" />
    @endif

    @if ($tab === 'stakes')
        <livewire:explorer.account.stakes-list :accountId="$account->id" />
    @endif

    @if ($tab === 'plasma')
        <livewire:explorer.account.plasma-list :accountId="$account->id" />
    @endif

    @if ($tab === 'projects')
        <livewire:explorer.account.projects-list :accountId="$account->id" />
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
