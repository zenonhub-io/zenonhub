<div>
    @if (! $account)
    @else
        <div class="card shadow mb-4">
            <div class="card-header border-bottom">
                <div class="text-muted d-flex justify-content-between">
                    <span>
                        Account {{ ($account->is_named_address ?  ' | ' . $account->named_address : '') }}
                        @if ($account->active_pillar)
                            <a class="ms-2" href="{{ route('pillars.detail', ['slug' => $account->pillar->slug]) }}" data-bs-toggle="tooltip" data-bs-title="Pillar">
                                {!! svg('pillar', '', 'height: 16px') !!}
                            </a>
                        @endif
                        @if ($account->active_sentinel)
                            <span class="ms-2" data-bs-toggle="tooltip" data-bs-title="Sentinel">
                                {!! svg('sentinel', '', 'height: 16px') !!}
                            </span>
                        @endif
                        @if ($account->is_embedded_contract)
                            <span class="ms-2" data-bs-toggle="tooltip" data-bs-title="Embedded contract">
                                <i class="bi bi-file-text-fill"></i>
                            </span>
                        @endif
                    </span>
                    <span>
                        <i
                            class="bi {{ $account->is_favourited ? 'bi-star-fill' : 'bi-star' }} hover-text"
                            data-bs-toggle="tooltip"
                            data-bs-title="{{ $account->is_favourited ? 'Edit' : 'Add' }} Favorite"
                            wire:click="$emit('showModal', 'modals.manage-account-favorite', '{{ $account->address }}')"
                        ></i>
                        <i class="bi bi-clipboard ms-2 hover-text js-copy" data-clipboard-text="{{ $account->address }}" data-bs-toggle="tooltip" data-bs-title="Copy addresses"></i>
                    </span>
                </div>
                <h4 class="mb-0">
                    {{ $account->address }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-24">
                        <div class="bg-secondary shadow rounded-3 mb-2 p-3">
                            <div class="d-block d-md-flex justify-content-md-evenly">
                                <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">ZNN</span>
                                    <span class="float-end float-md-none text-zenon-green pb-2">{{ $account->display_znn_balance }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">QSR</span>
                                    <span class="float-end float-md-none text-zenon-blue pb-2">{{ $account->display_qsr_balance }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">USD</span>
                                    <span class="float-end float-md-none">{{ $account->display_usd_balance }}</span>
                                </div>
                                <div class="text-start text-md-center">
                                    <span class="d-inline d-md-block fs-sm text-muted">Plasma</span>
                                    <span class="float-end float-md-none text-zenon-blue pb-2">
                                        @if ($account->plasma_level === 'High')
                                            <span class="legend-indicator bg-success ms-1" data-bs-toggle="tooltip" data-bs-title="High plasma"></span>
                                        @elseif ($account->plasma_level === 'Medium')
                                            <span class="legend-indicator bg-warning ms-1" data-bs-toggle="tooltip" data-bs-title="Medium plasma"></span>
                                        @elseif ($account->plasma_level === 'Low')
                                            <span class="legend-indicator bg-danger ms-1" data-bs-toggle="tooltip" data-bs-title="Low plasma"></span>
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-0 border-1 border-top-md mt-md-4 pt-md-4">
                                <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">Fused QSR</span>
                                    <span class="float-end float-md-none pb-2">{{ ($account->display_qsr_fused ?: '-') }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">Staked ZNN</span>
                                    <span class="float-end float-md-none pb-2">{{ ($account->display_znn_staked ?: '-') }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">ZNN rewards</span>
                                    <span class="float-end float-md-none pb-2">
                                        {{ ($account->display_total_znn_rewards ?: '-') }}
                                    </span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">QSR rewards</span>
                                    <span class="float-end float-md-none pb-2">
                                        {{ ($account->display_total_qsr_rewards ?: '-') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24">
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Last Active</span>
                                {{ ($account->latest_block ? $account->latest_block->created_at->format(config('zenon.date_format')) : '-') }}
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">First Active</span>
                                {{ ($account->first_block ? $account->first_block->created_at->format(config('zenon.date_format')) : '-') }}
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Delegating To</span>
                                @if ($account->active_delegation)
                                    <a href="{{ route('pillars.detail', ['slug' => $account->active_delegation->pillar->slug]) }}">
                                        {{ $account->active_delegation->pillar->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Public Key</span>
                                {{ ($account->decoded_public_key ?: '-') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">
                <div class="d-md-none">
                    <select id="account-sections" class="form-control" wire:change="$set('tab', $event.target.value)">
                        <option value="transactions" {{ $tab === 'transactions' ? 'selected' : '' }}>Transactions</option>
                        <option value="rewards" {{ $tab === 'rewards' ? 'selected' : '' }}>Rewards</option>
                        <option value="delegations" {{ $tab === 'delegations' ? 'selected' : '' }}>Delegations</option>
                        <option value="tokens" {{ $tab === 'tokens' ? 'selected' : '' }}>Tokens</option>
                        <option value="staking" {{ $tab === 'staking' ? 'selected' : '' }}>Staking</option>
                        <option value="plasma" {{ $tab === 'plasma' ? 'selected' : '' }}>Plasma</option>
                        <option value="projects" {{ $tab === 'projects' ? 'selected' : '' }}>Projects</option>
                        <option value="json" {{ $tab === 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'transactions' ? 'active' : '' }}" wire:click="$set('tab', 'transactions')">
                                Transactions
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'rewards' ? 'active' : '' }}" wire:click="$set('tab', 'rewards')">
                                Rewards
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'delegations' ? 'active' : '' }}" wire:click="$set('tab', 'delegations')">
                                Delegations
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'tokens' ? 'active' : '' }}" wire:click="$set('tab', 'tokens')">
                                Tokens
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'staking' ? 'active' : '' }}" wire:click="$set('tab', 'staking')">
                                Staking
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'plasma' ? 'active' : '' }}" wire:click="$set('tab', 'plasma')">
                                Plasma
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'projects' ? 'active' : '' }}" wire:click="$set('tab', 'projects')">
                                Projects
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'json' ? 'active' : '' }}" wire:click="$set('tab', 'json')">
                                JSON
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane show active">
                    @if ($tab === 'transactions')
                        <livewire:tables.account-blocks :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'rewards')
                        <livewire:tables.account-rewards :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'delegations')
                        <livewire:tables.account-delegations :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'tokens')
                        <livewire:tables.account-tokens :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'staking')
                        <livewire:tables.account-stakes :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'plasma')
                        <livewire:tables.account-plasma :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'projects')
                        <livewire:tables.account-projects :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'json')
                        <div class="p-4">
                            @if ($account->raw_json)
                                <pre class="line-numbers"><code class="lang-json">{{ pretty_json($account->raw_json) }}</code></pre>
                            @else
                                <x-alert
                                    message="Unable to load JSON data"
                                    type="info"
                                    icon="info-circle-fill"
                                    class="d-flex mb-0"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
