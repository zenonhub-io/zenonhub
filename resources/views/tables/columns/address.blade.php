@if ($row instanceof \App\Models\Nom\Account)
    <x-address :account="$row" />
@else
    <x-address :account="load_account($row->address)" />
@endif
