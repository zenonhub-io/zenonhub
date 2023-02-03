<div wire:init="shouldLoadResults">
    <div class="p-4">
        <div class="row">
            <div class="col-24 col-md-16 mb-3 mb-md-0 align-self-center">
                <livewire:tables.toolbar :enableExport="true" />
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
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $stake)
                        <tr>
                            <td>{{ $stake->display_amount }} ZNN</td>
                            <td>{{ $stake->started_at->format(config('zenon.short_date_format')) }}</td>
                            <td>{{ $stake->display_duration }}</td>
                            <td>{{ $stake->end_date->format(config('zenon.short_date_format')) }}</td>
                            <td>{{ $stake->started_at->diffForHumans(['parts' => 2], true) }}</td>
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
                    message="No active stakes found"
                    type="info"
                    icon="info-circle-fill"
                    class="mb-0"
                />
            </div>
        @endif
    </div>
</div>
