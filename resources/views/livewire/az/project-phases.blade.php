<div>
    @if ($data && $data->count())
        @foreach ($data as $phase)
            <div class="mb-4 card shadow card-hover {{ ($data->last()->id !== $phase->id ? 'mb-4' : '') }}" id="phase-{{ $phase->hash }}">
                <div class="card-header border-bottom">
                    <span class="float-end">
                        {!! $phase->display_badge !!}
                    </span>
                    <div class="text-muted fs-xs">
                        Phase {{ $phase->phase_number }}
                    </div>
                    <h5 class="mb-0">
                        <a href="{{ route('az.phase', ['hash' => $phase->hash]) }}">
                            {{ $phase->name }}
                        </a>
                    </h5>
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
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="mt-4">
            {{ $data->links() }}
        </div>
    @endif
</div>
