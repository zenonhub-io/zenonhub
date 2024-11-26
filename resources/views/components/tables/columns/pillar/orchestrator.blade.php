@if($row->orchestrator)
    <span class="legend-indicator bg-{{ ($row->orchestrator->is_active ? 'success' : 'danger') }}"></span> {{ ($row->orchestrator->is_active ? 'Online' : 'Offline') }}
@else
    -
@endif
