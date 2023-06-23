<div wire:init="shouldLoadResults">
    <div class="p-4">
        <div class="row">
            <div class="col-24 col-md-16 mb-3 mb-md-0 align-self-center">
                <livewire:tables.toolbar :enableExport="true" :search="$search" />
            </div>
            <div class="col-24 col-md-8">
                <div class="d-flex justify-content-center justify-content-md-end">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>
        </div>
    </div>
    <div class="w-100" wire:loading.delay>
        <div class="m-4 mt-0">
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
                <table class="table table-nowrap align-middle table-striped table-hover top-border">
                    <thead>
                    <tr>
                        <th>
                            Address
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('weight')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="weight"/>
                            </button>
                        </th>
                        <th>
                            Share
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('started_at')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="started_at" title="Started"/>
                            </button>
                        </th>
                        <th>
                            Duration
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $delegate)
                        <tr>
                            <td>
                                <x-address :account="$delegate->account" :eitherSide="8" :alwaysShort="true"/>
                            </td>
                            <td>{{ $delegate->display_weight }}</td>
                            <td>{{ $delegate->display_percentage_share }}%</td>
                            <td>{{ $delegate->started_at->format(config('zenon.short_date_format')) }}</td>
                            <td>{{ $delegate->display_duration }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="m-4 mt-2">
                {{ $data->links() }}
            </div>
        @elseif($data)
            <div class="m-4 mt-0">
                <x-alert
                    message="No delegators found"
                    type="info"
                    icon="info-circle-fill"
                    class="mb-0"
                />
            </div>
        @endif
    </div>
</div>
