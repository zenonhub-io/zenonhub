<div>
    <div id="livewire-modal" class="modal fade" tabindex="-1"
         {{ ($static ? 'data-bs-backdrop="static"' : '') }}
         {{ ($keyboard ? 'data-bs-keyboard="false"' : '') }}
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

