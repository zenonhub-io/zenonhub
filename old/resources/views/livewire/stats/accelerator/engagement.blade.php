<div>
    <div class="table-responsive p-n3">
        <table class="table table-nowrap align-middle table-striped table-hover">
            <thead>
            <tr>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('name')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="name" title="Pillar"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('az_engagement')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="az_engagement" title="Engagement" tooltip="% of Accelerator projects and phases voted on"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('az_avg_vote_time')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="az_avg_vote_time" title="Avg vote time"/>
                    </button>
                </th>
                <th>
                    Votes
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data as $pillar)
                <tr>
                    <td>
                        <a href="{{ route('pillars.detail', ['slug' => $pillar->slug]) }}">
                            {{ $pillar->name }}
                        </a>
                    </td>
                    <td>
                        {{ number_format($pillar->az_engagement) }}%
                    </td>
                    <td>
                        {{ $pillar->display_az_avg_vote_time }}<br>
                    </td>
                    <td>
                        {{ number_format($pillar->azVotes()->count()) }}
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
