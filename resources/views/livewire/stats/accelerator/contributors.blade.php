<div>
    <div class="table-responsive p-n3">
        <table class="table table-nowrap align-middle table-striped table-hover">
            <thead>
            <tr>
                <th>
                    Account
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('znn_paid')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="znn_paid" title="ZNN Paid"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('qsr_paid')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="znn_paid" title="QSR Paid"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('completed_projects_count')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="completed_projects_count" title="Completed"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('accepted_projects_count')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="accepted_projects_count" title="Accepted"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('rejected_projects_count')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="rejected_projects_count" title="Rejected"/>
                    </button>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data as $contributor)
                <tr>
                    <td>
                        <x-address :account="$contributor" :eitherSide="8" :alwaysShort="true"/>
                    </td>
                    <td>
                        {{ znn_token()->getDisplayAmount($contributor->znn_paid) }}
                    </td>
                    <td>
                        {{ qsr_token()->getDisplayAmount($contributor->qsr_paid) }}
                    </td>
                    <td>
                        {{ number_format($contributor->completed_projects_count) }}
                    </td>
                    <td>
                        {{ number_format($contributor->accepted_projects_count) }}
                    </td>
                    <td>
                        {{ number_format($contributor->rejected_projects_count) }}
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
