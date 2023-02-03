import './bootstrap';
import ProxyHandler from "./main/ProxyHandler";
import ZenonHub from "./main/ZenonHub";
//import Alpine from 'alpinejs';

((window) => {
    window.zenonHub = new Proxy(
        new ZenonHub(),
        ProxyHandler,
    );
})(window);
