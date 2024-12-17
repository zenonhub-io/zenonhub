@if ($row->vote === \App\Enums\Nom\VoteEnum::YES)
    <span class="legend-indicator bg-success"></span> Yes
@elseif ($row->vote === \App\Enums\Nom\VoteEnum::NO)
    <span class="legend-indicator bg-danger"></span> No
@else
    <span class="legend-indicator bg-light"></span> Abstain
@endif
