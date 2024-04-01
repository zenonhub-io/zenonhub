<div class="bg-secondary shadow rounded-2 mb-4 p-3">
    <div class="d-block d-md-flex justify-content-md-evenly mb-3">
        <div class="text-start text-md-center mb-2 mb-md-0">
            <span class="d-inline d-md-block text-muted fs-sm">ZNN</span>
            <span class="float-end float-md-none text-zenon-green">{{ $item->display_znn_requested }}</span>
        </div>
        <div class="text-start text-md-center mb-2 mb-md-0">
            <span class="d-inline d-md-block text-muted fs-sm">QSR</span>
            <span class="float-end float-md-none text-zenon-blue">{{ $item->display_qsr_requested }}</span>
        </div>
        <div class="text-start text-md-center">
            <span class="d-inline d-md-block text-muted fs-sm">USD</span>
            <span class="float-end float-md-none text-white opacity-80">{{ $item->display_usd_requested }}</span>
        </div>
    </div>
    <div class="text-start text-md-center">
        <div class="text-muted fs-sm text-center mb-2">
            {{ $item->quorum_status }}
        </div>
        <div class="progress bg-dark mb-3" style="height: 4px">
            <div
                class="progress-bar bg-success"
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
                class="progress-bar bg-secondary"
                role="progressbar"
                aria-label="Abstain"
                style="width: {{ $item->total_abstain_votes_percentage }}%"
                aria-valuenow="{{ $item->total_abstain_votes_percentage }}"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>
        </div>
        <div class="d-flex justify-content-evenly">
            <span class="badge bg-secondary text-muted">
                {{ $item->total_yes_votes }} Yes
            </span>
            <span class="badge bg-secondary text-muted">
                {{ $item->total_no_votes }} No
            </span>
            <span class="badge bg-secondary text-muted">
                {{ $item->total_abstain_votes }} Abstain
            </span>
        </div>
    </div>
</div>
