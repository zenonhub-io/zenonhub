import {Offcanvas as BSOffcanvas} from 'bootstrap';
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm'

export default class Offcanvas {

    init() {
        this.initLivewireOffcanvas();
        this.initInlineOffcanvas();
    }

    initLivewireOffcanvas() {
        let livewireOffcanvasElement = document.getElementById('livewire-offcanvas');

        livewireOffcanvasElement.addEventListener('hidden.bs.offcanvas', () => {
            Livewire.dispatch('reset-livewire-offcanvas');
        });

        Livewire.on('show-livewire-offcanvas', (e)  => {
            let offcanvas = BSOffcanvas.getOrCreateInstance(livewireOffcanvasElement);
            offcanvas.show();
        });

        Livewire.on('hide-livewire-offcanvas', ({}) => {
            let offcanvas = BSOffcanvas.getInstance(livewireOffcanvasElement);
            offcanvas.hide();
            Livewire.dispatch('reset-livewire-offcanvas');
        });
    }

    initInlineOffcanvas() {
        Livewire.on('show-inline-offcanvas', params => {
            let offcanvasElement = document.getElementById(params.id);
            let offcanvas = BSOffcanvas.getOrCreateInstance(offcanvasElement);
            offcanvas.show();
        });

        Livewire.on('hide-inline-offcanvas', params => {
            let offcanvasElement = document.getElementById(params.id);
            let offcanvas = BSOffcanvas.getInstance(offcanvasElement);
            offcanvas.hide();
        });
    }
}
