import './bootstrap'
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm'
import * as bootstrap from 'bootstrap';
import {docReady} from "./helpers.js";
import Core from "./core.js";
import Modals from "./modals.js";
import Offcanvas from "./offcanvas.js";
import '../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables.js';
//import '../../vendor/asantibanez/livewire-charts/resources/js/app.js';

window.Alpine = Alpine
window.bootstrap = bootstrap;

(function() {
    docReady(function () {
        (new Core).init();
        (new Modals).init();
        (new Offcanvas).init();
        Livewire.start();
    });
})();

