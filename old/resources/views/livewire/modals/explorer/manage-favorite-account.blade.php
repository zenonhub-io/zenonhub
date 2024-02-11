<div class="modal-dialog">
    <div class="modal-content">
        <form wire:submit.prevent="onAddFavorite" class="needs-validation">
            <input type="hidden" name="address" wire:model="address">
            <div class="modal-header">
                <h5 class="modal-title">Manage address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label for="form-label" class="form-label">Custom label</label>
                    <input
                        type="text"
                        id="form-label"
                        name="label"
                        class="form-control @error('label')is-invalid @enderror"
                        wire:model.defer="label"
                    >
                    <div class="invalid-feedback">
                        @error('label') {{ $message }} @enderror
                    </div>
                    <div class="form-text">Labels are used throughout the explorer instead of the address</div>
                </div>
                <div class="mb-0">
                    <label for="form-notes" class="form-label">Private note</label>
                    <textarea
                        id="form-notes"
                        name="notes"
                        class="form-control @error('notes')is-invalid @enderror"
                        rows="4"
                        wire:model.defer="notes"
                    ></textarea>
                    <div class="invalid-feedback">
                        @error('notes') {{ $message }} @enderror
                    </div>
                    <div class="form-text">Notes are encrypted and only visible to you</div>
                </div>
            </div>
            <div class="modal-footer">
                @if ($exists)
                    <button
                        type="button"
                        class="btn btn-outline-danger me-auto"
                        wire:click="onDeleteFavorite('{{ $address }}')"
                    ><i class="bi bi-trash me-2"></i> Delete</button>
                @endif
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-check-circle me-2"></i> Save</button>
            </div>
        </form>
    </div>
</div>
