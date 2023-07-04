import {Modal as BSModal} from 'bootstrap';
import Singleton from '../abstracts/Singleton';

export default class Modal extends Singleton {

    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {
        window.zenonHub.debug('modals ready');

        let modalsElement = document.getElementById('laravel-livewire-modals');

        modalsElement.addEventListener('hidden.bs.modal', () => {
            Livewire.emit('resetModal');
        });

        Livewire.on('showBootstrapModal', () => {
            let modal = BSModal.getInstance(modalsElement);

            if (!modal) {
                modal = new BSModal(modalsElement);
            }

            window.zenonHub.debug('show modal');
            modal.show();
        });

        Livewire.on('hideModal', () => {
            let modal = BSModal.getInstance(modalsElement);

            window.zenonHub.debug('hide modal');
            modal.hide();
        });
    }
}
