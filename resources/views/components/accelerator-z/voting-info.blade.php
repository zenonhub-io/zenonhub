@props(['item'])

<div class="text-start text-md-center">
    <div class="text-center text-muted mb-2">
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
