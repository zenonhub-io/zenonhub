@props([
    'alwaysShort' => false,
    'linked' => true,
    'breakpoint' => 'md',
    'named' => true,
])

@if ($row instanceof \App\Models\Nom\Account)
    <x-address :account="$row" :always-short="$alwaysShort" :linked="$linked" :breakpoint="$breakpoint" :named="$named" />
@else
    <x-address :account="load_account($row->address)" :always-short="$alwaysShort" :linked="$linked" :breakpoint="$breakpoint" :named="$named" />
@endif
