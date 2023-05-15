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
                        <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-3 mb-2 p-3">
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">Total</span>
                                <span class="float-end float-md-none">{{ $token->getDisplayAmount($token->total_supply) }}</span>
                            </div>
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">Max</span>
                                <span class="float-end float-md-none">{{ $token->getDisplayAmount($token->max_supply) }}</span>
                            </div>
                            <div class="text-start text-md-center">
                                <span class="d-inline d-md-block fs-sm text-muted">Hodlers</span>
                                <span class="float-end float-md-none">{{ number_format($token->holders_count) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-24">
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Symbol</span>
                                {{ $token->symbol }}
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Domain</span>
                                {{ $token->domain }}
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">ZTS</span>
                                {{ $token->token_standard }}
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Created</span>
                                {{ ($token->created_at ? $token->created_at->format(config('zenon.date_format')) : '-') }}
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Owner</span>
                                <x-address :account="$token->owner"/>
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
                                Holders
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'transactions' ? 'active' : '' }}" wire:click="$set('tab', 'transactions')">
                                Transactions
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'mints' ? 'active' : '' }}" wire:click="$set('tab', 'mints')">
                                Mints
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'burns' ? 'active' : '' }}" wire:click="$set('tab', 'burns')">
                                Burns
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
                            @if ($token->raw_json)
                                <pre class="line-numbers"><code class="lang-json">{{ pretty_json($token->raw_json) }}</code></pre>
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
