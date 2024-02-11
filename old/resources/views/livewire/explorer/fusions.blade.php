<div>
    <div class="card shadow">
        <div class="card-header border-bottom">
            <div class="d-block d-sm-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-3 mb-sm-0">
                        Fusions
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
                        From
                    </th>
                    <th></th>
                    <th>
                        Beneficiary
                    </th>
                    <th>
                        <button type="button" class="btn btn-sort" wire:click="sortBy('amount')">
                            <x-table-sort-button :sort="$sort" :order="$order" check="amount"/>
                        </button>
                    </th>
                    <th>
                        <button type="button" class="btn btn-sort" wire:click="sortBy('started_at')">
                            <x-table-sort-button :sort="$sort" :order="$order" check="started_at" title="Timestamp"/>
                        </button>
                    </th>
                    <th>
                        Duration
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $fusion)
                    <tr>
                        <td>
                            <x-address :account="$fusion->from_account" :eitherSide="8" :alwaysShort="true"/>
                        </td>
                        <td class="px-0">
                            {!! svg('explorer/send', 'text-success', 'transform: rotate(90deg);') !!}
                        </td>
                        <td>
                            <x-address :account="$fusion->to_account" :eitherSide="8" :alwaysShort="true"/>
                        </td>
                        <td>{{ $fusion->display_amount }} QSR</td>
                        <td>{{ $fusion->started_at->format(config('zenon.short_date_format')) }}</td>
                        <td>{{ $fusion->display_duration }}</td>
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
