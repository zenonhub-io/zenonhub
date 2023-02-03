<div>
    @if (! $account)
    @else
        <div class="card shadow mb-4">
            <div class="card-header border-bottom">
            <span class="float-end">
                @if ($account->pillar)
                    <span class="badge bg-faded-light ms-2 mb-2 d-block">
                        {!! svg('pillar', 'me-1 opacity-70', 'height: 11px') !!}
                        Pillar
                    </span>
                @endif
                @if ($account->sentinel)
                    <span class="badge bg-faded-light ms-2 mb-2 d-block">
                        {!! svg('sentinel', 'me-1 opacity-70', 'width: 11px') !!}
                        Sentinel
                    </span>
                @endif
                @if ($account->is_embedded_contract)
                    <span class="badge bg-faded-light ms-2 mb-2 d-block">
                        <i class="bi bi-file-text-fill me-1 opacity-70"></i>
                        Embedded
                    </span>
                @endif
            </span>
                <div class="text-muted fs-sm">
                    Account {{ ($account->is_named_address ?  ' | ' . $account->named_address : '') }}
                </div>
                <h4 class="mb-0">
                    {{ $account->address }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-24">
                        <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-2 mb-2 p-3">
                            <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                                <span class="d-inline d-md-block fs-sm text-muted">ZNN</span>
                                <span class="fw-bold float-end float-md-none text-zenon-green pb-2">{{ $account->display_znn_balance }}</span>
                            </div>
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">QSR</span>
                                <span class="fw-bold float-end float-md-none text-zenon-blue pb-2">{{ $account->display_qsr_balance }}</span>
                            </div>
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">USD</span>
                                <span class="fw-bold float-end float-md-none">{{ $account->display_usd_balance }}</span>
                            </div>
                            <div class="text-start text-md-center">
                                <span class="d-inline d-md-block fs-sm text-muted">Plasma</span>
                                <span class="fw-bold float-end float-md-none text-zenon-blue pb-2">
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
                    </div>
                    <div class="col-24">
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Fused QSR</span>
                                <span class="fw-bold">
                                    {{ $account->display_qsr_fused }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Staked ZNN</span>
                                <span class="fw-bold">
                                    {{ ($account->display_znn_staked ?: '-') }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">ZNN rewards</span>
                                <span class="fw-bold">
                                    {{ ($account->displayTotalZnnRewards ?: '-') }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">QSR rewards</span>
                                <span class="fw-bold">
                                    {{ ($account->displayTotalQsrRewards ?: '-') }}
                                </span>
                            </li>
                            @if ($account->pillar)
                                <li class="list-group-item">
                                    <span class="d-block fs-sm">Pillar Name</span>
                                    <span class="fw-bold">
                                    <a href="{{ route('pillars.detail', ['slug' => $account->pillar->slug]) }}">
                                        {{ $account->pillar->name }}
                                    </a>
                                </span>
                                </li>
                            @endif
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Delegating To</span>
                                <span class="fw-bold">
                                    @if ($account->active_delegation)
                                        <a href="{{ route('pillars.detail', ['slug' => $account->active_delegation->pillar->slug]) }}">
                                        {{ $account->active_delegation->pillar->name }}
                                    </a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Last Active</span>
                                <span class="fw-bold">
                                    {{ ($account->latest_block ? $account->latest_block->created_at->format(config('zenon.date_format')) : '-') }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">First Active</span>
                                <span class="fw-bold">
                                    {{ ($account->first_block ? $account->first_block->created_at->format(config('zenon.date_format')) : '-') }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Public Key</span>
                                <span class="fw-bold">
                                    {{ ($account->decoded_public_key ?: '-') }}
                                </span>
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
                        <option value="sent-transactions" {{ $tab === 'sent-transactions' ? 'selected' : '' }}>Sent</option>
                        <option value="received-transactions" {{ $tab === 'received-transactions' ? 'selected' : '' }}>Received</option>
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
                            <button class="btn nav-link {{ $tab === 'sent-transactions' ? 'active' : '' }}" wire:click="$set('tab', 'sent-transactions')">
                                <i class="bi bi-arrow-up-square-fill opacity-70 me-2"></i> Sent
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'received-transactions' ? 'active' : '' }}" wire:click="$set('tab', 'received-transactions')">
                                <i class="bi bi-arrow-down-square-fill opacity-70 me-2"></i> Received
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'rewards' ? 'active' : '' }}" wire:click="$set('tab', 'rewards')">
                                <i class="bi bi-trophy-fill opacity-70 me-2"></i> Rewards
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'delegations' ? 'active' : '' }}" wire:click="$set('tab', 'delegations')">
                                <i class="bi bi-clock-history opacity-70 me-2"></i> Delegations
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'tokens' ? 'active' : '' }}" wire:click="$set('tab', 'tokens')">
                                <i class="bi bi-circle opacity-70 me-2"></i> Tokens
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'staking' ? 'active' : '' }}" wire:click="$set('tab', 'staking')">
                                <i class="bi bi-lock-fill opacity-70 me-2"></i> Staking
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'plasma' ? 'active' : '' }}" wire:click="$set('tab', 'plasma')">
                                <i class="bi bi-fire opacity-70 me-2"></i> Plasma
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'projects' ? 'active' : '' }}" wire:click="$set('tab', 'projects')">
                                {!! svg('az', 'opacity-70 me-2', 'width: 14px') !!}  Projects
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'json' ? 'active' : '' }}" wire:click="$set('tab', 'json')">
                                <i class="bi bi-code-slash opacity-70 me-2"></i> JSON
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane show active">
                    @if ($tab === 'sent-transactions')
                        <livewire:tables.account-sent-blocks :account="$account" key="{{now()}}" />
                    @elseif ($tab === 'received-transactions')
                        <livewire:tables.account-received-blocks :account="$account" key="{{now()}}" />
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
                            <pre class="line-numbers"><code class="lang-json">{{ pretty_json($account->raw_json) }}</code></pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
