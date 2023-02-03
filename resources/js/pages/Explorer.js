if (window.zenonHub === undefined) {
    throw new Error('ZenonHub must be loaded in order to use the explorer page.');
}

export default class Explorer extends window.zenonHub.Singleton {

    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {
        this.attachHandlers();
    }

    attachHandlers() {
        window.livewire.on('urlChanged', (url) => this.urlChangeHandler(url));

        window.addEventListener("popstate", (event) => this.nextPrevHandler());
    }

    urlChangeHandler(url) {
        history.pushState(null, null, url);
    }

    nextPrevHandler() {
        if (window.document.querySelectorAll('.momentum-paginator').length) {
            let hash = window.location.pathname.split('/').reverse()[0];
            window.livewire.emit('momentumChanged', hash);
        }

        if (window.document.querySelectorAll('.transaction-paginator').length) {
            let hash = window.location.pathname.split('/').reverse()[0];
            window.livewire.emit('transactionChanged', hash);
        }
    }
}

((ZenonHub) => {
    ZenonHub.addPlugin('explorer', Explorer);
})(window.zenonHub);
