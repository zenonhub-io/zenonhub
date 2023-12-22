<div>
    <div class="card shadow">
        <div class="card-header border-bottom">
            <div class="d-block d-sm-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-0">
                        Momentums
                    </h4>
                    <div class="text-muted fs-sm mt-1 mb-3 mb-sm-0">
                        Showing the latest 50k results
                    </div>
                </div>
                <div class="d-block d-md-flex justify-content-md-end">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>
        </div>
        <div class="w-100" wire:loading.delay>
            <div class="card-body">
                <div class="row">
                    <div class="col-24 col-sm-8 offset-sm-8">
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
                            Height
                        </th>
                        <th>
                            Hash
                        </th>
                        <th>
                            Age
                        </th>
                        <th>
                            Producer
                        </th>
                        <th>
                            Transactions
                        </th>
                        <th>
                            Created
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $momentum)
                        <tr>
                            <td>{{ $momentum->display_height }}</td>
                            <td>
                                <a href="{{ route('explorer.momentum', ['hash' => $momentum->hash]) }}">
                                    <x-hash-tooltip :hash="$momentum->hash" :eitherSide="8" :alwaysShort="true"/>
                                </a>
                            </td>
                            <td>{{ $momentum->created_at->diffForHumans(['parts' => 2]) }}</td>
                            <td>
                                @if ($momentum->producer_pillar)
                                    <a href="{{ route('pillars.detail', ['slug' => $momentum->producer_pillar->slug]) }}">
                                        {{ $momentum->producer_pillar->name }}
                                    </a>
                                @else
                                    <x-address :account="$momentum->producer_account"/>
                                @endif
                            </td>
                            <td>{{ $momentum->account_blocks_count }}</td>
                            <td>{{ $momentum->created_at->format(config('zenon.short_date_format')) }}</td>
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
