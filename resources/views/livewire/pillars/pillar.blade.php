<div>
    <div class="card shadow mb-4">
        <div class="card-header border-bottom">
            <span class="float-end">
                @if ($pillar->revoked_at)
                    <span class="badge bg-danger ms-1">Revoked</span>
                @else
                    <span class="badge bg-success ms-1">Active</span>
                @endif
            </span>
            <div class="text-muted fs-xs">
                Pillar
            </div>
            <h4 class="mb-0">
                {{ $pillar->name }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-24">
                    <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-2 mb-2 p-3">
                        <div class="text-start text-md-center mb-2 mb-md-0">
                            <span class="d-inline d-md-block fs-sm text-muted">Weight <i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Total ZNN delegated to pillar"></i></span>
                            <span class="fw-bold float-end float-md-none">{{ $pillar->display_weight }}</span>
                        </div>
                        <div class="text-start text-md-center mb-2 mb-md-0">
                            <span class="d-inline d-md-block fs-sm text-muted">Engagement <i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="% of Accelerator projects voted on"></i></span>
                            <span class="fw-bold float-end float-md-none">
                                @if (! is_null($pillar->az_engagement))
                                    <span class="legend-indicator bg-{{ $pillar->az_status_indicator }}"></span>
                                    {{ number_format($pillar->az_engagement) }}%
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="text-start text-md-center mb-2 mb-md-0">
                            <span class="d-inline d-md-block fs-sm text-muted">Rewards <i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Momentum / Delegation rewards %"></i></span>
                            <span class="fw-bold float-end float-md-none">{{ $pillar->give_momentum_reward_percentage }} / {{ $pillar->give_delegate_reward_percentage }}</span>
                        </div>
                        <div class="text-start text-md-center">
                            <span class="d-inline d-md-block fs-sm text-muted">Momentums <i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Produced / Expected momentums"></i></span>
                            <span class="fw-bold float-end float-md-none">
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
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-24">
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Spawned</span>
                            <span class="fw-bold">{{ $pillar->created_at->format(config('zenon.date_format')) }}</span>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm">QSR Cost</span>
                            <span class="fw-bold">{{ $pillar->display_qsr_burn }}</span>
                        </li>
                        @if ($pillar->revoked_at)
                            <li class="list-group-item">
                                <span class="d-block fs-sm">Revoked</span>
                                <span class="fw-bold">{{ $pillar->revoked_at->format(config('zenon.date_format')) }}</span>
                            </li>
                        @endif
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Owner Address</span>
                            <span class="fw-bold">
                            <x-address :account="$pillar->owner" :named="false"/>
                        </span>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Producer Address</span>
                            <span class="fw-bold">
                            <x-address :account="$pillar->producer_account" :named="false"/>
                        </span>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Rewards Address</span>
                            <span class="fw-bold">
                            <x-address :account="$pillar->withdraw_account" :named="false"/>
                        </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <div class="d-md-none">
                <select id="pillar-sections" class="form-control" wire:change="$set('tab', $event.target.value)">
                    <option value="delegators" {{ $tab === 'delegators' ? 'selected' : '' }}>Delegators</option>
                    <option value="votes" {{ $tab === 'votes' ? 'selected' : '' }}>Votes</option>
                    <option value="updates" {{ $tab === 'updates' ? 'selected' : '' }}>Updates</option>
                    <option value="messages" {{ $tab === 'messages' ? 'selected' : '' }}>Messages</option>
{{--                    <option value="reviews" {{ $tab === 'reviews' ? 'selected' : '' }}>Reviews</option>--}}
                    <option value="json" {{ $tab === 'json' ? 'selected' : '' }}>JSON</option>
                </select>
            </div>
            <div class="d-none d-md-block">
                <ul class="nav nav-tabs-alt card-header-tabs">
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'delegators' ? 'active' : '' }}" wire:click="$set('tab', 'delegators')">
                            <i class="bi bi-people-fill opacity-70 me-2"></i> Delegators
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'votes' ? 'active' : '' }}" wire:click="$set('tab', 'votes')">
                            <i class="bi bi-check-square-fill opacity-70 me-2"></i> Votes
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'updates' ? 'active' : '' }}" wire:click="$set('tab', 'updates')">
                            <i class="bi bi-cloud-arrow-up-fill opacity-70 me-2"></i> Updates
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'messages' ? 'active' : '' }}" wire:click="$set('tab', 'messages')">
                            <i class="bi bi-wifi opacity-70 me-2"></i> Messages
                        </button>
                    </li>
{{--                    <li class="nav-item">--}}
{{--                        <button class="btn nav-link {{ $tab === 'reviews' ? 'active' : '' }}" wire:click="$set('tab', 'reviews')">--}}
{{--                            <i class="bi bi-star-fill opacity-70 me-2"></i> Reviews--}}
{{--                        </button>--}}
{{--                    </li>--}}
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'json' ? 'active' : '' }}" wire:click="$set('tab', 'json')">
                            <i class="bi bi-code-slash opacity-70 me-2"></i> JSON
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active">
                @if ($tab === 'delegators')
                    <livewire:tables.pillar-delegators :pillar="$pillar" key="{{now()}}" />
                @elseif ($tab === 'votes')
                    <livewire:tables.pillar-votes :pillar="$pillar" key="{{now()}}" />
                @elseif ($tab === 'updates')
                    <livewire:tables.pillar-history :pillar="$pillar" key="{{now()}}" />
                @elseif ($tab === 'messages')
                    <livewire:tables.pillar-messages :pillar="$pillar" key="{{now()}}" />
                @elseif ($tab === 'reviews')
                @elseif ($tab === 'json')
                    <div class="p-4">
                        <pre class="line-numbers"><code class="lang-json">{{ pretty_json($pillar->raw_json) }}</code></pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
