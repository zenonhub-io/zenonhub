<div>
    <div class="card shadow">
        <div class="card-header">
            <div class="d-block d-sm-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-3">
                        Accounts
                    </h4>
                </div>
                <div class="d-block d-md-flex justify-content-md-end mb-3">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>
            <div class="d-md-none">
                <select id="account-sections" class="form-select" wire:change="setTab($event.target.value)">
                    <option value="all" {{ $tab === 'all' ? 'selected' : '' }}>All</option>
                    <option value="contracts" {{ $tab === 'contracts' ? 'selected' : '' }}>Contracts</option>
                    <option value="pillars" {{ $tab === 'pillars' ? 'selected' : '' }}>Pillars</option>
                    <option value="sentinels" {{ $tab === 'sentinels' ? 'selected' : '' }}>Sentinels</option>
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
                        <button class="btn nav-link {{ $tab === 'contracts' ? 'active' : '' }}" wire:click="setTab('contracts')">
                            Contracts
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'pillars' ? 'active' : '' }}" wire:click="setTab('pillars')">
                            Pillars
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'sentinels' ? 'active' : '' }}" wire:click="setTab('sentinels')">
                            Sentinels
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
                                Address
                            </th>
                            <th>
                                <button type="button" class="btn btn-sort" wire:click="sortBy('sent_blocks_count')">
                                    <x-table-sort-button :sort="$sort" :order="$order" check="sent_blocks_count" title="Height"/>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="btn btn-sort" wire:click="sortBy('znn_balance')">
                                    <x-table-sort-button :sort="$sort" :order="$order" check="znn_balance" title="ZNN"/>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="btn btn-sort" wire:click="sortBy('qsr_balance')">
                                    <x-table-sort-button :sort="$sort" :order="$order" check="qsr_balance" title="QSR"/>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="btn btn-sort" wire:click="sortBy('updated_at')">
                                    <x-table-sort-button :sort="$sort" :order="$order" check="updated_at" title="Last active"/>
                                </button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $account)
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="pe-1 flex-grow-1"><x-address :account="$account" :eitherSide="10" breakpoint="lg"/></div>
                                        <div class="ps-1">
                                            @if ($account->active_pillar)
                                                <span class="ms-2 d-inline" data-bs-toggle="tooltip" data-bs-title="Pillar">
                                                    {!! svg('pillar', 'opacity-70', 'height: 18px') !!}
                                                </span>
                                            @endif
                                            @if ($account->active_sentinel)
                                                <span class="ms-2 d-inline" data-bs-toggle="tooltip" data-bs-title="Sentinel">
                                                    {!! svg('sentinel', '', 'width: 16px') !!}
                                                </span>
                                            @endif
                                            @if ($account->is_embedded_contract)
                                                <span class="d-inline" data-bs-toggle="tooltip" data-bs-title="Embedded contract">
                                                    <i class="bi bi-file-text-fill opacity-70"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ number_format($account->sent_blocks_count) }}
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-title="{{ $account->displayZnnBalance() }}">
                                        {{ $account->displayZnnBalance(2) }}
                                    </span>
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-title="{{ $account->displayQsrBalance() }}">
                                        {{ $account->displayQsrBalance(2) }}
                                    </span>
                                </td>
                                <td>{{ $account->updated_at?->format(config('zenon.short_date_format')) }}</td>
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
                        message="No accounts found"
                        type="info"
                        icon="info-circle-fill"
                        class="mb-0"
                    />
                </div>
            @endif
        </div>
    </div>
</div>
