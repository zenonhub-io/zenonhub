import {Offcanvas as BSOffcanvas} from 'bootstrap';
import Singleton from '../abstracts/Singleton';

export default class Offcanvas extends Singleton {

    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {

        //
        // Livewire offcanvas

        let livewireOffcanvasElement = document.getElementById('livewire-offcanvas');

        livewireOffcanvasElement.addEventListener('hidden.bs.offcanvas', () => {
            Livewire.dispatch('reset-livewire-offcanvas');
        });

        Livewire.on('show-livewire-offcanvas', (e)  => {
            let offcanvas = BSOffcanvas.getOrCreateInstance(livewireOffcanvasElement);
            window.zenonHub.debug('show offcanvas');
            offcanvas.show();
        });

        Livewire.on('hide-livewire-offcanvas', ({}) => {
            let offcanvas = BSOffcanvas.getInstance(livewireOffcanvasElement);
            window.zenonHub.debug('hide offcanvas');
            offcanvas.hide();
            Livewire.dispatch('reset-livewire-offcanvas');
        });

        //
        // Inline offcanvas

        Livewire.on('show-inline-offcanvas', params => {
            let offcanvasElement = document.getElementById(params.id);
            let offcanvas = BSModal.getOrCreateInstance(offcanvasElement);
            window.zenonHub.debug('show inline offcanvas');
            offcanvas.show();
        });

        Livewire.on('hide-inline-offcanvas', params => {
            let offcanvasElement = document.getElementById(params.id);
            let offcanvas = BSModal.getInstance(offcanvasElement);
            window.zenonHub.debug('hide inline offcanvas');
            offcanvas.hide();
        });

        window.zenonHub.debug('offcanvas ready');
    }
}
