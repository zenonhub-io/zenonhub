export default class Explorer {

    init() {
        this.initListeners();
        this.attachEvents();
    }

    initListeners() {}

    attachEvents() {}

    // attachHandlers() {
    //     window.livewire.on('urlChanged', (url) => this.urlChangeHandler(url));
    //
    //     window.addEventListener("popstate", (event) => this.nextPrevHandler());
    // }
    //
    // urlChangeHandler(url) {
    //     history.pushState(null, null, url);
    // }
    //
    // nextPrevHandler() {
    //     if (window.document.querySelectorAll('.momentum-paginator').length) {
    //         let hash = window.location.pathname.split('/').reverse()[0];
    //         window.livewire.emit('momentumChanged', hash);
    //     }
    //
    //     if (window.document.querySelectorAll('.transaction-paginator').length) {
    //         let hash = window.location.pathname.split('/').reverse()[0];
    //         window.livewire.emit('transactionChanged', hash);
    //     }
    // }
}
