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
                                >Completed</a>
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
                        <a href="{{ route('az.project', ['hash' => $project->hash]) }}">
                            <x-az-card-header :item="$project"/>
                        </a>
                        {{ $project->description }}
                    </div>
                    @if ($project->phases->count())
                        <div class="accordion mx-4 mb-4" id="phases-{{ $project->hash }}">
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
                                            <a href="{{ route('az.phase', ['hash' => $phase->hash]) }}">
                                                <x-az-card-header :item="$phase"/>
                                            </a>
                                            <p>
                                                {{ $phase->description }}
                                            </p>
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
