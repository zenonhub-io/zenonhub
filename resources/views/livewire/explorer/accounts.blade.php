<div>
    <div class="card-header border-bottom">
        <div class="d-block d-sm-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="mb-3 mb-sm-0">
                    Accounts
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
                    <button type="button" class="btn btn-sort" wire:click="sortBy('znn_balance')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="znn_balance" title="ZNN"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('total_znn_balance')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="total_znn_balance" title="Total ZNN" tooltip="Includes ZNN locked in staking, pillars and sentinels"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('qsr_balance')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="qsr_balance" title="QSR"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('total_qsr_balance')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="total_qsr_balance" title="Total QSR" tooltip="Includes fused QSR and sentinel lockup"/>
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
                                @if ($account->pillar)
                                    <span class="ms-2 d-inline" data-bs-toggle="tooltip" data-bs-title="Pillar">
                                        {!! svg('pillar', 'opacity-70', 'height: 18px') !!}
                                    </span>
                                @endif
                                @if ($account->sentinel)
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
                        <span data-bs-toggle="tooltip" data-bs-title="{{ $account->displayZnnBalance() }}">
                            {{ $account->displayZnnBalance(2) }}
                        </span>
                    </td>
                    <td>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ $account->displayTotalZnnBalance() }}">
                            {{ $account->displayTotalZnnBalance(2) }}
                        </span>
                    </td>
                    <td>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ $account->displayQsrBalance() }}">
                            {{ $account->displayQsrBalance(2) }}
                        </span>
                    </td>
                    <td>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ $account->displayTotalQsrBalance() }}">
                            {{ $account->displayTotalQsrBalance(2) }}
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
</div>
