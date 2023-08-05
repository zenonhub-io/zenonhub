<div>
    <div class="card shadow">
        <div class="card-header border-bottom">
            <div class="d-block d-sm-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-3 mb-sm-0">
                        Staking
                    </h4>
                </div>
                <div class="d-block d-md-flex justify-content-md-end">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>
        </div>
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
