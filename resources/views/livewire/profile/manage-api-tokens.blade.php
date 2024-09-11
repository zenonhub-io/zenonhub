<div>
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Manage API Tokens') }}</h4>
            <p class="text-muted">{{ __('API tokens allow your applications to access our APIs.') }}</p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <x-buttons.button class="btn btn-outline-primary" wire:click="createApiToken" wire:loading.attr="disabled">
                {{ __('Create') }} <i class="bi bi-check-lg ms-2"></i>
            </x-buttons.button>
        </div>
    </div>
    <div class="d-none d-md-block">
        <x-alerts.response class="alert-success my-6" on="profile.api-token.created">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('The API Token has been created') }}
        </x-alerts.response>
    </div>
    <hr class="my-6">
    <div class="vstack gap-6">
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Token Name')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.input name="name" id="{{ $uuid }}" wire:model="createApiTokenForm.name" />
            </div>
        </div>
    </div>
    <hr class="my-6 d-md-none">
    <div class="d-md-none">
        <x-buttons.button class="btn btn-outline-primary w-100" wire:click="createApiToken" wire:loading.attr="disabled">
            {{ __('Create') }} <i class="bi bi-check-lg ms-2"></i>
        </x-buttons.button>
    </div>

    @if ($this->user->tokens->isNotEmpty())
        <hr class="my-6">
        <div class="list-group">
            @foreach ($this->user->tokens->sortBy('name') as $token)
                <div class="list-group-item d-flex align-items-center">
                    <div class="flex-fill">
                        <span class="d-block text-sm text-heading fw-semibold">{{ $token->name }}</span>
                        @if ($token->last_used_at)
                            <div class="d-block text-xs text-muted mt-2">
                                {{ __('Last used') }}: <x-date-time.carbon :date="$token->last_used_at" class="d-inline fw-bold" />
                            </div>
                        @endif
                    </div>
                    <div class="ms-auto d-flex gap-4 align-items-center">
                        <x-buttons.button wire:click="confirmApiTokenDeletion({{ $token->id }})" class="btn-outline-danger btn-sm">
                            {{ __('Delete') }} <i class="bi bi-trash-fill ms-2"></i>
                        </x-buttons.button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modals.modal id="confirm-delete-token">
        <x-slot:heading>
            {{ __('Delete API Token') }}
        </x-slot:heading>
        {{ __('Are you sure you would like to delete this API token? This action cannot be undone.') }}
        <x-slot:footer>
            <x-buttons.button class="btn btn-neutral" data-bs-dismiss="modal">
                {{ __('Cancel') }}
            </x-buttons.button>
            <x-buttons.button class="btn btn-outline-danger" wire:click="deleteApiToken">
                {{ __('Confirm') }} <i class="bi bi-check-lg ms-2"></i>
            </x-buttons.button>
        </x-slot:footer>
    </x-modals.modal>

    <x-modals.modal id="view-api-token">
        <x-slot:heading>
            {{ __('API Token') }}
        </x-slot:heading>
        {{ __('Please copy your new API token. For your security, it won\'t be shown again.') }}

        <x-forms.inputs.textarea name="token_preview" class="form-control-lg mt-4"
                                 rows="2"
                                 autofocus autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
        >{{$plainTextToken}}</x-forms.inputs.textarea>

        <x-slot:footer>
            <x-buttons.button class="btn btn-neutral" data-bs-dismiss="modal">
                {{ __('Close') }} <i class="bi bi-x-lg ms-2"></i>
            </x-buttons.button>
        </x-slot:footer>
    </x-modals.modal>
</div>
