<div>
    <div class="card shadow">
        <div class="card-header">
            <div class="d-block d-sm-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-3">
                        Tokens
                    </h4>
                </div>
                <div class="d-block d-md-flex justify-content-md-end mb-3">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>
            <div class="d-md-none">
                <select id="account-sections" class="form-select" wire:change="setTab($event.target.value)">
                    <option value="all" {{ $tab === 'all' ? 'selected' : '' }}>All</option>
                    <option value="network" {{ $tab === 'network' ? 'selected' : '' }}>Network</option>
                    <option value="user" {{ $tab === 'user' ? 'selected' : '' }}>User</option>
                    <option value="favorites" {{ $tab === 'favorites' ? 'selected' : '' }}>Favorites</option>
                </select>
            </div>
            <div class="d-none d-md-block">
                <ul class="nav nav-tabs-alt card-header-tabs">
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'all' ? 'active' : '' }}" wire:click="setTab('all')">
                            All
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'network' ? 'active' : '' }}" wire:click="setTab('network')">
                            Network
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'user' ? 'active' : '' }}" wire:click="setTab('user')">
                            User
                        </button>
                    </li>
                    @if (auth()->check())
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'favorites' ? 'active' : '' }}" wire:click="setTab('favorites')">
                                Favorites
                            </button>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="w-100" wire:loading>
            <div class="m-4">
                <div class="row">
                    <div class="col-24 col-md-8 offset-md-8">
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
            @if ($data && $data->count())
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle table-striped table-hover">
                        <thead>
                        <tr>
                            <th>
                                <button type="button" class="btn btn-sort" wire:click="sortBy('name')">
                                    <x-table-sort-button :sort="$sort" :order="$order" check="name"/>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="btn btn-sort" wire:click="sortBy('holders_count')">
                                    <x-table-sort-button :sort="$sort" :order="$order" check="holders_count" title="Holders"/>
                                </button>
                            </th>
                            <th>
                                Total Supply
                            </th>
                            <th>
                                Token Standard (ZTS)
                            </th>
                            <th>
                                <button type="button" class="btn btn-sort" wire:click="sortBy('created_at')">
                                    <x-table-sort-button :sort="$sort" :order="$order" check="created_at" title="Created"/>
                                </button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $token)
                            <tr>
                                <td>
                                    <a href="{{ route('explorer.token', ['zts' => $token->token_standard]) }}">
                                        {{ $token->custom_label }}
                                    </a>
                                    <span class="fs-xs">
                                        {{ $token->symbol }}
                                    </span>
                                </td>
                                <td>{{ number_format($token->holders_count) }}</td>
                                <td>{{ $token->getFormattedAmount($token->total_supply) }}</td>
                                <td>{{ short_hash($token->token_standard, 8) }}</td>
                                <td>{{ ($token->created_at ? $token->created_at?->format(config('zenon.short_date_format')) : '-') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="m-4 mt-2">
                    {{ $data->onEachSide(0)->links() }}
                </div>
            @elseif($data)
                <div class="m-4 mt-4">
                    <x-alert
                        message="No tokens found"
                        type="info"
                        icon="info-circle-fill"
                        class="mb-0"
                    />
                </div>
            @endif
        </div>
    </div>
</div>
