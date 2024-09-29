@props(['item'])

<div class="bg-dark-subtle shadow rounded-2 mb-4 p-3">
    <div class="d-block d-md-flex justify-content-md-evenly mb-4">
        <div class="text-start text-md-center mb-2 mb-md-0">
            <span class="d-inline d-md-block text-muted text-sm">ZNN</span>
            <span class="float-end float-md-none text-primary">{{ $item->display_znn_requested }}</span>
        </div>
        <div class="text-start text-md-center mb-2 mb-md-0">
            <span class="d-inline d-md-block text-muted text-sm">QSR</span>
            <span class="float-end float-md-none text-secondary">{{ $item->display_qsr_requested }}</span>
        </div>
        <div class="text-start text-md-center">
            <span class="d-inline d-md-block text-muted text-sm">USD</span>
            <span class="float-end float-md-none text-white opacity-80">{{ $item->display_usd_requested }}</span>
        </div>
    </div>
    <div class="text-start text-md-center">
        <div class="text-muted text-center mb-2 text-sm">
            {{ $item->quorum_status }}
        </div>
        <div class="progress bg-dark mb-3" style="height: 4px">
            <div
                class="progress-bar bg-faded-success"
                role="progressbar"
                aria-label="Yes"
                style="width: {{ $item->total_yes_votes_percentage }}%"
                aria-valuenow="{{ $item->total_yes_votes_percentage }}"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>
            <div
                class="progress-bar bg-danger"
                role="progressbar"
                aria-label="No"
                style="width: {{ $item->total_no_votes_percentage }}%"
                aria-valuenow="{{ $item->total_no_votes_percentage }}"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>
            <div
                class="progress-bar bg-light-subtle"
                role="progressbar"
                aria-label="Abstain"
                style="width: {{ $item->total_abstain_votes_percentage }}%"
                aria-valuenow="{{ $item->total_abstain_votes_percentage }}"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>
        </div>
        <div class="d-flex justify-content-evenly">
            <span class="badge bg-light-subtle text-muted">
                {{ $item->total_yes_votes }} Yes
            </span>
            <span class="badge bg-light-subtle text-muted">
                {{ $item->total_no_votes }} No
            </span>
            <span class="badge bg-light-subtle text-muted">
                {{ $item->total_abstain_votes }} Abstain
            </span>
        </div>
    </div>
</div>
