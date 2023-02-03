<div>
    <div class="card shadow mb-4">
        <div class="card-header border-bottom">
        <span class="float-end">
            {!! $phase->display_badge !!}
        </span>
            <div class="text-muted fs-xs">
                Phase {{ $phase->phase_number }}
            </div>
            <h4 class="mb-0">
                {{ $phase->name }}
            </h4>
        </div>
        <div class="card-body mb-0">
            <div class="row">
                <div class="col-24">
                    <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-2 mb-2 p-3">
                        <div class="text-start text-md-center mb-2 mb-md-0">
                            <span class="d-inline d-md-block fs-sm text-muted">ZNN</span>
                            <span class="fw-bold float-end float-md-none text-zenon-green">{{ $phase->display_znn_funds_needed }}</span>
                        </div>
                        <div class="text-start text-md-center">
                            <span class="d-inline d-md-block fs-sm text-muted">QSR</span>
                            <span class="fw-bold float-end float-md-none text-zenon-blue">{{ $phase->display_qsr_funds_needed }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-24">
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item">
                            {{ $phase->description }}
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Link</span>
                            <span class="fw-bold">
                                <a href="{{ $phase->url }}" target="_blank">{{ $phase->url }}</a>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Project</span>
                            <span class="fw-bold">
                                <a href="{{ route('az.project', ['hash' => $phase->project->hash]) }}">
                                    {{ $phase->project->name }}
                                </a>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Owner</span>
                            <span class="fw-bold">
                                <x-address :account="$phase->project->owner"/>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm">Created</span>
                            <span class="fw-bold">
                                {{ $phase->created_at->format(config('zenon.date_format')) }}
                            </span>
                        </li>
                        <li class="list-group-item pb-0">
                            <div class="d-flex justify-content-evenly mt-1 mb-3">
                            <span class="badge bg-secondary">
                                {{ $phase->total_yes_votes }} Yes
                            </span>
                                <span class="badge bg-secondary">
                                {{ $phase->total_no_votes }} No
                            </span>
                                <span class="badge bg-secondary">
                                {{ $phase->total_abstain_votes }} Abstain
                            </span>
                            </div>
                            <div class="progress bg-dark mb-3" style="height: 4px">
                                <div
                                    class="progress-bar bg-success"
                                    role="progressbar"
                                    aria-label="Yes"
                                    style="width: {{ $phase->total_yes_votes_percentage }}%"
                                    aria-valuenow="{{ $phase->total_yes_votes_percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                ></div>
                                <div
                                    class="progress-bar bg-danger"
                                    role="progressbar"
                                    aria-label="No"
                                    style="width: {{ $phase->total_no_votes_percentage }}%"
                                    aria-valuenow="{{ $phase->total_no_votes_percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                ></div>
                                <div
                                    class="progress-bar bg-light"
                                    role="progressbar"
                                    aria-label="Abstain"
                                    style="width: {{ $phase->total_abstain_votes_percentage }}%"
                                    aria-valuenow="{{ $phase->total_abstain_votes_percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                ></div>
                            </div>
                            <div class="text-muted text-center">
                                {{ $phase->quorum_stauts }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <div class="d-md-none">
                <select id="phase-sections" class="form-control" wire:change="$set('tab', $event.target.value)">
                    <option value="votes" {{ $tab === 'votes' ? 'selected' : '' }}>Votes</option>
                    <option value="json" {{ $tab === 'json' ? 'selected' : '' }}>JSON</option>
                </select>
            </div>
            <div class="d-none d-md-block">
                <ul class="nav nav-tabs-alt card-header-tabs">
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'votes' ? 'active' : '' }}" wire:click="$set('tab', 'votes')">
                            <i class="bi bi-check-square-fill opacity-70 me-2"></i> Votes
                        </button>
                    </li>
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
                @if ($tab === 'votes')
                    <livewire:tables.phase-votes :phase="$phase" />
                @elseif ($tab === 'json')
                    <div class="p-4">
                        <pre class="line-numbers"><code class="lang-json">{{ pretty_json($phase->raw_json) }}</code></pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
