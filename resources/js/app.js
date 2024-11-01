import './bootstrap';
import ProxyHandler from "./main/ProxyHandler";
import ZenonHub from "./main/ZenonHub";

((window) => {
    window.zenonHub = new Proxy(
        new ZenonHub(),
        ProxyHandler,
    );
})(window);
