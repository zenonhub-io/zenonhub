@if (! is_null($row->az_engagement))
    <span class="legend-indicator bg-{{ $row->az_status_indicator }}"></span>
    {{ number_format($row->az_engagement) }}%
@else
    -
@endif
