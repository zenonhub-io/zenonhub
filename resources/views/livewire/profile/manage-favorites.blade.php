<div>
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Favorite Addresses') }}</h4>
            <p class="text-muted">{{ __('Save a list of favorite addresses for easy access') }}</p>
        </div>
    </div>

    @if ($favoritesAccounts)
        <hr class="my-6">
        <div class="list-group shadow">
            @foreach ($favoritesAccounts as $account)
                <div class="list-group-item d-flex align-items-center">
                    <div class="flex-fill">
                        <span class="d-block text-sm text-heading fw-semibold">{{ $account->custom_label }}</span>
                        <span class="d-block text-sm text-heading fw-semibold"><x-address :account="$account" :named="false" /></span>
                        <div class="d-block text-xs text-muted mt-2">
                            {{ __('Last active at') }}: @if($account->last_active_at)
                                <x-date-time.carbon :date="$account->last_active_at" class="d-inline fw-bold" />
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="ms-auto d-flex gap-4 align-items-center">
                        <x-buttons.button class="btn-outline-info btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#edit-favorite-address-{{ $account->address }}">
                            {{ __('Edit') }} <i class="bi bi-pencil-square ms-2"></i>
                        </x-buttons.button>
                    </div>
                    <x-modals.modal id="edit-favorite-address-{{ $account->address }}">
                        <livewire:utilities.manage-favorite item-type="address" :item-id="$account->address" :title="$account->address" />
                    </x-modals.modal>
                </div>
            @endforeach
        </div>
    @endif
</div>
