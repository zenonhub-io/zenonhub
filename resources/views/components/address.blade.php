@props([
    'account',
    'linked' => true,
    'named' => true,
    'alwaysShort' => false,
    'eitherSide' => 10,
    'breakpoint' => 'md',
    'copyable' => false
])

@if ($linked)
    <x-link :href="route('explorer.account.detail', ['address' => $account->address])">
@endif

    @if ($named && $account->has_custom_label)
        <span data-bs-toggle="tooltip" data-bs-title="{{ $account->address }}">
            {{ $account->custom_label }}
        </span>
    @else
        @if ($alwaysShort)
            <span data-bs-toggle="tooltip" data-bs-title="{{ $account->address }}">
                {{ short_hash($account->address, $eitherSide) }}
            </span>
        @else
            <span class="d-none d-{{ $breakpoint }}-inline">
                {{ $account->address }}
            </span>
            <span class="d-inline d-{{ $breakpoint }}-none" data-bs-toggle="tooltip" data-bs-title="{{ $account->address }}">
                {{ short_hash($account->address, $eitherSide) }}
            </span>
        @endif
    @endif

@if ($linked)
    </x-link>
@endif

@if($copyable)
    <x-copy class="ms-1" :text="$account->address" />
@endif
