<div>
    @if (! $token)
    @else
        <div class="card shadow mb-4">
            <div class="card-header">
                <div class="text-muted d-flex justify-content-between">
                    Token | {{ ($token->has_custom_label ? $token->custom_label : $token->symbol) }}
                    <span>
                        @if (! auth()->check())
                            <a href="{{ route('login', ['redirect' => url()->current()]) }}">
                                <i
                                    class="bi bi-star"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Add Favorite"
                                ></i>
                            </a>
                        @else
                            <i
                                class="bi {{ $token->is_favourited ? 'bi-star-fill' : 'bi-star' }} hover-text"
                                data-bs-toggle="tooltip"
                                data-bs-title="{{ $token->is_favourited ? 'Edit' : 'Add' }} Favorite"
                                wire:click="$emit('showModal', 'modals.explorer.manage-favorite-token', '{{ $token->token_standard }}')"
                            ></i>
                        @endif
                        <i class="bi bi-clipboard ms-2 hover-text js-copy" data-clipboard-text="{{ $token->token_standard }}" data-bs-toggle="tooltip" data-bs-title="Copy token standard"></i>
                    </span>
                </div>
                <h4 class="mb-0">
                    {{ $token->name }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-24">
                        <div class="bg-secondary shadow rounded-3 mb-2 p-3">
                            <div class="d-block d-md-flex justify-content-md-evenly">
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">Total Supply <i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Total amount of tokens currently in circulation"></i></span>
                                    <span class="float-end float-md-none">{{ $token->getDisplayAmount($token->total_supply) }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">Max Supply <i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Total amount of tokens that can be minted"></i></span>
                                    <span class="float-end float-md-none">{{ $token->getDisplayAmount($token->max_supply) }}</span>
                                </div>
                                <div class="text-start text-md-center">
                                    <span class="d-inline d-md-block fs-sm text-muted">Hodlers</span>
                                    <span class="float-end float-md-none">{{ number_format($token->holders_count) }}</span>
                                </div>
                            </div>
                            <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-0 border-1 border-top-md mt-md-4 pt-md-4">
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">Decimals</span>
                                    <span class="float-end float-md-none">
                                        {{ $token->decimals }}
                                    </span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">Mintable</span>
                                    <span class="float-end float-md-none">
                                        <span class="legend-indicator bg-{{ ($token->is_mintable ? 'success' : 'danger') }}"></span>
                                    </span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block fs-sm text-muted">Burnable</span>
                                    <span class="float-end float-md-none">
                                        <span class="legend-indicator bg-{{ ($token->is_burnable ? 'success' : 'danger') }}"></span>
                                    </span>
                                </div>
                                <div class="text-start text-md-center">
                                    <span class="d-inline d-md-block fs-sm text-muted">Utility</span>
                                    <span class="float-end float-md-none">
                                        <span class="legend-indicator bg-{{ ($token->is_utility ? 'success' : 'danger') }}"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24">
                        <ul class="list-group list-group-flush mb-0">
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Domain</span>
                                <a href="{{ $token->domain }}" target="_blank">{{ $token->domain }}</a>
                            </li>
                            <li class="list-group-item">
                                <span class="d-block fs-sm text-muted">Token Standard (ZTS)</span>
                                {{ $token->token_standard }}
                            </li>
                            @if ($token->is_mintable)
                                <li class="list-group-item">
                                    <span class="d-block fs-sm text-muted">Total Minted</span>
                                    {{ $token->display_total_minted }}
                                </li>
                            @endif
                            @if ($token->is_burnable)
                                <li class="list-group-item">
                                    <span class="d-block fs-sm text-muted">Total Burned</span>
                                    {{ $token->display_total_burned }}
                                </li>
                            @endif
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
