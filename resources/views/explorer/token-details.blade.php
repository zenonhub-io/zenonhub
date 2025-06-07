<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <div class="d-flex">
                    @if ($token->socialProfile?->avatar)
                        <div class="flex-fill ms-auto d-flex align-items-start me-3">
                            <img src="{{ $token->socialProfile?->avatar }}" class="rounded img-fluid" alt="{{ $token->name }} Logo" style="min-width: 60px; max-width: 60px;"/>
                        </div>
                    @endif
                    <div class="flex-fill">
                        <div class="d-flex align-items-center">
                            @if (! $token->socialProfile?->avatar)
                                <div class="title-icon me-3">
                                    {!! $token->avatar_svg !!}
                                </div>
                            @endif
                            <h5 class="text-muted">
                                {{ __('Token') }}
                                <x-copy :text="$token->token_standard" class="ms-2" :tooltip="__('Copy ZTS')" />
                                @if(! $token->is_network)
                                    <span class="pointer ms-2" data-bs-toggle="tooltip" data-bs-title="{{ __('Edit') }}">
                                        <i class="bi bi-pencil-square"
                                           data-bs-toggle="modal"
                                           data-bs-target="#edit-token-{{ $token->token_standard }}"></i>
                                    </span>
                                @endif
                            </h5>
                        </div>
                        <x-includes.header-title>
                            <h1 class="ls-tight text-wrap text-break">
                                {{ $token->name }} <span class="text-sm">{{ $token->symbol }}</span>
                            </h1>
                        </x-includes.header-title>
                    </div>
                </div>
                @if ($token->socialProfile)
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <x-social-profile.links :social-profile="$token->socialProfile" />
                    </div>
                @endif
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Supply')"
                            :info="__('The current amount of tokens that exist')">
                                <span data-bs-toggle="tooltip" data-bs-title="{{ $token->getFormattedAmount($token->total_supply) }}">
                                    {{ Number::abbreviate($token->getDisplayAmount($token->total_supply), 2) }}
                                </span>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Max Supply')"
                            :info="__('Maximum amount of tokens that can exist')">
                                <span data-bs-toggle="tooltip" data-bs-title="{{ $token->getFormattedAmount($token->max_supply) }}">
                                    {{ Number::abbreviate($token->getDisplayAmount($token->max_supply), 2) }}
                                </span>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-24 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Holders')"
                            :info="__('Total number of accounts with a balance of the token')"
                            :stat="number_format($token->holders_count)"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
        <x-cards.card class="mb-6">
            <x-cards.body>
                <div class="row">
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Token Standard')">
                                <x-hash :hash="$token->token_standard" :always-long="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Decimals')" :stat="$token->decimals" />
                            <x-stats.list-item :title="__('Domain')">
                                <x-link :href="external_url($token->domain)" :new-tab="true" :navigate="false">{{ $token->domain }}</x-link>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Owner')">
                                <x-address :account="$token->owner" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Created')" :hr="false">
                                <x-date-time.carbon :date="$token->created_at" />
                            </x-stats.list-item>
                            <hr class="d-block d-md-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Total Minted')" :stat="$token->getFormattedAmount($token->total_minted)" />
                            <x-stats.list-item :title="__('Total Burned')" :stat="$token->getFormattedAmount($token->total_burned)" />
                            <x-stats.list-item :title="__('Mintable')">
                                @if($token->is_mintable)
                                    <x-stats.indicator type="success" /> {{ __('Yes') }}
                                @else
                                    <x-stats.indicator type="danger" /> {{ __('No') }}
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Burnable')">
                                @if($token->is_burnable)
                                    <x-stats.indicator type="success" /> {{ __('Yes') }}
                                @else
                                    <x-stats.indicator type="danger" /> {{ __('No') }}
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Utility')" :hr="false">
                                @if($token->is_utility)
                                    <x-stats.indicator type="success" /> {{ __('Yes') }}
                                @else
                                    <x-stats.indicator type="danger" /> {{ __('No') }}
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
            __('Holders') => route('explorer.token.detail', ['zts' => $token->token_standard, 'tab' => 'holders']),
            __('Transactions') => route('explorer.token.detail', ['zts' => $token->token_standard, 'tab' => 'transactions']),
            __('Mints') => route('explorer.token.detail', ['zts' => $token->token_standard, 'tab' => 'mints']),
            __('Burns') => route('explorer.token.detail', ['zts' => $token->token_standard, 'tab' => 'burns']),
            __('JSON') => route('explorer.token.detail', ['zts' => $token->token_standard, 'tab' => 'json']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'holders')
        <livewire:explorer.token.holders-list :tokenId="$token->id" lazy />
    @endif

    @if ($tab === 'transactions')
        <livewire:explorer.token.transactions-list :tokenId="$token->id" lazy />
    @endif

    @if ($tab === 'mints')
        <livewire:explorer.token.mints-list :tokenId="$token->id" lazy />
    @endif

    @if ($tab === 'burns')
        <livewire:explorer.token.burns-list :tokenId="$token->id" lazy />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-cards.card>
                <x-cards.body>
                    <x-code-highlighters.json :code="$token->raw_json ?: ['error' => __('Data unavailable, please try again')]" />
                </x-cards.body>
            </x-cards.card>
        </div>
    @endif

    <x-modals.modal class="modal-lg" id="edit-token-{{ $token->token_standard }}">
        <livewire:utilities.update-social-profile item-type="token" :item-id="$token->token_standard" :address="$token->owner->address" :title="$token->name" />
    </x-modals.modal>
</x-app-layout>
