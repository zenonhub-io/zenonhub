<div wire:init="shouldLoadResults">
    @if ($data && $data->count())
        <div class="table-responsive">
            <table class="table table-nowrap align-middle table-striped table-hover">
                <thead>
                <tr>
                    <th>
                        <button type="button" class="btn btn-sort" wire:click="sortBy('pillar')">
                            <x-table-sort-button :sort="$sort" :order="$order" check="pillar"/>
                        </button>
                    </th>
                    <th>
                        Vote
                    </th>
                    <th>
                        <button type="button" class="btn btn-sort" wire:click="sortBy('created_at')">
                            <x-table-sort-button :sort="$sort" :order="$order" check="created_at" title="Timestamp"/>
                        </button>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $vote)
                    <tr>
                        <td>
                            <a href="{{ route('pillars.detail', ['slug' => $vote->owner->pillar->slug]) }}">
                                {{ $vote->owner->pillar->name }}
                            </a>
                        </td>
                        <td>
                            @if ($vote->is_yes)
                                <span class="legend-indicator bg-success"></span> Yes
                            @elseif ($vote->is_no)
                                <span class="legend-indicator bg-danger"></span> No
                            @else
                                <span class="legend-indicator bg-secondary"></span> Abstain
                            @endif
                        </td>
                        <td>{{ $vote->created_at->format(config('zenon.short_date_format')) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="m-4 mt-2">
            {{ $data->links() }}
        </div>
    @else
        <x-alert
            message="No votes submitted"
            type="info"
            icon="info-circle-fill"
            class="m-4"
        />
    @endif
</div>
