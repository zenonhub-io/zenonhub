import {Modal as BSModal} from 'bootstrap';
import Singleton from '../abstracts/Singleton';

export default class Modal extends Singleton {

    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {

        //
        // Livewire modal

        let livewireModalElement = document.getElementById('livewire-modal');

        livewireModalElement.addEventListener('hidden.bs.modal', (event) => {
            Livewire.dispatch('reset-livewire-modal');
        });

        Livewire.on('show-livewire-modal', (e) => {
            let modal = BSModal.getOrCreateInstance(livewireModalElement);
            window.zenonHub.debug('show modal');
            modal.show();
        });

        Livewire.on('hide-livewire-modal', ({}) => {
            let modal = BSModal.getInstance(livewireModalElement);
            window.zenonHub.debug('hide modal');
            modal.hide();
            Livewire.dispatch('reset-livewire-modal');
        });

        //
        // Inline modal

        Livewire.on('show-inline-modal', params => {
            let modalElement = document.getElementById(params.id);
            let modal = BSModal.getOrCreateInstance(modalElement);
            window.zenonHub.debug('show inline modal');
            modal.show();
        });

        Livewire.on('hide-inline-modal', params => {
            let modalElement = document.getElementById(params.id);
            let modal = BSModal.getInstance(modalElement);
            window.zenonHub.debug('hide inline modal');
            modal.hide();
        });

        window.zenonHub.debug('modals ready');
    }
}
