<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Manage your favorites</h4>
        </div>
        <div class="card-header">
            <div class="d-md-none">
                <select id="sections" class="form-select" wire:change="$emit('tabChange', $event.target.value)">
                    <option value="addresses" {{ $tab === 'addresses' ? 'selected' : '' }}>Addresses</option>
                    <option value="tokens" {{ $tab === 'tokens' ? 'selected' : '' }}>Tokens</option>
                    <option value="transactions" {{ $tab === 'transactions' ? 'selected' : '' }}>Transactions</option>
                    <option value="momentums" {{ $tab === 'momentums' ? 'selected' : '' }}>Momentums</option>
                </select>
            </div>
            <div class="d-none d-md-block">
                <ul class="nav nav-tabs-alt card-header-tabs">
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'addresses' ? 'active' : '' }}" wire:click="$emit('tabChange', 'addresses')">
                            Addresses
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'tokens' ? 'active' : '' }}" wire:click="$emit('tabChange', 'tokens')">
                            Tokens
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'transactions' ? 'active' : '' }}" wire:click="$emit('tabChange', 'transactions')">
                            Transactions
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'momentums' ? 'active' : '' }}" wire:click="$emit('tabChange', 'momentums')">
                            Momentums
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="tab-content">
                <div class="tab-pane show active">
                    <div class="w-100" wire:loading>
                        <div class="m-4">
                            <div class="row">
                                <div class="col-24 col-md-12 offset-md-6">
                                    <x-alert
                                        message="Processing request..."
                                        type="info"
                                        icon="arrow-repeat spin"
                                        class="d-flex justify-content-center mb-0"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div wire:loading.remove>
                        @if ($tab === 'addresses')
                            <livewire:tables.favourite-accounts key="{{now()}}" />
                        @elseif ($tab === 'tokens')
                            <livewire:tables.favourite-tokens key="{{now()}}" />
                        @elseif ($tab === 'transactions')
                            <livewire:tables.favourite-transactions key="{{now()}}" />
                        @elseif ($tab === 'momentums')
                            <livewire:tables.favourite-momentums key="{{now()}}" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
