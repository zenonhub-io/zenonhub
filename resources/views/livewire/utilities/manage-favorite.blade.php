<div class="m-n6">

    <x-modals.heading :title="__('Manage Favorite: ').$title" />

    <div class="p-6">
        <div class="vstack gap-4">
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('Label')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.input name="label" id="{{ $uuid }}" wire:model="favoriteForm.label"/>
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('Notes')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.textarea name="notes" id="{{ $uuid }}" wire:model="favoriteForm.notes" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        @if ($hasUserFavorite)
            <x-buttons.button class="btn btn-outline-danger me-auto" wire:click="deleteFavorite">
                {{ __('Delete') }} <i class="bi bi-trash ms-2"></i>
            </x-buttons.button>
        @endif
        <x-buttons.button class="btn btn-neutral" data-bs-dismiss="modal">
            {{ __('Cancel') }}
        </x-buttons.button>
        <x-buttons.button class="btn btn-outline-success" wire:click="saveFavorite">
            {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
        </x-buttons.button>
    </div>
</div>
