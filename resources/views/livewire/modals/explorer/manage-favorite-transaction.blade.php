<div class="modal-dialog">
    <div class="modal-content">
        <form wire:submit.prevent="onAddFavorite" class="needs-validation">
            <input type="hidden" name="hash" wire:model="hash">
            <div class="modal-header">
                <h5 class="modal-title">Manage transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label for="form-email" class="form-label">Private note</label>
                    <textarea
                        id="form-post"
                        name="notes"
                        class="form-control @error('notes')is-invalid @enderror"
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
                        wire:click="onDeleteFavorite('{{ $hash }}')"
                    ><i class="bi bi-trash me-2"></i> Delete</button>
                @endif
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-check-circle me-2"></i> Save</button>
            </div>
        </form>
    </div>
</div>
