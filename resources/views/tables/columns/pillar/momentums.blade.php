@if (! $row->revoked_at)
    @if ($row->is_producing)
        <span class="legend-indicator bg-success" data-bs-toggle="tooltip" data-bs-title="Producing momentums"></span>
    @else
        <span class="legend-indicator bg-danger" data-bs-toggle="tooltip" data-bs-title="Possible production issues"></span>
    @endif
    {{ $row->produced_momentums }} / {{ $row->expected_momentums }}
@else
    <span class="legend-indicator bg-danger"></span>
    0 / 0
@endif
