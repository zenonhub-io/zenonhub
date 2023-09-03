<div>
    <div class="table-responsive p-n3">
        <table class="table table-nowrap align-middle table-striped table-hover">
            <thead>
            <tr>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('nom_pillars.name')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="nom_pillars.name" title="Name"/>
                    </button>
                </th>
                <th>
                    Account
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('status')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="status" title="Status"/>
                    </button>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data as $orchestrator)
                <tr>
                    <td>
                        {{ $orchestrator->pillar->name }}
                    </td>
                    <td>
                        <x-address :account="$orchestrator->account" :eitherSide="8" :alwaysShort="true" :named="false"/>
                    </td>
                    <td>
                        <span class="legend-indicator bg-{{ ($orchestrator->status ? 'success' : 'danger') }}" data-bs-toggle="tooltip" data-bs-title="{{ ($orchestrator->status ? 'Online' : 'Offline') }}"></span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="m-4 mt-2">
        {{ $data->links() }}
    </div>
</div>
