<div>
    <div class="card shadow">
        <div class="card-header">
            <div class="row">
                <div class="col-24 col-md-16 mb-3 mb-md-0">
                    <div wire:loading.remove>
                        <ul class="nav nav-tabs card-header-tabs d-flex flex-nowrap overflow-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ $list === 'all' ? 'active' : '' }}"
                                   wire:click="setList('all')"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $list === 'active' ? 'active' : '' }}"
                                   wire:click="setList('active')"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >Active</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $list === 'inactive' ? 'active' : '' }}"
                                   wire:click="setList('inactive')"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >Inactive</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $list === 'revoked' ? 'active' : '' }}"
                                   href="javascript:;"
                                   wire:click="setList('revoked')"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >Revoked</a>
                            </li>
                        </ul>
                    </div>
                    <div wire:loading>
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link disabled"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                ><i class="bi-arrow-repeat spin mx-2"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-24 col-md-8 mb-0 mb-md-0">
                    <label for="pillars.filters.search" class="visually-hidden form-label">Search pillars</label>
                    <div class="input-group input-group-merge">
                    <span class="input-group-prepend input-group-text">
                        <i class="bi-search"></i>
                    </span>
                        <input
                            type="text"
                            class="form-control"
                            id="pillars.filters.search"
                            placeholder="Search pillars"
                            aria-label="Search pillars"
                            wire:model.debounce.400ms="search"
                            value="{{ $search }}"
                        >
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-nowrap align-middle table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('name')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="name"/>
                            </button>
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('weight')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="weight" title="Weight" tooltip="Total ZNN delegated to pillar"/>
                            </button>
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('az_engagement')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="az_engagement" title="Engagement" tooltip="% of Accelerator projects voted on"/>
                            </button>
                        </th>
                        <th>
                            Rewards <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Momentum / Delegation rewards %"></i></span>
                        </th>
                        <th>
                            Momentums <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Produced / Expected momentums"></i></span>
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('delegators_count')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="delegators_count" title="Delegators" tooltip="Total number of delegators"/>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($pillars as $pillar)
                    <tr>
                        <td>
                            <a href="{{ route('pillars.detail', ['slug' => $pillar->slug]) }}">
                                {{ $pillar->name }}
                            </a>
                        </td>
                        <td>{{ $pillar->display_weight }}</td>
                        <td>
                            @if (! is_null($pillar->az_engagement))
                                <span class="legend-indicator bg-{{ $pillar->az_status_indicator }}"></span>
                                {{ number_format($pillar->az_engagement) }}%
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $pillar->give_momentum_reward_percentage }} / {{ $pillar->give_delegate_reward_percentage }}</td>
                        <td>
                            @if (! $pillar->is_revoked)
                                @if ($pillar->is_producing)
                                    <span class="legend-indicator bg-success" data-bs-toggle="tooltip" data-bs-title="Producing momentums"></span>
                                @else
                                    <span class="legend-indicator bg-danger" data-bs-toggle="tooltip" data-bs-title="Possible production issues"></span>
                                @endif
                                {{ $pillar->produced_momentums }} / {{ $pillar->expected_momentums }}
                            @else
                                <span class="legend-indicator bg-danger"></span>
                                0 / 0
                            @endif
                        </td>
                        <td>
                            {{ $pillar->active_delegators_count }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer border-0 pt-0">
            {{ $pillars->onEachSide(1)->links() }}
        </div>
    </div>
</div>
