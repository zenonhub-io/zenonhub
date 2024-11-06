import {Modal as BSModal} from 'bootstrap';
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm'

export default class Modals {

    init() {
        this.initLivewireModals();
        this.initInlineModals();
    }

    initLivewireModals() {
        let livewireModalElement = document.getElementById('livewire-modal');

        livewireModalElement.addEventListener('hidden.bs.modal', (event) => {
            Livewire.dispatch('reset-livewire-modal');
        });

        Livewire.on('show-livewire-modal', (e) => {
            let modal = BSModal.getOrCreateInstance(livewireModalElement);
            modal.show();
        });

        Livewire.on('hide-livewire-modal', ({}) => {
            let modal = BSModal.getInstance(livewireModalElement);
            modal.hide();
            Livewire.dispatch('reset-livewire-modal');
        });
    }

    initInlineModals() {
        Livewire.on('show-inline-modal', params => {
            let modalElement = document.getElementById(params.id);
            let modal = BSModal.getOrCreateInstance(modalElement);
            modal.show();
        });

        Livewire.on('hide-inline-modal', params => {
            let modalElement = document.getElementById(params.id);
            let modal = BSModal.getInstance(modalElement);
            modal.hide();
        });
    }
}
