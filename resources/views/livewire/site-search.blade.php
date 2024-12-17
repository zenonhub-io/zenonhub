<div>
    <div class="modal-body">
        <div class="input-group input-group-lg input-group-inline rounded-pill shadow">
            <span class="input-group-text rounded-start-pill">
                <i class="bi bi-search me-2"></i>
            </span>
            <input type="search" class="form-control ps-0 rounded-end-pill"
                   placeholder="Search accounts, pillars, AZ..."
                   autofocus
                   aria-label="Search"
                   wire:model.live.debounce.250ms="search"
            >
        </div>

        @if($totalResults !== null)
            <h4 class="text-center pt-6">
                Found {{ $totalResults }} matching {{ Str::plural('item', $totalResults) }}
            </h4>
            @foreach($results as $resultGroup => $items)
                @if(count($items))
                    <h5 class="mb-3 mt-6">{{ count($items) }} {{ Str::plural(Str::headline(Str::singular($resultGroup)), count($items)) }}</h5>
                    <div class="list-group overflow-hidden shadow">
                        @foreach($items as $result)
                            <div class="list-group-item bg-body-secondary-hover px-6 overflow-x-auto">
                                <x-link :href="$result->link" :navigate="false" class="stretched-link">
                                    {{ $result->title }}

                                    @if ($result->comment)
                                        <span class="d-block mt-1 text-sm text-muted">
                                            {{ $result->comment }}
                                        </span>
                                    @endif
                                </x-link>
                            </div>
                        @endforeach
                    </div>
                @endif

            @endforeach
        @endif
    </div>
</div>
