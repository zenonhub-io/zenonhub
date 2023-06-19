<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Manage your favorites</h4>
        </div>
        <div class="card-header">
            <div class="d-md-none">
                <select id="sections" class="form-control" wire:change="$emit('tabChange', $event.target.value)">
                    <option value="addresses" {{ $tab === 'addresses' ? 'selected' : '' }}>Addresses</option>
                    <option value="addresses" {{ $tab === 'pillars' ? 'selected' : '' }}>Pillars</option>
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
                        <button class="btn nav-link {{ $tab === 'pillars' ? 'active' : '' }}" wire:click="$emit('tabChange', 'pillars')">
                            Pillars
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
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane show active">
                    @if ($tab === 'addresses')

                    @endif

                    @if ($tab === 'pillars')

                    @endif

                    @if ($tab === 'tokens')

                    @endif

                    @if ($tab === 'transactions')

                    @endif

                    @if ($tab === 'momentums')

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
