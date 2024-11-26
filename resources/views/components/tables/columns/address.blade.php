@props([
    'alwaysShort' => false,
    'linked' => true,
    'breakpoint' => 'md',
])

@if ($row instanceof \App\Models\Nom\Account)
    <x-address :account="$row" :always-short="$alwaysShort" :linked="$linked" :breakpoint="$breakpoint" />
@else
    <x-address :account="load_account($row->address)" :always-short="$alwaysShort" :linked="$linked" :breakpoint="$breakpoint" />
@endif
