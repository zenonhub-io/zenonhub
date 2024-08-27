import './bootstrap';
import ProxyHandler from "./main/ProxyHandler";
import ZenonHub from "./main/ZenonHub";

import '../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables.js';

((window) => {
    window.zenonHub = new Proxy(
        new ZenonHub(),
        ProxyHandler,
    );
})(window);
