<x-stats.indicator type="{{ $pillar->status_colour }}" data-bs-toggle="tooltip" data-bs-title="{{ $pillar->status_tooltip }}" />
@if (! $pillar->revoked_at)
    {{ $pillar->produced_momentums }} / {{ $pillar->expected_momentums }}
@else
    0 / 0
@endif
