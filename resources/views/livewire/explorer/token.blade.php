<div>
    @if (! $token)
    @else
        <div class="card shadow mb-4">
            <div class="card-header">
                <div class="text-muted fs-sm">
                    Token
                </div>
                <h5 class="card-title mb-0">{{ $token->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-24">
                        <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-2 mb-2 p-3">
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">Total</span>
                                <span class="fw-bold float-end float-md-none">{{ $token->getDisplayAmount($token->total_supply) }}</span>
                            </div>
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">Max</span>
                                <span class="fw-bold float-end float-md-none">{{ $token->getDisplayAmount($token->max_supply) }}</span>
                            </div>
                            <div class="text-start text-md-center">
                                <span class="d-inline d-md-block fs-sm text-muted">Hodlers</span>
                                <span class="fw-bold float-end float-md-none">{{ number_format($token->holders_count) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-24">
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Symbol</span>
                                <span class="fw-bold">{{ $token->symbol }}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Domain</span>
                                <span class="fw-bold">{{ $token->domain }}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">ZTS</span>
                                <span class="fw-bold">{{ $token->token_standard }}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Created</span>
                                <span class="fw-bold">
                                    {{ ($token->created_at ? $token->created_at->format(config('zenon.date_format')) : '-') }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Owner</span>
                                <span class="fw-bold">
                                    <x-address :account="$token->owner"/>
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
                    <select id="token-sections" class="form-control" wire:change="$set('tab', $event.target.value)">
                        <option value="holders" {{ $tab === 'holders' ? 'selected' : '' }}>Holders</option>
                        <option value="transactions" {{ $tab === 'transactions' ? 'selected' : '' }}>Transactions</option>
                        <option value="mints" {{ $tab === 'mints' ? 'selected' : '' }}>Mints</option>
                        <option value="burns" {{ $tab === 'burns' ? 'selected' : '' }}>Burns</option>
                        <option value="json" {{ $tab === 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'holders' ? 'active' : '' }}" wire:click="$set('tab', 'holders')">
                                <i class="bi bi-people-fill opacity-70 me-2"></i> Holders
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'transactions' ? 'active' : '' }}" wire:click="$set('tab', 'transactions')">
                                <i class="bi bi-arrow-left-right opacity-70 me-2"></i> Transactions
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'mints' ? 'active' : '' }}" wire:click="$set('tab', 'mints')">
                                <i class="bi bi-plus-lg opacity-70 me-2"></i> Mints
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'burns' ? 'active' : '' }}" wire:click="$set('tab', 'burns')">
                                <i class="bi bi-fire opacity-70 me-2"></i> Burns
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
                    @if ($tab === 'holders')
                        <livewire:tables.token-holders :token="$token" key="{{now()}}" />
                    @elseif ($tab === 'transactions')
                        <livewire:tables.token-transactions :token="$token" key="{{now()}}" />
                    @elseif ($tab === 'mints')
                        <livewire:tables.token-mints :token="$token" key="{{now()}}" />
                    @elseif ($tab === 'burns')
                        <livewire:tables.token-burns :token="$token" key="{{now()}}" />
                    @elseif ($tab === 'json')
                        <div class="p-4">
                            <pre class="line-numbers"><code class="lang-json">{{ pretty_json($token->raw_json) }}</code></pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
