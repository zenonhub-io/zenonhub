<div>
    <div id="livewire-modal" class="modal fade" tabindex="-1"
         data-bs-backdrop="static" data-bs-keyboard="false"
         wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                @if ($alias)
                    @livewire($alias, $params, key($activeModal))
                @endif
            </div>
        </div>
    </div>
</div>

