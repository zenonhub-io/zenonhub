<div>
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Delete Account') }}</h4>
            <p class="text-muted">{{ __('Permanently delete your account and all associated data.') }}</p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <x-utilities.confirm-password wire:then="deleteUser" class="btn-outline-danger" wire:loading.attr="disabled">
                {{ __('Delete') }} <i class="bi bi-trash-fill ms-2"></i>
            </x-utilities.confirm-password>
        </div>
    </div>
    <hr class="my-6 d-md-none">
    <div class="d-md-none">
        <x-utilities.confirm-password wire:then="deleteUser" class="btn-outline-danger w-100" wire:loading.attr="disabled">
            {{ __('Delete') }} <i class="bi bi-trash-fill ms-2"></i>
        </x-utilities.confirm-password>
    </div>
</div>
