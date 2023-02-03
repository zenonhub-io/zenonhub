<div>
    <div class="card shadow mb-4">
        <div class="card-header border-0">
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
                                <a class="nav-link {{ $list === 'new' ? 'active' : '' }}"
                                   wire:click="setList('new')"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >New</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $list === 'accepted' ? 'active' : '' }}"
                                   wire:click="setList('accepted')"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >Accepted</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $list === 'complete' ? 'active' : '' }}"
                                   wire:click="setList('complete')"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >Complete</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $list === 'rejected' ? 'active' : '' }}"
                                   wire:click="setList('rejected')"
                                   href="javascript:;"
                                   data-bs-toggle="tab"
                                   role="tab"
                                >Rejected</a>
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
                    <label for="projects.filters.search" class="visually-hidden form-label">Search projects</label>
                    <div class="input-group input-group-merge">
                    <span class="input-group-prepend input-group-text">
                        <i class="bi-search"></i>
                    </span>
                        <input
                            type="text"
                            class="form-control"
                            id="projects.filters.search"
                            placeholder="Search projects"
                            aria-label="Search projects"
                            wire:model.debounce.400ms="search"
                            value="{{ $search }}"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($projects as $project)
            <div class="col-24 col-md-12 mb-4">
                <div class="card shadow h-100 card-hover">
                    <div class="card-header border-bottom">
                        <div class="d-flex mb-0">
                            <h4 class="card-title me-auto mb-0">
                                <a href="{{ route('az.project', ['hash' => $project->hash]) }}">
                                    {{ $project->name }}
                                </a>
                            </h4>
                            <div class="ps-3">
                                <a href="{{ $project->url }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i></a>
                            </div>
                            <div class="ps-3">
                                {!! $project->display_badge !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-2 mb-4 p-3">
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block text-muted">ZNN</span>
                                <span class="fw-bold float-end float-md-none text-zenon-green">{{ $project->display_znn_funds_needed }}</span>
                            </div>
                            <div class="text-start text-md-center">
                                <span class="d-inline d-md-block text-muted">QSR</span>
                                <span class="fw-bold float-end float-md-none text-zenon-blue">{{ $project->display_qsr_funds_needed }}</span>
                            </div>
                        </div>
                        <p>
                            {{ $project->description }}
                        </p>
                    </div>
                    @if ($project->phases->count())
                        <div class="accordion mx-3 mb-4" id="phases-{{ $project->hash }}">
                            @foreach ($project->phases->reverse() as $phase)
                                <div class="accordion-item">
                                    <div class="accordion-header" id="phase-heading-{{ $phase->hash }}">
                                        <a class="accordion-button collapsed shadow-none rounded-3" role="button" data-bs-toggle="collapse" data-bs-target="#phase-collapse-{{ $phase->hash }}" aria-expanded="false" aria-controls="phase-collapse-{{ $phase->hash }}">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">{!! $phase->display_badge !!}</div>
                                                {{ $phase->name }}
                                            </div>
                                        </a>
                                    </div>
                                    <div id="phase-collapse-{{ $phase->hash }}" class="accordion-collapse collapse" aria-labelledby="phase-heading-{{ $phase->hash }}" data-bs-parent="#phases-{{ $project->hash }}">
                                        <div class="accordion-body">
                                            <div class="d-flex mb-2">
                                                <div class="me-auto">
                                                    ZNN: <span class="fw-bold text-zenon-green">{{ $phase->display_znn_funds_needed }}</span> | QSR: <span class="fw-bold text-zenon-blue">{{ $phase->display_qsr_funds_needed }}</span>
                                                </div>
                                                <div class="ps-3">
                                                    <a href="{{ $phase->url }}"><i class="bi bi-box-arrow-up-right"></i></a>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                Created: {{ $phase->created_at->format(config('zenon.short_date_format')) }}
                                            </div>
                                            <p>
                                                {{ $phase->description }}
                                            </p>
                                            <div class="d-flex justify-content-evenly mb-4">
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
                                                    class="progress-bar bg-secondary"
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
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-alert
                            message="No phases submitted"
                            type="info"
                            icon="info-circle-fill"
                            class="mx-4 mb-4"
                        />
                    @endif
                    <div class="card-footer">
                        <div class="d-flex justify-content-evenly mb-4">
                            <span class="badge bg-secondary">
                                {{ $project->total_yes_votes }} Yes
                            </span>
                            <span class="badge bg-secondary">
                                {{ $project->total_no_votes }} No
                            </span>
                            <span class="badge bg-secondary">
                                {{ $project->total_abstain_votes }} Abstain
                            </span>
                        </div>
                        <div class="progress bg-dark mb-3" style="height: 4px">
                            <div
                                class="progress-bar bg-success"
                                role="progressbar"
                                aria-label="Yes"
                                style="width: {{ $project->total_yes_votes_percentage }}%"
                                aria-valuenow="{{ $project->total_yes_votes_percentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                            <div
                                class="progress-bar bg-danger"
                                role="progressbar"
                                aria-label="No"
                                style="width: {{ $project->total_no_votes_percentage }}%"
                                aria-valuenow="{{ $project->total_no_votes_percentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                            <div
                                class="progress-bar bg-secondary"
                                role="progressbar"
                                aria-label="Abstain"
                                style="width: {{ $project->total_abstain_votes_percentage }}%"
                                aria-valuenow="{{ $project->total_abstain_votes_percentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                        <div class="text-muted text-center">
                            {{ $project->quorum_stauts }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="card shadow">
        <div class="card-footer border-0">
            {{ $projects->onEachSide(1)->links() }}
        </div>
    </div>
</div>
