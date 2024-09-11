@if ($row->is_yes)
    <span class="legend-indicator bg-success"></span> Yes
@elseif ($row->is_no)
    <span class="legend-indicator bg-danger"></span> No
@else
    <span class="legend-indicator bg-light"></span> Abstain
@endif
