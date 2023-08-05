<div>
    <div class="card shadow">
        <div class="card-header">
            <div class="d-block d-sm-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-3">
                        Staking
                    </h4>
                </div>
                <div class="d-block d-md-flex justify-content-md-end mb-3">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>
            <div class="d-md-none">
                <select id="account-sections" class="form-control" wire:change="setTab($event.target.value)">
                    <option value="all" {{ $tab === 'all' ? 'selected' : '' }}>All</option>
                    <option value="znn" {{ $tab === 'znn' ? 'selected' : '' }}>ZNN</option>
                    <option value="lp-eth" {{ $tab === 'lp-eth' ? 'selected' : '' }}>LP-ETH</option>
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
                        <button class="btn nav-link {{ $tab === 'znn' ? 'active' : '' }}" wire:click="setTab('znn')">
                            ZNN
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'lp-eth' ? 'active' : '' }}" wire:click="setTab('lp-eth')">
                            LP-ETH
                        </button>
                    </li>
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
            <div class="table-responsive">
                <table class="table table-nowrap align-middle table-striped table-hover">
                    <thead>
                    <tr>
                        <th>
                            Address
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('amount')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="amount"/>
                            </button>
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('started_at')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="started_at" title="Started"/>
                            </button>
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('duration')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="duration" title="Lockup"/>
                            </button>
                        </th>
                        <th>
                            Available
                        </th>
                        <th>
                            Duration
                        </th>
                        <th>
                            Hash
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $stake)
                        <tr>
                            <td>
                                <x-address :account="$stake->account" :eitherSide="8" :alwaysShort="true"/>
                            </td>
                            <td>
                                {{ $stake->display_amount }} <a href=" {{ route('explorer.token', ['zts' => $stake->token->token_standard]) }}">
                                    {{ $stake->token->custom_label }}
                                </a>
                            </td>
                            <td>{{ $stake->started_at->format(config('zenon.short_date_format')) }}</td>
                            <td>{{ $stake->display_duration }}</td>
                            <td>{{ $stake->end_date->format(config('zenon.short_date_format')) }}</td>
                            <td>{{ $stake->started_at->diffForHumans(['parts' => 2], true) }}</td>
                            <td>
                                <a href=" {{ route('explorer.transaction', ['hash' => $stake->hash]) }}">
                                    <x-hash-tooltip :hash="$stake->hash" :eitherSide="8" :alwaysShort="true"/>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="m-4 mt-2">
                {{ $data->onEachSide(0)->links() }}
            </div>
        </div>
    </div>
</div>
