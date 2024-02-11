<div wire:init="shouldLoadResults">
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
                <table class="table table-nowrap align-middle table-striped table-hover">
                    <thead>
                    <tr>
                        <th>
                            Momentum
                        </th>
                        <th>
                            Notes
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $favourite)
                        <tr>
                            <td>
                                <a href="{{ route('explorer.momentum', ['hash' => $favourite->markable->hash]) }}">
                                    <x-hash-tooltip :hash="$favourite->markable->hash" :eitherSide="8" :alwaysShort="true"/>
                                </a>
                            </td>
                            <td>
                                {{ $favourite->notes }}
                            </td>
                            <th>
                                <i
                                    class="bi bi-pencil hover-text"
                                    wire:click="$emit('showModal', 'modals.explorer.manage-favorite-momentum', '{{ $favourite->markable->hash }}')"
                                ></i>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="m-4 mt-2">
                {{ $data->links() }}
            </div>
        @elseif($data)
            <div class="m-4 mt-4">
                <x-alert
                    message="No favorites found"
                    type="info"
                    icon="info-circle-fill"
                    class="mb-0"
                />
            </div>
        @endif
    </div>
</div>
